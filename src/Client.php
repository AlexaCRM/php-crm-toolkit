<?php
/**
 * Copyright (c) 2016 AlexaCRM.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Lesser Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace AlexaCRM\CRMToolkit;

use AlexaCRM\CRMToolkit\Auth\Authentication;
use AlexaCRM\CRMToolkit\Auth\Federation;
use AlexaCRM\CRMToolkit\Auth\OnlineFederation;
use BadMethodCallException;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use AlexaCRM\CRMToolkit\Entity\MetadataCollection;
use Exception;
use SimpleXMLElement;
use SoapFault;
use stdClass;

/**
 * This class creates and manages SOAP connections to a Microsoft Dynamics CRM server
 */
class Client extends AbstractClient {

    /**
     * Object of authentication class
     *
     * @var Authentication
     */
    private $authentication;

    /**
     * Object of settings class
     *
     * @var Settings
     */
    private $settings;

    /**
     * Unique name of organization to connect
     *
     * @var string
     */
    private $organizationUniqueName;

    /**
     * Organization Service url
     * This refers to the organization that you specify in the URL when you access the web application.
     * For example, for Contoso.crm.dynamics.com, the OrganizationName is Contoso.
     * ServerName refers to the name of the server, including the port number, for example, myserver or myserver:5555.
     *
     * @var string
     */
    private $organizationUrl;

    /**
     * Discovery service url
     * The IDiscoveryService web service provides information about the organizations available
     * on the Microsoft Dynamics CRM server using the SOAP protocol. This information includes
     * the web address (URL) for each organization.
     *
     * @var string
     */
    private $discoveryUrl;

    /**
     * @var SoapActions
     */
    private $soapActions;

    /**
     * @ignore
     */
    private $organizationDOM;

    /**
     * @ignore
     */
    private $organizationSecurityPolicy;

    /* Security Details */
    private $security = array();

    /* Cached Discovery data */
    private $discoveryDOM;

    private $discoverySecurityPolicy;

    /**
     * Connection timeout for CURLOPT_TIMEOUT
     *
     * @var integer $connectorTimeout time in seconds for waiting the response from Dynamics CRM web service
     */
    protected static $connectorTimeout = 300;

    /**
     * Maximum record to retrieve
     *
     * @var int
     */
    protected static $maximumRecords = self::MAX_CRM_RECORDS;

    /**
     * Volatile entity cache
     *
     * @var Entity[]
     */
    protected static $entityCache = [];

    /**
     * Stores a map of LogicalName => {Key,...} associations for cached records
     *
     * @var array
     */
    protected static $entityCacheRefs = [];

    /**
     * @var CacheInterface
     */
    public $cache;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * Create a new instance of the AlexaCRM\CRMToolkit\AlexaSDK
     *
     * @param Settings $settings
     * @param CacheInterface $cache
     * @param LoggerInterface $logger
     *
     * @throws Exception
     */
    function __construct( Settings $settings, CacheInterface $cache = null, LoggerInterface $logger = null ) {
        try {
            // Create settings object
            $this->settings = $settings;

            // Inject CacheInterface implementation
            $this->cache = $cache;

            // Inject LoggerInterface implementation
            $this->logger = $logger;

            /* If either mandatory parameter is NULL, throw an Exception */
            if ( !$this->checkConnectionSettings() ) {
                switch ( $this->settings->authMode ) {
                    case "OnlineFederation":
                        throw new BadMethodCallException( get_class( $this ) . ' constructor requires Username and Password' );
                    case "Federation":
                        throw new BadMethodCallException( get_class( $this ) . ' constructor requires the Discovery URI, Username and Password' );
                }
            }
            /* Create authentication class to connect to CRM Online or Internet facing deployment via ADFS */
            switch ( $this->settings->authMode ) {
                case "OnlineFederation":
                    $this->authentication = new OnlineFederation( $this->settings, $this );
                    break;
                case "Federation":
                    $this->settings->loginUrl = $this->getFederationSecurityURI( 'organization' );
                    $this->authentication     = new Federation( $this->settings, $this );
                    break;
            }
            $this->soapActions = new SoapActions( $this );

            if ( !$this->settings->hasOrganizationData() ) {
                $organizationDetails                    = $this->retrieveOrganization( $this->settings->serverUrl );
                $this->settings->organizationId         = $organizationDetails['OrganizationId'];
                $this->settings->organizationName       = $organizationDetails['FriendlyName'];
                $this->settings->organizationUniqueName = $organizationDetails['UniqueName'];
                $this->settings->organizationVersion    = $organizationDetails['OrganizationVersion'];
            }

            /* Initialize the entity metadata instance */
            MetadataCollection::instance( $this );
        } catch ( Exception $e ) {
            $this->logger->critical( 'Caught an exception while initializing PHP CRM Toolkit.', [ 'exception' => $e ] );
            throw $e;
        }
    }

    public function __get( $name ) {
        switch ( strtolower( $name ) ) {
            case "organizationversion":
                return $this->settings->organizationVersion;
            case "settings":
                return $this->settings;
        }
    }

