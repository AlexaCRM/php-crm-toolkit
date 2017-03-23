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

use DOMDocument;
use Exception;

/**
 * AlexaCRM\CRMToolkit\AlexaSDK_SoapActions.class.php
 *
 * @author alexacrm.com
 * @version 1.0
 * @package AlexaCRM\CRMToolkit\AlexaSDK
 */
class SoapActions {

    /**
     * Object of AlexaCRM\CRMToolkit\Client class
     *
     * @var Client
     */
    private $client;

    /**
     * @ignore
     */
    private $organizationSoapActions;

    /**
     * @ignore
     * @var array
     */
    private $discoverySoapActions;

    /**
     * Create a new usable Dynamics CRM Soap Actions object for Discovery Service and Organization service
     *
     * @param Client $client Connection to the Dynamics CRM server - should be active already.
     */
    public function __construct( Client $client ) {
        $this->client                  = $client;
        $this->organizationSoapActions = $this->getCachedSoapActions( 'organization' );
        $this->discoverySoapActions    = $this->getCachedSoapActions( 'discovery' );
    }

    /**
     * Sets Soap Actions to cache
     *
     * @param string $service
     * @param SoapActions $soapActions
     */
    private function setCachedSoapActions( $service, $soapActions ) {
        $cache = $this->client->cache;
        $cache->set( $service . "_soap_actions", $soapActions, 4*7*24*60*60 ); // four weeks
    }

    /**
     * Retrieves Soap Actions from cache
     *
     * @param string $service
     *
     * @return array|null Soap Action array of strings, or NULL if action not cached
     */
    private function getCachedSoapActions( $service ) {
        $cache       = $this->client->cache;
        $soapActions = $cache->get( $service . '_soap_actions' );

        return $soapActions;
    }

    /**
     * Utility function to get the SoapAction
     *
     * @param string $service Dynamics CRM soap service, can be 'organization' or 'discovery'
     * @param string $soapAction Action for soap method
     *
     * @return string
     * @throws Exception
     */
    public function getSoapAction( $service, $soapAction ) {
        /* Capitalize first char in action name */
        $action = $soapAction; /* ucfirst(strtolower($soapAction)); */
        /* Switch service for soap action */
        switch ( strtolower( $service ) ) {
            case "organization":
                return $this->getOrganizationAction( $action );
                break;
            case "discovery":
                return $this->getDiscoveryAction( $action );
                break;
            default:
                throw new Exception( "Undefined service(" . $service . ") for soap action(" . $action . ")" );
        }
    }

    public function getOrganizationAction( $action ) {
        $soapActions = $this->getAllOrganizationSoapActions();

        return $soapActions[ $action ];
    }

    public function getDiscoveryAction( $action ) {
        $soapActions = $this->getAllDiscoverySoapActions();

        return $soapActions[ $action ];
    }

    /**
     * Get all the Operations & corresponding SoapActions for the DiscoveryService
     */
    private function getAllDiscoverySoapActions() {
        /* If it is not cached, update the cache */
        if ( $this->discoverySoapActions == null ) {
            $this->discoverySoapActions = self::getAllSoapActions( $this->client->getDiscoveryDOM(), 'DiscoveryService' );
            $this->setCachedSoapActions( 'discovery', $this->discoverySoapActions );
        }

        /* Return the cached value */

        return $this->discoverySoapActions;
    }

    /**
     * Get all the Operations & corresponding SoapActions for the OrganizationService
     */
    private function getAllOrganizationSoapActions() {
        /* If it is not cached, update the cache */
        if ( $this->organizationSoapActions == null ) {
            $this->organizationSoapActions = self::getAllSoapActions( $this->client->getOrganizationDOM(), 'OrganizationService' );
            $this->setCachedSoapActions( 'organization', $this->organizationSoapActions );
        }

        /* Return the cached value */

        return $this->organizationSoapActions;
    }

    /**
     * Search a Microsoft Dynamics CRM WSDL for all available Operations/SoapActions on a Service
     *
     * @ignore
     *
     * @param DOMDocument $wsdlDocument
     * @param $serviceName
     *
     * @return array
     * @throws Exception
     */
    private static function getAllSoapActions( DOMDocument $wsdlDocument, $serviceName ) {
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
            throw new Exception( 'Could not find defintion of Binding <' . $bindingName . '> in provided WSDL' );
        }
        /* Array to store the list of Operations and SoapActions */
        $operationArray = Array();
        /* Find the Operations */
        foreach ( $bindingNode->getElementsByTagName( 'operation' ) as $operationNode ) {
            if ( $operationNode->hasAttribute( 'name' ) ) {
                /* Record the Name of this Operation */
                $operationName = $operationNode->getAttribute( 'name' );
                /* Find the Operation SoapAction from the WSDL */
                foreach ( $operationNode->getElementsByTagName( 'operation' ) as $soap12OperationNode ) {
                    if ( $soap12OperationNode->hasAttribute( 'soapAction' ) ) {
                        /* Record the SoapAction for this Operation */
                        $soapAction = $soap12OperationNode->getAttribute( 'soapAction' );
                        /* Store the soapAction in the Array */
                        $operationArray[ $operationName ] = $soapAction;
                    }
                }
                unset( $soap12OperationNode );
            }
        }

        /* Return the array of available actions */

        return $operationArray;
    }
}