    /**
     * Return the Authentication Mode used by the Discovery service
     *
     * @return mixed string if one auth type, array if there is multiple authentication types
     * @throws Exception
     * @ignore
     */
    protected function getDiscoveryAuthenticationMode() {
        try {
            /* If it's set, return the details from the Security array */
            if ( isset( $this->settings->authMode ) ) {
                return $this->settings->authMode;
            }
            /* Get the Discovery DOM */
            $discoveryDOM = $this->getDiscoveryDOM();
            /* Get the Security Policy for the Organization Service from the WSDL */
            $this->discoverySecurityPolicy = self::findSecurityPolicy( $discoveryDOM, 'DiscoveryService' );
            /* Check the Authentication node existence */
            if ( $this->discoverySecurityPolicy->getElementsByTagName( 'Authentication' )->length == 0 ) {
                throw new Exception( 'Could not find Authentication tag in provided Discovery Security policy XML' );
            }
            /* Find the Authentication type used */
            $authMode = array();
            if ( $this->discoverySecurityPolicy->getElementsByTagName( 'Authentication' )->length > 1 ) {
                foreach ( $this->discoverySecurityPolicy->getElementsByTagName( 'Authentication' ) as $authentication ) {
                    array_push( $authMode, $authentication->textContent );
                }
            } else {
                array_push( $authMode, $this->discoverySecurityPolicy->getElementsByTagName( 'Authentication' )->item( 0 )->textContent );
            }

            /* Return authType array */

            return $authMode;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Fetch and flatten the Discovery Service WSDL as a DOM
     *
     * @ignore
     */
    public function getDiscoveryDOM() {
        try {
            /* If it's already been fetched, use the one we have */
            if ( $this->discoveryDOM != null ) {
                return $this->discoveryDOM;
            }
            /* Fetch the WSDL for the Discovery Service as a parseable DOM Document */
            Logger::log( 'Getting Discovery DOM WSDL data from: ' . $this->settings->discoveryUrl . '?wsdl' );

            $discoveryDOM = new DOMDocument();

            $wsdlCurl = curl_init( $this->settings->discoveryUrl . '?wsdl' );
            curl_setopt( $wsdlCurl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $wsdlCurl, CURLOPT_CONNECTTIMEOUT, self::$connectorTimeout );
            curl_setopt( $wsdlCurl, CURLOPT_TIMEOUT, self::$connectorTimeout );

            if ( $this->settings->ignoreSslErrors ) {
                curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYPEER, 0 );
                curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYHOST, 0 );
            }

            $importXML = curl_exec( $wsdlCurl );
            curl_close( $wsdlCurl );

            $discoveryDOM->loadXML( $importXML );

            /* Flatten the WSDL and include all the Imports */
            $this->mergeWSDLImports( $discoveryDOM );
            /* Cache the DOM in the current object */
            $this->discoveryDOM = $discoveryDOM;

            return $discoveryDOM;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Return the Authentication Address used by the Discovery service
     *
     * @ignore
     */
    protected function getDiscoveryAuthenticationAddress() {
        try {
            /* If it's set, return the details from the Security array */
            if ( isset( $this->security['discovery_authuri'] ) ) {
                return $this->security['discovery_authuri'];
            }
            /* If we don't already have a Security Policy, get it */
            if ( $this->discoverySecurityPolicy == null ) {
                /* Get the Discovery DOM */
                $discoveryDOM = $this->getDiscoveryDOM();
                /* Get the Security Policy for the Organization Service from the WSDL */
                $this->discoverySecurityPolicy = self::findSecurityPolicy( $discoveryDOM, 'DiscoveryService' );
            }

            $authAddress = self::getSecurityAddress( $this->discoverySecurityPolicy, $this->security['discovery_authmode'] );

            return $authAddress;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Return the Authentication Address used by the Organization service
     *
     * @ignore
     */
    public function getOrganizationAuthenticationAddress() {
        try {
            /* If it's set, return the details from the Security array */
            if ( isset( $this->security['organization_authuri'] ) ) {
                return $this->security['organization_authuri'];
            }

            /* If we don't already have a Security Policy, get it */
            if ( $this->organizationSecurityPolicy == null ) {
                /* Get the Organization DOM */
                $organizationDOM = $this->getOrganizationDOM();
                /* Get the Security Policy for the Organization Service from the WSDL */
                $this->organizationSecurityPolicy = self::findSecurityPolicy( $organizationDOM, 'OrganizationService' );
            }
            /* Find the Authentication type used */
            $this->security['organization_authuri'] = self::getSecurityAddress( $this->organizationSecurityPolicy, 'Federation' );

            return $this->security['organization_authuri'];
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Return the Authentication Mode used by the Organization service
     *
     * @ignore
     */
    public function getOrganizationAuthenticationMode() {
        try {
            /* If it's set, return the details from the Security array */
            if ( isset( $this->security['organization_authmode'] ) ) {
                return $this->security['organization_authmode'];
            }

            /* Get the Organization DOM */
            $organizationDOM = $this->getOrganizationDOM();
            /* Get the Security Policy for the Organization Service from the WSDL */
            $this->organizationSecurityPolicy = self::findSecurityPolicy( $organizationDOM, 'OrganizationService' );
            /* Find the Authentication type used */
            $authType = $this->organizationSecurityPolicy->getElementsByTagName( 'Authentication' )->item( 0 )->textContent;

            return $authType;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Search for the security address
     *
     * @param DOMNode $securityPolicyNode
     * @param string $authMode
     *
     * @return string
     * @throws Exception
     */
    protected static function getSecurityAddress( DOMNode $securityPolicyNode, $authMode ) {
        if ( $authMode === 'OnlineFederation' ) {
            $tokenElementName = 'SignedSupportingTokens';
        } elseif ( $authMode === 'Federation' ) {
            $tokenElementName = 'EndorsingSupportingTokens';
        } else {
            throw new \InvalidArgumentException( 'Authentication mode "' . $authMode . '" is not supported' );
        }

        try {
            $securityURL = null;

            /* Find the SignedSupportingTokens tag */
            if ( $securityPolicyNode->getElementsByTagName( $tokenElementName )->length == 0 ) {
                throw new Exception( 'Could not find ' . $tokenElementName . ' tag in provided security policy XML' );
            }
            $estNode = $securityPolicyNode->getElementsByTagName( $tokenElementName )->item( 0 );

            /* Find the Policy tag */
            if ( $estNode->getElementsByTagName( 'Policy' )->length == 0 ) {
                throw new Exception( 'Could not find ' . $tokenElementName . '/Policy tag in provided security policy XML' );
            }
            $estPolicyNode = $estNode->getElementsByTagName( 'Policy' )->item( 0 );
            /* Find the IssuedToken tag */
            if ( $estPolicyNode->getElementsByTagName( 'IssuedToken' )->length == 0 ) {
                throw new Exception( 'Could not find ' . $tokenElementName . '/Policy/IssuedToken tag in provided security policy XML' );
            }
            $issuedTokenNode = $estPolicyNode->getElementsByTagName( 'IssuedToken' )->item( 0 );
            /* Find the Issuer tag */
            if ( $issuedTokenNode->getElementsByTagName( 'Issuer' )->length == 0 ) {
                throw new Exception( 'Could not find ' . $tokenElementName . '/Policy/IssuedToken/Issuer tag in provided security policy XML' );
            }
            $issuerNode = $issuedTokenNode->getElementsByTagName( 'Issuer' )->item( 0 );
            /* Find the Metadata tag */
            if ( $issuerNode->getElementsByTagName( 'Metadata' )->length == 0 ) {
                throw new Exception( 'Could not find ' . $tokenElementName . '/Policy/IssuedToken/Issuer/Metadata tag in provided security policy XML' );
            }

            $metadataNode = $issuerNode->getElementsByTagName( 'Metadata' )->item( 0 );
            /* Find the Address tag */
            if ( $metadataNode->getElementsByTagName( 'Address' )->length == 0 ) {
                throw new Exception( 'Could not find ' . $tokenElementName . '/Policy/IssuedToken/Issuer/Metadata/.../Address tag in provided security policy XML' );
            }
            $addressNode = $metadataNode->getElementsByTagName( 'Address' )->item( 0 );

            /* Get the URI */
            $securityURL = $addressNode->textContent;
            if ( $securityURL == null ) {
                throw new Exception( 'Could not find Security URL in provided security policy WSDL' );
            }

            return $securityURL;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Get the Trust Address for the Trust13UsernameMixed authentication method
     *
     * @ignore
     */
    protected static function getTrust13UsernameAddress( DOMDocument $authenticationDOM ) {
        return self::getTrustAddress( $authenticationDOM, 'UserNameWSTrustBinding_IWSTrust13Async' );
    }

    /**
     * Search the WSDL from an ADFS server to find the correct end-point for a
     * call to RequestSecurityToken with a given set of parmameters
     *
     * @ignore
     */
    protected static function getTrustAddress( DOMDocument $authenticationDOM, $trustName ) {
        try {
            /* Search the available Ports on the WSDL */
            $trustAuthNode = null;
            foreach ( $authenticationDOM->getElementsByTagName( 'port' ) as $portNode ) {
                if ( $portNode->hasAttribute( 'name' ) && $portNode->getAttribute( 'name' ) == $trustName ) {
                    $trustAuthNode = $portNode;
                    break;
                }
            }
            if ( $trustAuthNode == null ) {
                throw new Exception( 'Could not find Port for trust type <' . $trustName . '> in provided WSDL' );
            }
            /* Get the Address from the Port */
            $authenticationURI = null;
            if ( $trustAuthNode->getElementsByTagName( 'address' )->length > 0 ) {
                $authenticationURI = $trustAuthNode->getElementsByTagName( 'address' )->item( 0 )->getAttribute( 'location' );
            }
            if ( $authenticationURI == null ) {
                throw new Exception( 'Could not find Address for trust type <' . $trustName . '> in provided WSDL' );
            }

            /* Return the found URI */

            return $authenticationURI;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Search a WSDL XML DOM for "import" tags and import the files into
     * one large DOM for the entire WSDL structure
     *
     * @ignore
     */
    protected function mergeWSDLImports( DOMNode &$wsdlDOM, $continued = false, DOMDocument &$newRootDocument = null ) {
        try {
            static $rootNode = null;
            static $rootDocument = null;
            /* If this is an external call, find the "root" defintions node */
            if ( $continued == false ) {
                $rootNode     = $wsdlDOM->getElementsByTagName( 'definitions' )->item( 0 );
                $rootDocument = $wsdlDOM;
            }
            if ( $newRootDocument == null ) {
                $newRootDocument = $rootDocument;
            }
            Logger::log( "Processing Node: " . $wsdlDOM->nodeName . " which has " . $wsdlDOM->childNodes->length . " child nodes" );
            $nodesToRemove = Array();
            /* Loop through the Child nodes of the provided DOM */
            foreach ( $wsdlDOM->childNodes as $childNode ) {
                Logger::log( "\tProcessing Child Node: " . $childNode->nodeName . " " . ( isset( $childNode->localName ) ? "(" . $childNode->localName . "). " : "" ) . ( ( isset( $childNode->childNodes ) && $childNode->childNodes ) ? "which has " . $childNode->childNodes->length . " child nodes" . PHP_EOL : "" ) );
                /* If this child is an IMPORT node, get the referenced WSDL, and remove the Import */
                if ( $childNode->localName == 'import' ) {
                    /* Get the location of the imported WSDL */
                    if ( $childNode->hasAttribute( 'location' ) ) {
                        $importURI = $childNode->getAttribute( 'location' );
                    } else if ( $childNode->hasAttribute( 'schemaLocation' ) ) {
                        $importURI = $childNode->getAttribute( 'schemaLocation' );
                    } else {
                        $importURI = null;
                    }
                    /* Only import if we found a URI - otherwise, don't change it! */
                    if ( $importURI != null ) {
                        Logger::log( "\tImporting data from: " . $importURI );
                        $importDOM = new DOMDocument();

                        $wsdlCurl = curl_init( $importURI );
                        curl_setopt( $wsdlCurl, CURLOPT_RETURNTRANSFER, 1 );
                        curl_setopt( $wsdlCurl, CURLOPT_CONNECTTIMEOUT, self::$connectorTimeout );
                        curl_setopt( $wsdlCurl, CURLOPT_TIMEOUT, self::$connectorTimeout );

                        if ( $this->settings->ignoreSslErrors ) {
                            curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYPEER, 0 );
                            curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYHOST, 0 );
                        }

                        $importXML = curl_exec( $wsdlCurl );
                        curl_close( $wsdlCurl );

                        $importDOM->loadXML( $importXML );

                        /* Find the "Definitions" on this imported node */
                        $importDefinitions = $importDOM->getElementsByTagName( 'definitions' )->item( 0 );
                        /* If we have "Definitions", import them one by one - Otherwise, just import at this level */
                        if ( $importDefinitions != null ) {
                            /* Add all the attributes (namespace definitions) to the root definitions node */
                            foreach ( $importDefinitions->attributes as $attribute ) {
                                /* Don't copy the "TargetNamespace" attribute */
                                if ( $attribute->name != 'targetNamespace' ) {
                                    $rootNode->setAttributeNode( $attribute );
                                }
                            }
                            $this->mergeWSDLImports( $importDefinitions, true, $importDOM );
                            foreach ( $importDefinitions->childNodes as $importNode ) {
                                if ( isset( $importNode ) && $importNode ) {
                                    Logger::log( "\t\tInserting Child: " . $importNode->C14N( true ) );
                                }
                                $importNode = $newRootDocument->importNode( $importNode, true );
                                $wsdlDOM->insertBefore( $importNode, $childNode );
                            }
                        } else {
                            $importNode = $newRootDocument->importNode( $importDOM->firstChild, true );
                            $wsdlDOM->insertBefore( $importNode, $childNode );
                        }
                        Logger::log( "\t\tRemoving Child: " . $childNode->C14N( true ) );
                        $nodesToRemove[] = $childNode;
                    }
                } else {
                    Logger::log( 'Preserving node: ' . $childNode->localName );
                    if ( $childNode->hasChildNodes() ) {
                        $this->mergeWSDLImports( $childNode, true );
                    }
                }
            }
            /* Actually remove the nodes (not done in the loop, as it messes up the ForEach pointer!) */
            foreach ( $nodesToRemove as $node ) {
                $wsdlDOM->removeChild( $node );
            }

            return $wsdlDOM;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Parse the results of a RetrieveEntity into a useable PHP object
     *
     * @ignore
     */
    protected static function parseRetrieveEntityResponse( $soapResponse ) {
        try {
            /* Load the XML into a DOMDocument */
            $soapResponseDOM = new DOMDocument();
            $soapResponseDOM->loadXML( $soapResponse );
            /* Find the ExecuteResult node with Type b:RetrieveRecordChangeHistoryResponse */
            $executeResultNode = null;
            foreach ( $soapResponseDOM->getElementsByTagName( 'ExecuteResult' ) as $node ) {
                if ( $node->hasAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'type' ) && self::stripNS( $node->getAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'type' ) ) == 'RetrieveEntityResponse' ) {
                    $executeResultNode = $node;
                    break;
                }
            }
            unset( $node );
            if ( $executeResultNode == null ) {
                throw new Exception( 'Could not find ExecuteResult for RetrieveEntityResponse in XML provided' );
            }
            /* Find the Value node with Type d:AlexaCRM\CRMToolkit\Entity\EntityMetadata */
            $entityMetadataNode = null;
            foreach ( $executeResultNode->getElementsByTagName( 'value' ) as $node ) {
                if ( $node->hasAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'type' ) && self::stripNS( $node->getAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'type' ) ) == 'EntityMetadata' ) {
                    $entityMetadataNode = $node;
                    break;
                }
            }
            if ( $entityMetadataNode == null ) {
                throw new Exception( 'Could not find returned EntityMetadata in XML provided' );
            }

            /* Assemble a simpleXML class for the details to return */

            $nodeXML = $entityMetadataNode->ownerDocument->saveXML( $entityMetadataNode );

            // remove XML namespaces
            $nodeXMLWithoutNS = preg_replace( '/(<)([a-z]:)/', '<', preg_replace( '/(<\/)([a-z]:)/', '</', $nodeXML ) );
            $nodeXMLWithoutNS = preg_replace( '~([\s"])[a-z]:([a-zA-Z]+)~', '$1$2', $nodeXMLWithoutNS );

            $entityMetadataElement = simplexml_load_string( $nodeXMLWithoutNS );

            if ( !$entityMetadataElement ) {
                throw new Exception( 'Unable to load metadata simple_xml_class' );
            }

            /* Return the SimpleXML object */

            return $entityMetadataElement;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Parse the results of a RetrieveMultipleRequest into a usable PHP object
     *
     * @param String $soapResponse
     *
     * @return SimpleXMLElement[]
     * @throws Exception
     * @internal param bool $simpleMode
     * @ignore
     */
    protected static function parseRetrieveAllEntitiesResponse( $soapResponse ) {
        try {
            /* Load the XML into a DOMDocument */
            $soapResponseDOM = new DOMDocument();
            $soapResponseDOM->loadXML( $soapResponse );
            /**
             * Find the RetrieveMultipleResponse
             *
             * @var $retrieveMultipleResponseNode DOMElement
             */
            $retrieveMultipleResponseNode = null;
            foreach ( $soapResponseDOM->getElementsByTagName( 'ExecuteResponse' ) as $node ) {
                $retrieveMultipleResponseNode = $node;
                break;
            }
            unset( $node );
            if ( $retrieveMultipleResponseNode == null ) {
                throw new Exception( 'Could not find ExecuteResponse node in XML provided' );
            }
            /**
             * Find the RetrieveMultipleResult node
             *
             * @var $retrieveMultipleResultNode DOMElement
             */
            $retrieveMultipleResultNode = null;
            foreach ( $retrieveMultipleResponseNode->getElementsByTagName( 'Results' ) as $node ) {
                $retrieveMultipleResultNode = $node;
                break;
            }
            unset( $node );
            if ( $retrieveMultipleResultNode == null ) {
                throw new Exception( 'Could not find ExecuteResult node in XML provided' );
            }
            /* Assemble an associative array for the details to return */
            $responseDataArray = array();

            /* Loop through the Entities returned */
            foreach ( $retrieveMultipleResultNode->getElementsByTagName( 'EntityMetadata' ) as $entityNode ) {
                /**
                 * @var $entityNode DOMElement
                 */
                if ( $entityNode->getElementsByTagName( "IsValidForAdvancedFind" )->item( 0 )->textContent != "true" ) {
                    continue;
                }

                $nodeXML = $entityNode->ownerDocument->saveXML( $entityNode );

                // remove XML namespaces
                $nodeXMLWithoutNS = preg_replace( '/(<)([a-z]:)/', '<', preg_replace( '/(<\/)([a-z]:)/', '</', $nodeXML ) );
                $nodeXMLWithoutNS = preg_replace( '~([\s"])[a-z]:([a-zA-Z]+)~', '$1$2', $nodeXMLWithoutNS );

                $entityMetadataElement = simplexml_load_string( $nodeXMLWithoutNS );
                array_push( $responseDataArray, $entityMetadataElement );
            }

            return $responseDataArray;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Get the SOAP Endpoint for the Federation Security service
     *
     * @ignore
     */
    public function getFederationSecurityURI( $service ) {
        try {
            $securityEndpointKey = $service . '_authendpoint';
            /* If it's set, return the details from the Security array */
            if ( isset( $this->security[ $securityEndpointKey ] ) ) {
                return $this->security[ $securityEndpointKey ];
            }

            if ( $service === 'organization' ) {
                $authUri = $this->getOrganizationAuthenticationAddress();
            } elseif ( $service === 'discovery' ) {
                $authUri = $this->getDiscoveryAuthenticationAddress();
            } else {
                $authUri = $this->security[ $service . '_authuri' ];
            }

            /* Fetch the WSDL for the Authentication Service as a parseable DOM Document */
            Logger::log( 'Getting WSDL data for Federation Security URI from: ' . $authUri );
            $authenticationDOM = new DOMDocument();

            $wsdlCurl = curl_init( $authUri );
            curl_setopt( $wsdlCurl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $wsdlCurl, CURLOPT_CONNECTTIMEOUT, self::$connectorTimeout );
            curl_setopt( $wsdlCurl, CURLOPT_TIMEOUT, self::$connectorTimeout );

            if ( $this->settings->ignoreSslErrors ) {
                curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYPEER, 0 );
                curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYHOST, 0 );
            }

            $importXML = curl_exec( $wsdlCurl );
            curl_close( $wsdlCurl );

            $authenticationDOM->loadXML( $importXML );
            /* Flatten the WSDL and include all the Imports */
            $this->mergeWSDLImports( $authenticationDOM );

            // Note: Find the real end-point to use for my security request - for now, we hard-code to Trust13 Username & Password using known values
            // See http://code.google.com/p/php-dynamics-crm-2011/issues/detail?id=4
            $authEndpoint = self::getTrust13UsernameAddress( $authenticationDOM );

            $this->security[ $securityEndpointKey ] = $authEndpoint;

            return $authEndpoint;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Get the SOAP Endpoint for the OnlineFederation Security service
     *
     * @ignore
     */
    protected function getOnlineFederationSecurityURI( $service ) {
        try {
            /* If it's set, return the details from the Security array */
            if ( isset( $this->security[ $service . '_authendpoint' ] ) ) {
                return $this->security[ $service . '_authendpoint' ];
            }

            /* Fetch the WSDL for the Authentication Service as a parseable DOM Document */
            Logger::log( 'Getting WSDL data for OnlineFederation Security URI from: ' . $this->security[ $service . '_authuri' ] );
            $authenticationDOM = new DOMDocument();

            $wsdlCurl = curl_init( $this->security[ $service . '_authuri' ] );
            curl_setopt( $wsdlCurl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $wsdlCurl, CURLOPT_CONNECTTIMEOUT, self::$connectorTimeout );
            curl_setopt( $wsdlCurl, CURLOPT_TIMEOUT, self::$connectorTimeout );

            if ( $this->settings->ignoreSslErrors ) {
                curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYPEER, 0 );
                curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYHOST, 0 );
            }

            $importXML = curl_exec( $wsdlCurl );
            curl_close( $wsdlCurl );

            $authenticationDOM->loadXML( $importXML );
            /* Flatten the WSDL and include all the Imports */
            $this->mergeWSDLImports( $authenticationDOM );

            $authEndpoint = self::getLoginOnmicrosoftAddress( $authenticationDOM ); //FIXME

            return $authEndpoint;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Search a Microsoft Dynamics CRM 2011 WSDL for the Security Policy for a given Service
     *
     * @ignore
     *
     * @param DOMDocument $wsdlDocument
     * @param string $serviceName
     *
     * @return DOMElement
     * @throws Exception
     */
    protected static function findSecurityPolicy( DOMDocument $wsdlDocument, $serviceName ) {
        try {
            /* Find the selected Service definition from the WSDL */
            $selectedServiceNode = null;

            foreach ( $wsdlDocument->getElementsByTagName( 'service' ) as $serviceNode ) {
                if ( $serviceNode->hasAttribute( 'name' ) && $serviceNode->getAttribute( 'name' ) == $serviceName ) {
                    $selectedServiceNode = $serviceNode;
                    break;
                }
            }
            if ( $selectedServiceNode == null ) {
                throw new Exception( 'Could not find definition of Service <' . $serviceName . '> in provided WSDL' );
            }
            /* Now find the Binding for the Service */
            $bindingName = null;
            foreach ( $selectedServiceNode->getElementsByTagName( 'port' ) as $portNode ) {
                if ( $portNode->hasAttribute( 'name' ) ) {
                    $bindingName = $portNode->getAttribute( 'name' );
                    break;
                }
            }
            if ( $bindingName == null ) {
                throw new Exception( 'Could not find binding for Service <' . $serviceName . '> in provided WSDL' );
            }
            /* Find the Binding definition from the WSDL */
            $bindingNode = null;
            foreach ( $wsdlDocument->getElementsByTagName( 'binding' ) as $bindingNode ) {
                if ( $bindingNode->hasAttribute( 'name' ) && $bindingNode->getAttribute( 'name' ) == $bindingName ) {
                    break;
                }
            }
            if ( $bindingNode == null ) {
                throw new Exception( 'Could not find definition of Binding <' . $bindingName . '> in provided WSDL' );
            }
            /* Find the Policy Reference */
            $policyReferenceURI = null;
            foreach ( $bindingNode->getElementsByTagName( 'PolicyReference' ) as $policyReferenceNode ) {
                if ( $policyReferenceNode->hasAttribute( 'URI' ) ) {
                    /* Strip the leading # from the PolicyReferenceURI to get the ID */
                    $policyReferenceURI = substr( $policyReferenceNode->getAttribute( 'URI' ), 1 );
                    break;
                }
            }
            if ( $policyReferenceURI == null ) {
                throw new Exception( 'Could not find Policy Reference for Binding <' . $bindingName . '> in provided WSDL' );
            }
            /**
             * Find the Security Policy from the WSDL
             *
             * @var DOMElement $securityPolicyNode
             */
            $securityPolicyNode = null;
            foreach ( $wsdlDocument->getElementsByTagName( 'Policy' ) as $policyNode ) {
                if ( $policyNode->hasAttribute( 'wsu:Id' ) && $policyNode->getAttribute( 'wsu:Id' ) == $policyReferenceURI ) {
                    $securityPolicyNode = $policyNode;
                    break;
                }
            }
            if ( $securityPolicyNode == null ) {
                throw new Exception( 'Could not find Policy with ID <' . $policyReferenceURI . '> in provided WSDL' );
            }

            /* Return the selected node */

            return $securityPolicyNode;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Fetch and flatten the Organization Service WSDL as a DOM
     *
     * @ignore
     */
    public function getOrganizationDOM() {
        try {
            /* If it's already been fetched, use the one we have */
            if ( $this->organizationDOM != null ) {
                return $this->organizationDOM;
            }
            if ( $this->settings->organizationUrl == null ) {
                throw new Exception( 'Cannot get Organization DOM before determining Organization URI' );
            }

            /* Fetch the WSDL for the Organization Service as a parseable DOM Document */
            Logger::log( 'Getting WSDL data for Organization DOM from: ' . $this->settings->organizationUrl . '?wsdl' );
            $organizationDOM = new DOMDocument();
            $wsdlCurl = curl_init( $this->settings->organizationUrl . '?wsdl' );
            curl_setopt( $wsdlCurl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $wsdlCurl, CURLOPT_CONNECTTIMEOUT, self::$connectorTimeout );
            curl_setopt( $wsdlCurl, CURLOPT_TIMEOUT, self::$connectorTimeout );

            if ( $this->settings->ignoreSslErrors ) {
                curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYPEER, 0 );
                curl_setopt( $wsdlCurl, CURLOPT_SSL_VERIFYHOST, 0 );
            }

            $importXML = curl_exec( $wsdlCurl );
            curl_close( $wsdlCurl );
            $organizationDOM->loadXML( $importXML );
            /* Flatten the WSDL and include all the Imports */
            $this->mergeWSDLImports( $organizationDOM );

            /* Cache the DOM in the current object */
            $this->organizationDOM = $organizationDOM;

            return $organizationDOM;
        } catch ( Exception $e ) {
            Logger::log( "Exception", $e );
            throw $e;
        }
    }

    /**
     * Send a RetrieveEntity request to the Dynamics CRM 2011 server and return the results as a structured Object
     *
     * @param String $entityType the LogicalName of the Entity to be retrieved (Incident, Account etc.)
     * @param String $entityId the internal Id of the Entity to be retrieved (without enclosing brackets)
     * @param array $entityFilters array listing all fields to be fetched, or null to get all columns
     * @param Boolean $showUnpublished
     *
     * @return stdClass a PHP Object containing all the data retrieved.
     */
    public function retrieveEntity( $entityType, $entityId = null, $entityFilters = null, $showUnpublished = false ) {
        /* Get the raw XML data */
        $rawSoapResponse = $this->retrieveEntityRaw( $entityType, $entityId, $entityFilters, $showUnpublished );
        /* Parse the raw XML data into an Object */
        $soapData = self::parseRetrieveEntityResponse( $rawSoapResponse );

        /* Return the structured object */

        return $soapData;
    }

    /**
     * Send a RetrieveEntity request to the Dynamics CRM server and return the results as raw XML
     * This is particularly useful when debugging the responses from the server
     *
     * @param string $entityType the LogicalName of the Entity to be retrieved (Incident, Account etc.)
     *
     * @return string the raw XML returned by the server, including all SOAP Envelope, Header and Body data.
     */
    public function retrieveEntityRaw( $entityType, $entityId = null, $entityFilters = null, $showUnpublished = false ) {
        /* Send the sequrity request and get a security token */
        $securityToken = $this->authentication->getOrganizationSecurityToken();
        /* Generate the XML for the Body of a RetrieveEntity request */
        $executeNode = SoapRequestsGenerator::generateRetrieveEntityRequest( $entityType, $entityId, $entityFilters, $showUnpublished );
        /* Turn this into a SOAP request, and send it */
        $retrieveEntityRequest = $this->generateSoapRequest( $this->settings->organizationUrl, $this->soapActions->getSoapAction( 'organization', 'Execute' ), $securityToken, $executeNode );
        $soapResponse          = self::getSoapResponse( $this->settings->organizationUrl, $retrieveEntityRequest );

        return $soapResponse;
    }

    /**
     * Send a RetrieveMultipleEntities request to the Dynamics CRM server
     * and return the results as a structured Object
     * Each Entity returned is processed into an appropriate AlexaCRM\CRMToolkit\AlexaSDK_Entity object
     *
     * @param string $entityType logical name of entities to retrieve
     * @param boolean $allPages indicates if the query should be resent until all possible data is retrieved
     * @param string $pagingCookie if multiple pages are returned, send the paging cookie to get pages 2 and onwards.  Use NULL to get the first page.  Ignored if $allPages is specified.
     * @param integer $limitCount maximum number of records to be returned per page
     * @param boolean $simpleMode indicates if we should just use stdClass, instead of creating Entities
     *
     * @return stdClass a PHP Object containing all the data retrieved.
     */
    public function retrieveMultipleEntities( $entityType, $allPages = true, $pagingCookie = null, $limitCount = null, $pageNumber = null, $simpleMode = false ) {
        $queryXML = new DOMDocument();
        $fetch    = $queryXML->appendChild( $queryXML->createElement( 'fetch' ) );
        $fetch->setAttribute( 'version', '1.0' );
        $fetch->setAttribute( 'output-format', 'xml-platform' );
        $fetch->setAttribute( 'mapping', 'logical' );
        $fetch->setAttribute( 'distinct', 'false' );
        $entity = $fetch->appendChild( $queryXML->createElement( 'entity' ) );
        $entity->setAttribute( 'name', $entityType );
        $entity->appendChild( $queryXML->createElement( 'all-attributes' ) );

        $order = $entity->appendChild( $queryXML->createElement( 'order' ) );
        $order->setAttribute( 'attribute', MetadataCollection::instance( $this, $this->cache )->getEntityDefinition( $entityType )->primaryNameAttribute );
        $order->setAttribute( 'descending', 'false' );

        $queryXML->saveXML( $fetch );

        return $this->retrieveMultiple( $queryXML->C14N(), $allPages, $pagingCookie, $limitCount, $pageNumber, $simpleMode );
    }

    /**
     * Send a Retrieve request to the Dynamics CRM 2011 server and return the results as raw XML
     * This function is typically used just after creating something (where you get the ID back
     * as the return value), as it is more efficient to use RetrieveMultiple to search directly if
     * you don't already have the ID.
     * This is particularly useful when debugging the responses from the server
     *
     * @param Entity $entity the Entity to retrieve - must have an ID specified
     * @param array $columnSet array listing all fields to be fetched, or null to get all fields
     *
     * @return string the raw XML returned by the server, including all SOAP Envelope, Header and Body data.
     * @throws Exception
     */
    public function retrieveRaw( Entity $entity, $columnSet = null ) {
        /* Determine the Type & ID of the Entity */
        $entityType = $entity->LogicalName;
        /* Send the security request and get a security token */
        $securityToken = $this->authentication->getOrganizationSecurityToken();
        /* Check if entity have and ID */
        if ( $entity->ID != self::EmptyGUID ) {
            $entityId    = $entity->ID;
            $executeNode = SoapRequestsGenerator::generateRetrieveRequest( $entityType, $entityId, $columnSet );
            $action      = "Retrieve";
        } else if ( $entity->keyAttributes ) {
            $executeNode = SoapRequestsGenerator::generateExecuteRetrieveRequest( $entityType, $entity->keyAttributes, $columnSet );
            $action      = "Execute";
        } else {
            /* Only allow "Retrieve" for an Entity with an ID */
            throw new Exception( 'Cannot Retrieve an Entity without an ID or KeyAttributes.' );
        }
        /* Turn this into a SOAP request, and send it */
        $retrieveRequest = $this->generateSoapRequest( $this->settings->organizationUrl, $this->soapActions->getSoapAction( 'organization', $action ), $securityToken, $executeNode );

        $soapResponse = self::getSoapResponse( $this->settings->organizationUrl, $retrieveRequest );

        return $soapResponse;
    }

    public function retrieveOrganizations() {
        /* Request a Security Token for the Discovery Service */
        $securityToken = $this->authentication->getDiscoverySecurityToken();
        /* Generate a Soap Request for the Retrieve Organization Request method of the Discovery Service */
        $discoverySoapRequest = $this->generateSoapRequest( $this->settings->discoveryUrl, $this->soapActions->getSoapAction( 'discovery', 'Execute' ), $securityToken, SoapRequestsGenerator::generateRetrieveOrganizationRequest() );

        $discovery_data = self::getSoapResponse( $this->settings->discoveryUrl, $discoverySoapRequest );

        $organizationDetails = array();
        $discoveryDOM        = new DOMDocument();
        $discoveryDOM->loadXML( $discovery_data );

        if ( $discoveryDOM->getElementsByTagName( 'OrganizationDetail' )->length > 0 ) {
            foreach ( $discoveryDOM->getElementsByTagName( 'OrganizationDetail' ) as $organizationNode ) {
                $organization = array();
                foreach ( $organizationNode->getElementsByTagName( 'Endpoints' )->item( 0 )->getElementsByTagName( 'KeyValuePairOfEndpointTypestringztYlk6OT' ) as $endpointDOM ) {
                    $organization["Endpoints"][ $endpointDOM->getElementsByTagName( 'key' )->item( 0 )->textContent ] = $endpointDOM->getElementsByTagName( 'value' )->item( 0 )->textContent;
                }

                if ( $organizationNode->getElementsByTagName( 'FriendlyName' )->length > 0 ) {
                    $organization["FriendlyName"] = $organizationNode->getElementsByTagName( 'FriendlyName' )->item( 0 )->textContent;
                }

                if ( $organizationNode->getElementsByTagName( 'OrganizationId' )->length > 0 ) {
                    $organization["OrganizationId"] = $organizationNode->getElementsByTagName( 'OrganizationId' )->item( 0 )->textContent;
                }

                if ( $organizationNode->getElementsByTagName( 'OrganizationVersion' )->length > 0 ) {
                    $organization["OrganizationVersion"] = $organizationNode->getElementsByTagName( 'OrganizationVersion' )->item( 0 )->textContent;
                }

                if ( $organizationNode->getElementsByTagName( 'State' )->length > 0 ) {
                    $organization["State"] = $organizationNode->getElementsByTagName( 'State' )->item( 0 )->textContent;
                }

                if ( $organizationNode->getElementsByTagName( 'UniqueName' )->length > 0 ) {
                    $organization["UniqueName"] = $organizationNode->getElementsByTagName( 'UniqueName' )->item( 0 )->textContent;
                }

                if ( $organizationNode->getElementsByTagName( 'UrlName' )->length > 0 ) {
                    $organization["UrlName"] = $organizationNode->getElementsByTagName( 'UrlName' )->item( 0 )->textContent;
                }

                array_push( $organizationDetails, $organization );
            }
        }

        return $organizationDetails;
    }

    public function retrieveOrganization( $webApplicationUrl ) {
        $organizationDetails = null;
        $parsedUrl           = parse_url( $webApplicationUrl );

        $organizations = $this->retrieveOrganizations();

        foreach ( $organizations as $organization ) {
            if ( substr_count( $organization["Endpoints"]["WebApplication"], $parsedUrl["host"] ) ) {
                $organizationDetails = $organization;
            }
        }

        return $organizationDetails;
    }

    /**
     * Create a new usable Dynamics CRM Entity object
     *
     * @param string $logicalName Entity logical name
     * @param string|KeyAttributes $id Entity record ID or key attribute
     * @param array $columnSet List of field values to retrieve
     *
     * @return Entity
     */
    public function entity( $logicalName, $id = null, $columnSet = null ) {
        if ( is_null( $id ) ) {
            return new Entity( $this, $logicalName, null, $columnSet );
        }

        if ( is_array( $columnSet ) && count( $columnSet ) ) {
            $emptyColumnSetCacheKey = Entity::generateCacheKey( $logicalName, $id );

            if ( array_key_exists( $emptyColumnSetCacheKey, static::$entityCache ) ) {
                /*
                 * If we have a record with all fields retrieved, return it instead of retrieving
                 * a limited amount of fields from the CRM.
                 */
                return static::$entityCache[$emptyColumnSetCacheKey];
            }
        }

        $cacheKey = Entity::generateCacheKey( $logicalName, $id, $columnSet );

        if ( !array_key_exists( $cacheKey, static::$entityCache ) ) {
            static::$entityCache[$cacheKey] = new Entity( $this, $logicalName, $id, $columnSet );
            static::$entityCacheRefs[$logicalName][] = $cacheKey;
        }

        return static::$entityCache[$cacheKey];
    }

    /**
     * Get the connector timeout value
     *
     * @return int the maximum time the connector will wait for a response from the CRM in seconds
     */
    public static function getConnectorTimeout() {
        return self::$connectorTimeout;
    }

    /**
     * Set the connector timeout value
     *
     * @param int $_connectorTimeout maximum time the connector will wait for a response from the CRM in seconds
     */
    public static function setConnectorTimeout( $_connectorTimeout ) {
        if ( !is_int( $_connectorTimeout ) ) {
            return;
        }
        self::$connectorTimeout = $_connectorTimeout;
    }

    /**
     * Get the Discovery URL which is currently in use
     *
     * @return string the URL of the Discovery Service
     */
    public function getDiscoveryURI() {
        return $this->discoveryURI;
    }

    /**
     * Get the Organization Unique Name which is currently in use
     *
     * @return string the Unique Name of the Organization
     */
    public function getOrganization() {
        return $this->organizationUniqueName;
    }

    /**
     * Get the maximum records for a query
     *
     * @return int the maximum records that will be returned from RetrieveMultiple per page
     */
    public static function getMaximumRecords() {
        return self::$maximumRecords;
    }

    /**
     * Set the maximum records for a query
     *
     * @param int $_maximumRecords the maximum number of records to fetch per page
     */
    public static function setMaximumRecords( $_maximumRecords ) {
        if ( !is_int( $_maximumRecords ) ) {
            return;
        }
        self::$maximumRecords = $_maximumRecords;
    }

    /**
     * SEE GetSOAPResponse
     *
     * @param $soapUrl
     * @param $content
     * @param $requestType
     *
     * @ignore
     * @return array $header Formatted headers
     */
    private static function formatHeaders( $soapUrl, $content, $requestType = "POST" ) {
        $scheme = parse_url( $soapUrl );
        /* Setup headers array */
        $headers = array(
            $requestType . " " . $scheme["path"] . " HTTP/1.1",
            "Host: " . $scheme["host"],
            'Connection: Keep-Alive',
            "Content-type: application/soap+xml; charset=UTF-8",
            "Content-length: " . strlen( $content ),
        );

        return $headers;
    }

    /**
     * Send the SOAP message, and get the response
     *
     * @param $soapUrl
     * @param $content
     * @param bool $throwException
     *
     * @return string response XML
     * @throws Exception
     * @throws NotAuthorizedException
     * @throws SoapFault
     */
    public static function getSoapResponse( $soapUrl, $content, $throwException = true ) {
        /* Format cUrl headers */
        $headers = self::formatHeaders( $soapUrl, $content );

        $cURLHandle = curl_init();
        curl_setopt( $cURLHandle, CURLOPT_URL, $soapUrl );
        curl_setopt( $cURLHandle, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $cURLHandle, CURLOPT_TIMEOUT, self::$connectorTimeout );
        curl_setopt( $cURLHandle, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $cURLHandle, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $cURLHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt( $cURLHandle, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $cURLHandle, CURLOPT_POST, 1 );
        curl_setopt( $cURLHandle, CURLOPT_POSTFIELDS, $content );
        curl_setopt( $cURLHandle, CURLOPT_HEADER, false );
        /* Execute the cURL request, get the XML response */
        $responseXML = curl_exec( $cURLHandle );
        /* Check for cURL errors */
        if ( curl_errno( $cURLHandle ) != CURLE_OK ) {
            throw new Exception( 'cURL Error: ' . curl_error( $cURLHandle ) );
        }
        /* Check for HTTP errors */
        $httpResponse = curl_getinfo( $cURLHandle, CURLINFO_HTTP_CODE );
        curl_close( $cURLHandle );
        /* Determine the Action in the SOAP Response */
        $responseDOM = new DOMDocument();
        $responseDOM->loadXML( $responseXML );
        /* Check we have a SOAP Envelope */
        if ( $responseDOM->getElementsByTagNameNS( 'http://www.w3.org/2003/05/soap-envelope', 'Envelope' )->length < 1 ) {
            throw new Exception( 'Invalid SOAP Response: HTTP Response ' . $httpResponse . PHP_EOL . $responseXML );
        }
        /* Authentication error */
        if ( $responseDOM->getElementsByTagNameNS( 'http://schemas.microsoft.com/Passport/SoapServices/SOAPFault', 'value' )->length > 0 ) {
            $errorCode = $responseDOM->getElementsByTagNameNS( 'http://schemas.microsoft.com/Passport/SoapServices/SOAPFault', 'value' )->item( 0 )->textContent;
            if ( $errorCode == "0x80048831" ) {
                throw new NotAuthorizedException( $errorCode, 'Not authorized.' );
            }
        }
        /* Check we have a SOAP Header */
        if ( $responseDOM->getElementsByTagNameNS( 'http://www.w3.org/2003/05/soap-envelope', 'Envelope' )->item( 0 )
                         ->getElementsByTagNameNS( 'http://www.w3.org/2003/05/soap-envelope', 'Header' )->length < 1
        ) {
            throw new Exception( 'Invalid SOAP Response: No SOAP Header! ' . PHP_EOL . $responseXML );
        }
        /* Get the SOAP Action */
        $actionString = $responseDOM->getElementsByTagNameNS( 'http://www.w3.org/2003/05/soap-envelope', 'Envelope' )->item( 0 )
                                    ->getElementsByTagNameNS( 'http://www.w3.org/2003/05/soap-envelope', 'Header' )->item( 0 )
                                    ->getElementsByTagNameNS( 'http://www.w3.org/2005/08/addressing', 'Action' )->item( 0 )->textContent;
        Logger::log( __FUNCTION__ . ': SOAP Action in returned XML is "' . $actionString . '"' );
        /* Handle known Error Actions */
        if ( in_array( $actionString, self::$SOAPFaultActions ) && $throwException ) {

            $q = new \DOMXPath( $responseDOM );
            $q->registerNamespace( 'c', 'http://schemas.microsoft.com/xrm/2011/Contracts' );

            $faultCode = $q->query( '/s:Envelope/s:Body/s:Fault/s:Detail/c:OrganizationServiceFault/c:ErrorCode' );
            if ( !$faultCode->length ) {
                $faultCode = $q->query( '/s:Envelope/s:Body/s:Fault/s:Code/s:Value' )->item( 0 )->nodeValue;
            } else {
                $faultCode = $faultCode->item( 0 )->nodeValue;
            }

            $faultString = $q->query( '/s:Envelope/s:Body/s:Fault/s:Reason/s:Text' )->item( 0 )->nodeValue;

            throw new SoapFault( (string) $faultCode, $faultString );
        }

        /* Return XML response string */

        return $responseXML;
    }

    /**
     * Create the XML String for a Soap Request
     *
     * @ignore
     */
    protected function generateSoapRequest( $serviceURI, $soapAction, $securityToken, DOMNode $bodyContentNode ) {
        $soapRequestDOM = new DOMDocument();
        $soapEnvelope   = $soapRequestDOM->appendChild( $soapRequestDOM->createElementNS( 'http://www.w3.org/2003/05/soap-envelope', 's:Envelope' ) );
        $soapEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://www.w3.org/2005/08/addressing' );
        $soapEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:u', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd' );
        /* Get the SOAP Header */
        $soapHeaderNode = $this->generateSoapHeader( $serviceURI, $soapAction, $securityToken );
        $soapEnvelope->appendChild( $soapRequestDOM->importNode( $soapHeaderNode, true ) );
        /* Create the SOAP Body */
        $soapBodyNode = $soapEnvelope->appendChild( $soapRequestDOM->createElement( 's:Body' ) );
        $soapBodyNode->appendChild( $soapRequestDOM->importNode( $bodyContentNode, true ) );

        return $soapRequestDOM->saveXML( $soapEnvelope );
    }

    /**
     * Generate a Soap Header using the specified service URI and SoapAction
     * Include the details from the Security Token for login
     *
     * @ignore
     */
    protected function generateSoapHeader( $serviceURI, $soapAction, $securityToken ) {
        $soapHeaderDOM = new DOMDocument();
        $headerNode    = $soapHeaderDOM->appendChild( $soapHeaderDOM->createElement( 's:Header' ) );
        $headerNode->appendChild( $soapHeaderDOM->createElement( 'a:Action', $soapAction ) )->setAttribute( 's:mustUnderstand', '1' );

        $headerNode->appendChild( $soapHeaderDOM->createElement( 'SdkClientVersion', "8.1.0.383" ) )->setAttribute( 'xmlns', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
        $headerNode->appendChild( $soapHeaderDOM->createElement( 'UserType', "CrmUser" ) )->setAttribute( 'xmlns', 'http://schemas.microsoft.com/xrm/2011/Contracts' );

        $headerNode->appendChild( $soapHeaderDOM->createElement( 'a:ReplyTo' ) )->appendChild( $soapHeaderDOM->createElement( 'a:Address', 'http://www.w3.org/2005/08/addressing/anonymous' ) );
        $headerNode->appendChild( $soapHeaderDOM->createElement( 'a:MessageId', 'urn:uuid:' . parent::getUuid() ) );
        $headerNode->appendChild( $soapHeaderDOM->createElement( 'a:To', $serviceURI ) )->setAttribute( 's:mustUnderstand', '1' );
        $securityHeaderNode = $this->authentication->getSecurityHeaderNode( $securityToken );
        $headerNode->appendChild( $soapHeaderDOM->importNode( $securityHeaderNode, true ) );

        return $headerNode;
    }

    /**
     * Utility function that checks base CRM Connection settings
     * Checks the Discovery URL, username and password in provided settings and verifies all the necessary data exists
     *
     * @return boolean indicator showing if the connection details are okay
     * @ignore
     */
    private function checkConnectionSettings() {
        /* username and password are common for authentication modes */
        if ( $this->settings->username == null || $this->settings->password == null ) {
            return false;
        }
        if ( $this->settings->authMode == "Federation" && $this->settings->discoveryUrl == null ) {
            return false;
        }

        return true;
    }

    /**
     * Send a RetrieveMultiple request to the Dynamics CRM server
     * and return the results as a structured Object
     * Each Entity returned is processed into an appropriate AlexaCRM\CRMToolkit\AlexaSDK_Entity object
     *
     * @param string $queryXML the Fetch XML string (as generated by the Advanced Find tool on Microsoft Dynamics CRM 2011)
     * @param boolean $allPages indicates if the query should be resent until all possible data is retrieved
     * @param string $pagingCookie if multiple pages are returned, send the paging cookie to get pages 2 and onwards.  Use NULL to get the first page.  Ignored if $allPages is specified.
     * @param integer $limitCount maximum number of records to be returned per page
     * @param boolean $simpleMode indicates if we should just use stdClass, instead of creating Entities
     *
     * @return stdClass a PHP Object containing all the data retrieved.
     */
    public function retrieveMultiple( $queryXML, $allPages = false, $pagingCookie = null, $limitCount = null, $pageNumber = null, $simpleMode = false ) {
        /* Prepare an Object to hold the returned data */
        $soapData = null;
        /* If we need all pages, ignore any supplied paging cookie */
        if ( $allPages ) {
            $pagingCookie = null;
        }
        do {
            /* Get the raw XML data */
            $rawSoapResponse = $this->retrieveMultipleRaw( $queryXML, $pagingCookie, $limitCount, $pageNumber );
            /* Parse the raw XML data into an Object */
            $tmpSoapData = self::parseRetrieveMultipleResponse( $this, $rawSoapResponse, $simpleMode );
            /* If we already had some data, add the old Entities */
            if ( $soapData != null ) {
                $tmpSoapData->Entities = array_merge( $soapData->Entities, $tmpSoapData->Entities );
                $tmpSoapData->Count += $soapData->Count;
            }
            /* Save the new Soap Data */
            $soapData = $tmpSoapData;
            /* Check if the PagingCookie is present & needed */
            if ( $soapData->MoreRecords && $soapData->PagingCookie == null ) {
                /* Paging Cookie is not present in returned data, but is expected! */
                /* Check if a Paging Cookie was supplied */
                if ( $pagingCookie == null ) {
                    /* This was the first page */
                    $pageNo = 1;
                } else {
                    /* This is the page from the last PagingCookie, plus 1 */
                    $pageNo = self::getPageNo( $pagingCookie ) + 1;
                }
                /* Create a new paging cookie for this page */
                $pagingCookie           = '<cookie page="' . $pageNo . '"></cookie>';
                $soapData->PagingCookie = $pagingCookie;
            } else {
                /* PagingCookie exists, or is not needed */
                $pagingCookie = $soapData->PagingCookie;
            }
            /* Loop while there are more records, and we want all pages */
        } while ( $soapData->MoreRecords && $allPages );

        /* Return the compiled structure */

        return $soapData;
    }

    /**
     * Send a RetrieveMultiple request to the Dynamics CRM server
     * and return the results as a structured Object
     * Each Entity returned is processed into a simple stdClass
     * Note that this function is faster than using Entities, but not as strong
     * at handling complicated return types.
     *
     * @param string $queryXML the Fetch XML string (as generated by the Advanced Find tool on Microsoft Dynamics CRM 2011)
     * @param boolean $allPages indicates if the query should be resent until all possible data is retrieved
     * @param string $pagingCookie if multiple pages are returned, send the paging cookie to get pages 2 and onwards.  Use NULL to get the first page.  Ignored if $allPages is specified.
     * @param integer $limitCount maximum number of records to be returned per page
     *
     * @return stdClass a PHP Object containing all the data retrieved.
     */
    public function retrieveMultipleSimple( $queryXML, $allPages = true, $pagingCookie = null, $pageNumber = null, $limitCount = null ) {
        return $this->retrieveMultiple( $queryXML, $allPages, $pagingCookie, $limitCount, $pageNumber, true );
    }

    /**
     * retrieve a single Entity based on queryXML
     *
     * @param string $queryXML the Fetch XML string (as generated by the Advanced Find tool on Microsoft Dynamics CRM)
     *
     * @return Entity a PHP Object containing all the data retrieved.
     */
    public function retrieveSingle( $queryXML ) {
        /* Execute retrieve multiple action with limitation for 1 record */
        $result = $this->retrieveMultiple( $queryXML, false, null, 1, null, false );

        /* If record exists return the Entity object */

        return ( $result->Count ) ? $result->Entities[0] : null;
    }

    /**
     * Send a RetrieveMultiple request to the Dynamics CRM server
     * and return the results as raw XML
     * This is particularly useful when debugging the responses from the server
     *
     * @param string $queryXML the Fetch XML string (as generated by the Advanced Find tool on Microsoft Dynamics CRM 2011)
     * @param string $pagingCookie if multiple pages are returned, send the paging cookie to get pages 2 and onwards.  Use NULL to get the first page
     * @param integer $limitCount maximum number of records to be returned per page
     *
     * @return string the raw XML returned by the server, including all SOAP Envelope, Header and Body data.
     */
    public function retrieveMultipleRaw( $queryXML, $pagingCookie = null, $limitCount = null, $pageNumber = null ) {
        /* Send the sequrity request and get a security token */
        $securityToken = $this->authentication->getOrganizationSecurityToken();
        /* Generate the XML for the Body of a RetrieveMulitple request */
        $executeNode = SoapRequestsGenerator::generateRetrieveMultipleRequest( $queryXML, $pagingCookie, $limitCount, $pageNumber );
        /* Turn this into a SOAP request, and send it */
        $retrieveMultipleSoapRequest = $this->generateSoapRequest( $this->settings->organizationUrl, $this->soapActions->getSoapAction( 'organization', 'RetrieveMultiple' ), $securityToken, $executeNode );

        /* Execute request to Dynamics CRM Soap web service and get response */

        return self::getSoapResponse( $this->settings->organizationUrl, $retrieveMultipleSoapRequest );
    }

    /**
     * Parse the results of a RetrieveMultipleRequest into a useable PHP object
     *
     * @param Client $client
     * @param string $soapResponse
     * @param boolean $simpleMode
     *
     * @return object
     * @throws Exception
     * @ignore
     */
    public static function parseRetrieveMultipleResponse( Client $client, $soapResponse, $simpleMode ) {
        /* Load the XML into a DOMDocument */
        $soapResponseDOM = new DOMDocument();
        $soapResponseDOM->loadXML( $soapResponse );
        /* Find the RetrieveMultipleResponse */
        $retrieveMultipleResponseNode = null;
        foreach ( $soapResponseDOM->getElementsByTagName( 'RetrieveMultipleResponse' ) as $node ) {
            $retrieveMultipleResponseNode = $node;
            break;
        }
        unset( $node );
        if ( $retrieveMultipleResponseNode == null ) {
            throw new Exception( 'Could not find RetrieveMultipleResponse node in XML provided' );
        }
        /* Find the RetrieveMultipleResult node */
        $retrieveMultipleResultNode = null;
        foreach ( $retrieveMultipleResponseNode->getElementsByTagName( 'RetrieveMultipleResult' ) as $node ) {
            $retrieveMultipleResultNode = $node;
            break;
        }
        unset( $node );
        if ( $retrieveMultipleResultNode == null ) {
            throw new Exception( 'Could not find RetrieveMultipleResult node in XML provided' );
        }
        /* Assemble an associative array for the details to return */
        $responseDataArray                 = Array();
        $responseDataArray['EntityName']   = $retrieveMultipleResultNode->getElementsByTagName( 'EntityName' )->length == 0 ? null : $retrieveMultipleResultNode->getElementsByTagName( 'EntityName' )->item( 0 )->textContent;
        $responseDataArray['MoreRecords']  = ( $retrieveMultipleResultNode->getElementsByTagName( 'MoreRecords' )->item( 0 )->textContent == 'true' );
        $responseDataArray['PagingCookie'] = $retrieveMultipleResultNode->getElementsByTagName( 'PagingCookie' )->length == 0 ? null : $retrieveMultipleResultNode->getElementsByTagName( 'PagingCookie' )->item( 0 )->textContent;
        $responseDataArray['Entities']     = Array();
        /* Loop through the Entities returned */
        foreach ( $retrieveMultipleResultNode->getElementsByTagName( 'Entities' )->item( 0 )->getElementsByTagName( 'Entity' ) as $entityNode ) {
            /* If we are in "SimpleMode", just create the Attributes as a stdClass */
            if ( $simpleMode ) {
                /* Create an Array to hold the Entity properties */
                $entityArray = [ ];
                /* Identify the Attributes */
                $keyValueNodes = $entityNode->getElementsByTagName( 'Attributes' )->item( 0 )->getElementsByTagName( 'KeyValuePairOfstringanyType' );
                /* Add the Attributes in the Key/Value Pairs of String/AnyType to the Array */
                self::addAttributes( $entityArray, $keyValueNodes );
                /* Identify the FormattedValues */
                $keyValueNodes = $entityNode->getElementsByTagName( 'FormattedValues' )->item( 0 )->getElementsByTagName( 'KeyValuePairOfstringstring' );
                /* Add the Formatted Values in the Key/Value Pairs of String/String to the Array */
                self::addFormattedValues( $entityArray, $keyValueNodes );
                /* Add the Entity to the Entities Array as a stdClass Object */
                $responseDataArray['Entities'][] = (Object) $entityArray;
            } else {
                /* Generate a new Entity from the DOMNode */
                $entity = Entity::fromDOM( $client, $responseDataArray['EntityName'], $entityNode );
                /* Add the Entity to the Entities Array as a AlexaCRM\CRMToolkit\AlexaSDK_Entity Object */
                $responseDataArray['Entities'][] = $entity;
            }
        }
        /* Record the number of Entities */
        $responseDataArray['Count'] = count( $responseDataArray['Entities'] );
        /* Convert the Array to a stdClass Object */
        $responseData = (object) $responseDataArray;

        return $responseData;
    }

    /**
     * Add a list of Attributes to an Array of Attributes, using appropriate handling
     * of the Attribute type, and avoiding over-writing existing attributes
     * already in the array
     * Optionally specify an Array of sub-keys, and a particular sub-key
     * - If provided, each sub-key in the Array will be created as an Object attribute,
     *   and the value will be set on the specified sub-key only (e.g. (New, Old) / New)
     *
     * @ignore
     */
    protected static function addAttributes( array &$targetArray, DOMNodeList $keyValueNodes, Array $keys = null, $key1 = null ) {
        foreach ( $keyValueNodes as $keyValueNode ) {
            /* Get the Attribute name (key) */
            $attributeKey = $keyValueNode->getElementsByTagName( 'key' )->item( 0 )->textContent;
            /* Check the Value Type */
            $attributeValueType = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'type' );
            /* Strip any Namespace References from the Type */
            $attributeValueType = self::stripNS( $attributeValueType );
            switch ( $attributeValueType ) {
                case 'AliasedValue':
                    /* For an AliasedValue, the Key is Alias.Field, so just get the Alias */
                    list( $attributeKey, ) = explode( '.', $attributeKey, 2 );
                    /* Entity Logical Name => the Object Type */
                    $entityLogicalName = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'EntityLogicalName' )->item( 0 )->textContent;
                    /* Attribute Logical Name => the actual Attribute of the Aliased Object */
                    $attributeLogicalName = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'AttributeLogicalName' )->item( 0 )->textContent;
                    $entityAttributeValue = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Value' )->item( 0 )->textContent;
                    /* See if this Alias is already in the Array */
                    if ( array_key_exists( $attributeKey, $targetArray ) ) {
                        /* It already exists, so grab the existing Object and set the new Attribute */
                        $attributeValue                        = $targetArray[ $attributeKey ];
                        $attributeValue->$attributeLogicalName = $entityAttributeValue;
                        /* Pull it from the array, so we don't set a duplicate */
                        unset( $targetArray[ $attributeKey ] );
                    } else {
                        /* Create a new Object with the Logical Name, and this Attribute */
                        $attributeValue = (Object) Array(
                            'LogicalName'         => $entityLogicalName,
                            $attributeLogicalName => $entityAttributeValue
                        );
                    }
                    break;
                case 'EntityReference':
                    $attributeLogicalName = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'LogicalName' )->item( 0 )->textContent;
                    $attributeId          = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Id' )->item( 0 )->textContent;
                    $attributeName        = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Name' )->item( 0 )->textContent;
                    $attributeValue       = (Object) Array(
                        'LogicalName' => $attributeLogicalName,
                        'Id'          => $attributeId,
                        'Name'        => $attributeName
                    );
                    break;
                case 'OptionSetValue':
                    $attributeValue = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Value' )->item( 0 )->textContent;
                    break;
                case 'dateTime':
                    $attributeValue = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->textContent;
                    $attributeValue = self::parseTime( $attributeValue, '%Y-%m-%dT%H:%M:%SZ' );
                    break;
                default:
                    $attributeValue = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->textContent;
            }
            /* If we are working normally, just store the data in the array */
            if ( $keys == null ) {
                /* Assume that if there is a duplicate, it's a formatted version of this */
                if ( array_key_exists( $attributeKey, $targetArray ) ) {
                    $targetArray[ $attributeKey ] = (object) [
                        'Value'          => $attributeValue,
                        'FormattedValue' => $targetArray[ $attributeKey ]
                    ];
                } else {
                    $targetArray[ $attributeKey ] = $attributeValue;
                }
            } else {
                /* Store the data in the array for this AuditRecord's properties */
                if ( array_key_exists( $attributeKey, $targetArray ) ) {
                    /* We assume it's already a "good" Object, and just set this key */
                    if ( isset( $targetArray[ $attributeKey ]->$key1 ) ) {
                        /* It's already set, so add the Un-formatted version */
                        $targetArray[ $attributeKey ]->$key1 = (Object) Array(
                            'Value'          => $attributeValue,
                            'FormattedValue' => $targetArray[ $attributeKey ]->$key1
                        );
                    } else {
                        /* It's not already set, so just set this as a value */
                        $targetArray[ $attributeKey ]->$key1 = $attributeValue;
                    }
                } else {
                    /* We need to create the Object */
                    $obj = (Object) Array();
                    foreach ( $keys as $k ) {
                        $obj->$k = null;
                    }
                    /* And set the particular property */
                    $obj->$key1 = $attributeValue;
                    /* And store the Object in the target Array */
                    $targetArray[ $attributeKey ] = $obj;
                }
            }
        }
    }

    /**
     * Find the PageNumber in a PagingCookie
     *
     * @param String $pagingCookie
     *
     * @ignore
     */
    public static function getPageNo( $pagingCookie ) {
        /* Turn the pagingCookie into a DOMDocument so we can read it */
        $pagingDOM = new DOMDocument();
        $pagingDOM->loadXML( $pagingCookie );
        /* Find the page number */
        $pageNo = $pagingDOM->documentElement->getAttribute( 'page' );

        return (int) $pageNo;
    }

    /**
     * Send a Retrieve request to the Dynamics CRM 2011 server and return the results as a structured Object
     * This function is typically used just after creating something (where you get the ID back
     * as the return value), as it is more efficient to use RetrieveMultiple to search directly if
     * you don't already have the ID.
     *
     * @param Entity $entity the Entity to retrieve - must have an ID specified
     * @param array $columnSet array listing all fields to be fetched, or null to get all fields
     *
     * @return Entity (subclass) a Strongly-Typed Entity containing all the data retrieved.
     */
    public function retrieve( Entity $entity, $columnSet = null ) {
        /* Get the raw XML data */
        $rawSoapResponse = $this->retrieveRaw( $entity, $columnSet );
        /* Parse the raw XML data into an Object */
        $newEntity = self::parseRetrieveResponse( $this, $entity->LogicalName, $rawSoapResponse );

        /* Return the structured object */

        return $newEntity;
    }

    /**
     * Parse the results of a RetrieveRequest into a useable PHP object
     *
     * @param Client $client
     * @param String $entityLogicalName
     * @param String $soapResponse
     *
     * @ignore
     */
    private function parseRetrieveResponse( Client $client, $entityLogicalName, $soapResponse ) {
        /* Load the XML into a DOMDocument */
        $soapResponseDOM = new DOMDocument();
        $soapResponseDOM->loadXML( $soapResponse );
        /* Find the RetrieveResponse */
        $retrieveResponseNode = null;
        foreach ( $soapResponseDOM->getElementsByTagName( 'RetrieveResponse' ) as $node ) {
            $retrieveResponseNode = $node;
            break;
        }
        unset( $node );
        if ( $retrieveResponseNode == null ) {
            throw new Exception( 'Could not find RetrieveResponse node in XML provided' );
        }
        /* Find the RetrieveResult node */
        $retrieveResultNode = null;
        foreach ( $retrieveResponseNode->getElementsByTagName( 'RetrieveResult' ) as $node ) {
            $retrieveResultNode = $node;
            break;
        }
        unset( $node );
        if ( $retrieveResultNode == null ) {
            throw new Exception( 'Could not find RetrieveResult node in XML provided' );
        }
        /* Generate a new Entity from the DOMNode */
        $entity = Entity::FromDom( $client, $entityLogicalName, $retrieveResultNode );

        return $entity;
    }

    /**
     * Send a Create request to the Dynamics CRM server, and return the ID of the newly created Entity
     *
     * @param Entity $entity the Entity to create
     *
     * @return string|bool EntityId on success, FALSE on failure
     * @throws Exception
     */
    public function create( Entity &$entity ) {
        $this->purgeEntityCache( $entity->logicalName );

        /* Only allow "Create" for an Entity with no ID */
        if ( $entity->ID != self::EmptyGUID ) {
            throw new Exception( 'Cannot Create an Entity that already exists.' );
        }
        /* Send the security request and get a security token */
        $securityToken = $this->authentication->getOrganizationSecurityToken();
        /* Generate the XML for the Body of a Create request */
        $createNode = SoapRequestsGenerator::generateCreateRequest( $entity );

        Logger::log( PHP_EOL . 'Create Request: ' . PHP_EOL . $createNode->C14N() . PHP_EOL );
        /* Turn this into a SOAP request, and send it */
        $createRequest = $this->generateSoapRequest( $this->settings->organizationUrl, $this->soapActions->getSoapAction( 'organization', 'Create' ), $securityToken, $createNode );

        $soapResponse = self::getSoapResponse( $this->settings->organizationUrl, $createRequest );

        Logger::log( PHP_EOL . 'Create Response: ' . PHP_EOL . $soapResponse . PHP_EOL );

        /* Load the XML into a DOMDocument */
        $soapResponseDOM = new DOMDocument();
        $soapResponseDOM->loadXML( $soapResponse );
        /* Find the CreateResponse */
        $createResponseNode = null;
        foreach ( $soapResponseDOM->getElementsByTagName( 'CreateResponse' ) as $node ) {
            $createResponseNode = $node;
            break;
        }
        unset( $node );
        if ( $createResponseNode == null ) {
            throw new Exception( 'Could not find CreateResponse node in XML returned from Server' );
        }
        /* Get the EntityID from the CreateResult tag */
        $entityID   = $createResponseNode->getElementsByTagName( 'CreateResult' )->item( 0 )->textContent;
        $entity->ID = $entityID;
        $entity->reset();

        return $entityID;
    }

    /**
     * Send an Update request to the Dynamics CRM server, and return update response status
     *
     * @param Entity $entity the Entity to update
     *
     * @return string Formatted raw XML response of update request
     */
    public function update( Entity &$entity ) {
        $this->purgeEntityCache( $entity->logicalName );

        /* Only allow "Update" for an Entity with an ID */
        if ( $entity->ID == self::EmptyGUID ) {
            throw new Exception( 'Cannot Update an Entity without an ID.' );
        }
        /* Send the sequrity request and get a security token */
        $securityToken = $this->authentication->getOrganizationSecurityToken();
        /* Generate the XML for the Body of an Update request */
        $updateNode = SoapRequestsGenerator::generateUpdateRequest( $entity );
        Logger::log( PHP_EOL . 'Update Request: ' . PHP_EOL . $updateNode->C14N() . PHP_EOL );
        /* Turn this into a SOAP request, and send it */
        $updateRequest = $this->generateSoapRequest( $this->settings->organizationUrl, $this->soapActions->getSoapAction( 'organization', 'Update' ), $securityToken, $updateNode );
        /* Get response */
        $soapResponse = self::getSoapResponse( $this->settings->organizationUrl, $updateRequest );
        Logger::log( PHP_EOL . 'Update Response: ' . PHP_EOL . $soapResponse . PHP_EOL );
        /* Load the XML into a DOMDocument */
        $soapResponseDOM = new DOMDocument();
        $soapResponseDOM->loadXML( $soapResponse );
        /* Find the UpdateResponse */
        $updateResponseNode = null;
        foreach ( $soapResponseDOM->getElementsByTagName( 'UpdateResponse' ) as $node ) {
            $updateResponseNode = $node;
            break;
        }
        unset( $node );
        if ( $updateResponseNode == null ) {
            throw new Exception( 'Could not find UpdateResponse node in XML returned from Server' );
        }

        /* Update occurred successfully */

        return $updateResponseNode->C14N();
    }

    /**
     * Send a Delete request to the Dynamics CRM server, and return delete response status
     *
     * @param Entity $entity the Entity to delete
     *
     * @return boolean TRUE on successful delete, false on failure
     */
    public function delete( Entity &$entity ) {
        $this->purgeEntityCache( $entity->logicalName );

        /* Only allow "Delete" for an Entity with an ID */
        if ( $entity->ID == self::EmptyGUID ) {
            throw new Exception( 'Cannot Delete an Entity without an ID.' );
        }
        /* Send the sequrity request and get a security token */
        $securityToken = $this->authentication->getOrganizationSecurityToken();
        /* Generate the XML for the Body of a Delete request */
        $deleteNode = SoapRequestsGenerator::generateDeleteRequest( $entity );

        Logger::log( PHP_EOL . 'Delete Request: ' . PHP_EOL . $deleteNode->C14N() . PHP_EOL );
        /* Turn this into a SOAP request, and send it */
        $deleteRequest = $this->generateSoapRequest( $this->settings->organizationUrl, $this->soapActions->getSoapAction( 'organization', 'Delete' ), $securityToken, $deleteNode );
        $soapResponse  = self::getSoapResponse( $this->settings->organizationUrl, $deleteRequest );

        Logger::log( PHP_EOL . 'Delete Response: ' . PHP_EOL . $soapResponse . PHP_EOL );
        /* Load the XML into a DOMDocument */
        $soapResponseDOM = new DOMDocument();
        $soapResponseDOM->loadXML( $soapResponse );
        /* Find the DeleteResponse */
        $deleteResponseNode = null;
        foreach ( $soapResponseDOM->getElementsByTagName( 'DeleteResponse' ) as $node ) {
            $deleteResponseNode = $node;
            break;
        }
        unset( $node );
        if ( $deleteResponseNode == null ) {
            throw new Exception( 'Could not find DeleteResponse node in XML returned from Server' );
        }

        /* Delete occurred successfully */

        return true;
    }

    public function upsert( Entity &$entity ) {
        $this->purgeEntityCache( $entity->logicalName );

        /* Check the Dynamics CRM version, if it less then 7.1.0, throw exception */
        if ( version_compare( $this->settings->organizationVersion, "7.1.0", "<" ) ) {
            throw new Exception( 'Upsert request is not supported for the organization version lower then 7.1.0' );
        }
        /* Send the sequrity request and get a security token */
        $securityToken = $this->authentication->getOrganizationSecurityToken();
        /* Generate the XML for the Body of an Update request */
        $upsertNode = SoapRequestsGenerator::generateUpsertRequest( $entity );
        Logger::log( PHP_EOL . 'Upsert Request: ' . PHP_EOL . $upsertNode->C14N() . PHP_EOL );
        /* Turn this into a SOAP request, and send it */
        $upsertRequest = $this->generateSoapRequest( $this->settings->organizationUrl, $this->soapActions->getSoapAction( 'organization', 'Execute' ), $securityToken, $upsertNode );
        $soapResponse  = self::getSoapResponse( $this->settings->organizationUrl, $upsertRequest );
        /* Load the XML into a DOMDocument */
        $soapResponseDOM = new DOMDocument();
        $soapResponseDOM->loadXML( $soapResponse );
        /* Find the UpdateResponse */
        $executeResultNode = null;
        foreach ( $soapResponseDOM->getElementsByTagName( 'ExecuteResult' ) as $node ) {
            $executeResultNode = $node;
            break;
        }
        unset( $node );
        if ( $executeResultNode == null ) {
            throw new Exception( 'Could not find ExecuteResult node in XML returned from Server' );
        }
        $keyValuesArray = Array();
        foreach ( $executeResultNode->getElementsByTagName( 'KeyValuePairOfstringanyType' ) as $keyValueNode ) {
            $keyValuesArray[ $keyValueNode->getElementsByTagName( 'key' )->item( 0 )->textContent ] = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->textContent;
        }
        /* Add the Entity to the KeyValues Array as a stdClass Object */
        $responseDataArray = (Object) $keyValuesArray;

        /* Return structured Key/Value object */

        return $responseDataArray;
    }

    /**
     * ExecuteAction Request
     *
     * @param string $requestName name of Action to request
     * @param $parameters
     * @param $requestType
     *
     * @return stdClass returns std class object of responded data
     * @throws Exception
     * @internal param $Array (optional)
     */
    public function executeAction( $requestName, $parameters = null, $requestType = null ) {
        try {
            /* Send the security request and get a security token */
            $securityToken = $this->authentication->getOrganizationSecurityToken();
            /* Generate the XML for the Body of a Execute Action request */
            $executeActionNode = SoapRequestsGenerator::generateExecuteActionRequest( $requestName, $parameters, $requestType );

            Logger::log( PHP_EOL . 'ExecuteAction Request: ' . PHP_EOL . $executeActionNode->C14N() . PHP_EOL );
            /* Turn this into a SOAP request, and send it */
            $executeActionRequest = $this->generateSoapRequest( $this->settings->organizationUrl, $this->soapActions->getSoapAction( 'organization', 'Execute' ), $securityToken, $executeActionNode );

            $soapResponse = self::getSoapResponse( $this->settings->organizationUrl, $executeActionRequest );

            Logger::log( PHP_EOL . 'ExecuteAction Response: ' . PHP_EOL . $soapResponse . PHP_EOL );

            /* Load the XML into a DOMDocument */
            $soapResponseDOM = new DOMDocument();
            $soapResponseDOM->loadXML( $soapResponse );
            /* Find the UpdateResponse */
            $executeResultNode = null;
            foreach ( $soapResponseDOM->getElementsByTagName( 'ExecuteResult' ) as $node ) {
                $executeResultNode = $node;
                break;
            }
            unset( $node );
            if ( $executeResultNode == null ) {
                throw new Exception( 'Could not find ExecuteResult node in XML returned from Server' );
            }

            $keyValuesArray = array();

            foreach ( $executeResultNode->getElementsByTagName( 'KeyValuePairOfstringanyType' ) as $keyValueNode ) {
                $keyValuesArray[ $keyValueNode->getElementsByTagName( 'key' )->item( 0 )->textContent ] = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->textContent;
            }
            /* Add the Entity to the KeyValues Array as a stdClass Object */
            $responseDataArray = (Object) $keyValuesArray;

            /* Return structured Key/Value object */

            return $responseDataArray;
        } catch ( Exception $ex ) {
            Logger::log( "Exception:", $ex );
            throw $ex;
        }
    }

    public function retrieveAllEntitiesRaw( $entityFilters = null, $retrieveAsIfPublished = false ) {
        /* Send the security request and get a security token */
        $securityToken = $this->authentication->getOrganizationSecurityToken();
        /* Generate the XML for the Body of a RetrieveEntity request */
        $executeNode = SoapRequestsGenerator::generateRetrieveAllEntitiesRequest( $entityFilters, $retrieveAsIfPublished );
        /* Turn this into a SOAP request, and send it */
        $retrieveEntityRequest = $this->generateSoapRequest( $this->settings->organizationUrl, $this->soapActions->getSoapAction( 'organization', 'Execute' ), $securityToken, $executeNode );
        $soapResponse          = self::getSoapResponse( $this->settings->organizationUrl, $retrieveEntityRequest );

        return $soapResponse;
    }

    /**
     * Retrieves metadata for every entity from the CRM
     *
     * @param string $entityFilters
     * @param bool $retrieveAsIfPublished
     *
     * @return SimpleXMLElement[]
     */
    public function retrieveAllEntities( $entityFilters = null, $retrieveAsIfPublished = false ) {
        /* Get the raw XML data */
        $rawSoapResponse = $this->retrieveAllEntitiesRaw( $entityFilters, $retrieveAsIfPublished );
        /* Parse the raw XML data into an Object */
        $soapData = self::parseRetrieveAllEntitiesResponse( $rawSoapResponse );

        /* Return the structured object */

        return $soapData;
    }

    /**
     * Checks whether caching is enabled
     *
     * @return bool
     */
    public function isCacheEnabled() {
        return ( $this->cache instanceof CacheInterface );
    }

    /**
     * Purges the volatile entity cache entirely or by specific logical name.
     *
     * @param string $logicalName
     */
    private function purgeEntityCache( $logicalName = null ) {
        if ( is_null( $logicalName ) ) {
            static::$entityCache = [];
            static::$entityCacheRefs = [];

            return;
        }

        if ( !array_key_exists( $logicalName, static::$entityCacheRefs ) ) {
            return;
        }

        $doomedRecords = static::$entityCacheRefs[$logicalName];
        foreach ( $doomedRecords as $cacheKey ) {
            unset( static::$entityCache[$cacheKey] );
        }
    }
}
