<?php

if (!class_exists("AlexaSDK")) :
    
    class AlexaSDK extends AlexaSDK_Abstract{
    
        public $authenticationType;
        public $authentication;
        private $organizationUniqueName;
        public $domain;
        
        public $organizationUrl;
        public $discoveryUrl;
        public $settings;
        
        /* Cached Organization data */
	private $organizationDOM;
	private $organizationSoapActions;
	private $organizationCreateAction;
	private $organizationDeleteAction;
	private $organizationExecuteAction;
	private $organizationRetrieveAction;
	private $organizationRetrieveMultipleAction;
	private $organizationUpdateAction;
	private $organizationSecurityPolicy;
	private $organizationSecurityToken;
        
        /* Cached Entity Definitions */
	private $cachedEntityDefintions = Array();
        private $cacheClass;
        
        /* Security Details */
	public $security = Array();
	/* Cached Discovery data */
	private $discoveryDOM;
	private $discoverySoapActions;
	private $discoveryExecuteAction;
	private $discoverySecurityPolicy;
        
        /* Connection Details */
	protected static $connectorTimeout = 6000;
        /* Cache lifetime in seconds */
        protected static $cacheTime = 6000;
	protected static $maximumRecords = self::MAX_CRM_RECORDS;
        
        
        function __construct($_settings, $_debug = NULL) {
            /* Construct AlexaCRM_Abstract class to setup includes */
            parent::__construct();
            /* Enable or disable debug mode */
            self::$debugMode = $_debug;
            
            /* Simple settings check */
            if ($_settings instanceof AlexaSDK_Settings){
                $this->settings = $_settings;
            }
            
            /* Check if we're using a cached login */
            /*if (is_array($discoveryUrl)) {
                    return $this->loadLoginCache($discoveryUrl);
            }*/
            
            /* If either mandatory parameter is NULL, throw an Exception */
            if (!$this->checkConnectionSettings()) {
                switch ($this->settings->authMode){
                    case "OnlineFederation":
                        throw new BadMethodCallException(get_class($this).' constructor requires Username and Password');
                    case "Federation":
                        throw new BadMethodCallException(get_class($this).' constructor requires the Discovery URI, Username and Password');
                }
            }
            
            switch ($this->settings->authMode){
                case "OnlineFederation":
                    $this->authentication = new AlexaSDK_Office365($this->settings);
                    break;
                case "Federation":
                    $this->authentication = new AlexaSDK_Federation($this->settings);
                    break;
            }
            
            
            /* Move this section to the separated method : 
             * 
             */ 
            $cache = $this->cacheClass = new AlexaSDK_Cache( array('storage' => 'auto'));
            //$this->clearCache();
            /* Need to Define Clean cache mechanism */
            $entities = $cache->get('entities');
            if ($entities != null){
                $this->cachedEntityDefintions = unserialize($entities);
            }
            /* End */
        }
        
        /**
	 * Authenticate to Dynamics CRM web service and store security tokens
	 *
	 * @return boolean true, if authentication succeeds
	 */
        public function Authenticate(){
            
            try{
                if (isset($this->authentication)){
                    return $this->authentication->Authenticate();
                }else{
                    return false;
                }
                
            }catch(Exception $e){
                return false;
            }
            
        }
        
        /**
	 * Generate a Soap Header using the specified service URI and SoapAction
	 * Include the details from the Security Token for login
         * @param String $soapAction SoapAction for the OrganizationService
	 * @ignore
	 */
        public function getHeader($soapAction) {
            try{
                if (isset($this->authentication)){
                    return $this->authentication->getHeader($soapAction);
                }else{
                    return false;
                }
            }catch(Exception $e){
                return false;
            }
        }
        
        /**
         * Clears all stored cache
         */
        public function clearCache(){
            /* Clear all cache using AlexaSDK_Cache */
            $this->cacheClass->cleanup();
        }
        
        /**
	 * Return the Authentication Mode used by the Discovery service 
         * @return Mixed string if one auth type, array if 
	 * @ignore
	 */
	protected function getDiscoveryAuthenticationMode() {
		/* If it's set, return the details from the Security array */
		if (isset($this->settings->authType)) 
			return $this->settings->authType;
		
		/* Get the Discovery DOM */
		$discoveryDOM = $this->getDiscoveryDOM();
                
		/* Get the Security Policy for the Organization Service from the WSDL */
		$this->discoverySecurityPolicy = self::findSecurityPolicy($discoveryDOM, 'DiscoveryService');
                
		/* Find the Authentication type used */
                
                if ($this->discoverySecurityPolicy->getElementsByTagName('Authentication')->length == 0) {
			throw new Exception('Could not find Authentication tag in provided Discovery Security policy XML');
			return FALSE;
		}
                
                $authType = Array();
                if ($this->discoverySecurityPolicy->getElementsByTagName('Authentication')->length > 1){
                    foreach($this->discoverySecurityPolicy->getElementsByTagName('Authentication') as $authentication){
                        array_push($authType, $authentication->textContent);
                    }
                }else{
                    array_push($authType, $this->discoverySecurityPolicy->getElementsByTagName('Authentication')->item(0)->textContent);
                }
                
		return $authType;
	}
        
        /**
	 * Fetch and flatten the Discovery Service WSDL as a DOM
	 * @ignore
	 */
	protected function getDiscoveryDOM() {
		/* If it's already been fetched, use the one we have */
		if ($this->discoveryDOM != NULL) return $this->discoveryDOM;
		
		/* Fetch the WSDL for the Discovery Service as a parseable DOM Document */
		if (self::$debugMode) echo 'Getting Discovery DOM WSDL data from: '.$this->settings->discoveryUrl.'?wsdl'.PHP_EOL;
                
		$discoveryDOM = new DOMDocument();
                
                @$discoveryDOM->load($this->settings->discoveryUrl.'?wsdl');
                
                
                if (self::$debugMode) :
                    
                endif;
                
		/* Flatten the WSDL and include all the Imports */
		$this->mergeWSDLImports($discoveryDOM);
		
		/* Cache the DOM in the current object */
		$this->discoveryDOM = $discoveryDOM;
		return $discoveryDOM;
	}
        
        /**
	 * Return the Authentication Address used by the Discovery service 
	 * @ignore
	 */
	protected function getDiscoveryAuthenticationAddress() {
		/* If it's set, return the details from the Security array */
		if (isset($this->security['discovery_authuri'])) 
			return $this->security['discovery_authuri'];
		
		/* If we don't already have a Security Policy, get it */
		if ($this->discoverySecurityPolicy == NULL) {
			/* Get the Discovery DOM */
			$discoveryDOM = $this->getDiscoveryDOM();
			/* Get the Security Policy for the Organization Service from the WSDL */
			$this->discoverySecurityPolicy = self::findSecurityPolicy($discoveryDOM, 'DiscoveryService');
		}
                
                
                if ($this->security['discovery_authmode'] == "Federation"){
                    /* Find the Authentication type used */
                    $authAddress = self::getFederatedSecurityAddress($this->discoverySecurityPolicy);
                }else if ($this->security['discovery_authmode'] == "OnlineFederation"){
                    $authAddress = self::getOnlineFederationSecurityAddress($this->discoverySecurityPolicy);
                }
		return $authAddress;
	}
        
        
        /**
	 * Return the Authentication Address used by the Organization service 
	 * @ignore
	 */
	public function getOrganizationAuthenticationAddress() {
		/* If it's set, return the details from the Security array */
		if (isset($this->security['organization_authuri'])) 
			return $this->security['organization_authuri'];
		
		/* If we don't already have a Security Policy, get it */
		if ($this->organizationSecurityPolicy == NULL) {
			/* Get the Organization DOM */
			$organizationDOM = $this->getOrganizationDOM();
			/* Get the Security Policy for the Organization Service from the WSDL */
			$this->organizationSecurityPolicy = self::findSecurityPolicy($organizationDOM, 'OrganizationService');
		}
		/* Find the Authentication type used */
		$authAddress = self::getFederatedSecurityAddress($this->organizationSecurityPolicy);
                
                $this->security['organization_authuri'] = $authAddress;
                
		return $authAddress;
	}
        
        /**
         * Not needed any more
         * @deprecated
         */
        public function getSecurityTokenServiceIdentifier($service){
                /* If it's set, return the details from the Security array */
		if (isset($this->security[$service.'_sts_identifier'])) 
			return $this->security[$service.'_sts_identifier'];
                
                /* If we don't already have a Security Policy, get it */
		if ($this->organizationSecurityPolicy == NULL) {
			/* Get the Organization DOM */
			$organizationDOM = $this->getOrganizationDOM();
			/* Get the Security Policy for the Organization Service from the WSDL */
			$this->organizationSecurityPolicy = self::findSecurityPolicy($organizationDOM, 'OrganizationService');
		}
                /* Find the Identifier node value */
                $this->security[$service.'_sts_identifier'] = self::getSTSidentifier($this->organizationSecurityPolicy);
                return $this->security[$service.'_sts_identifier'];
        }
        
        protected static function getSTSidentifier(DOMNode $securityPolicyNode){
                $stsIdentifier = NULL;
		/* Find the EndorsingSupportingTokens tag */
		if ($securityPolicyNode->getElementsByTagName('SecureTokenService')->length == 0) {
			throw new Exception('Could not find SecureTokenService tag in provided security policy XML');
			return FALSE;
		}
		$stsNode = $securityPolicyNode->getElementsByTagName('SecureTokenService')->item(0);
		/* Find the Policy tag */
		if ($stsNode->getElementsByTagName('Identifier')->length == 0) {
			throw new Exception('Could not find SecureTokenService/Identifier tag in provided security policy XML');
			return FALSE;
		}
		$stsIdentifierNode = $stsNode->getElementsByTagName('Identifier')->item(0);
                
                $stsIdentifier = $stsIdentifierNode->textContent;
                
		if ($stsIdentifier == NULL) {
			throw new Exception('Could not find SecurityTokenServiceIdentifier in provided security policy WSDL');
			return FALSE;
		}
                
		return $stsIdentifier;
        }
        
        
        /**
	 * Return the Authentication Mode used by the Organization service 
	 * @ignore
	 */
	public function getOrganizationAuthenticationMode() {
		/* If it's set, return the details from the Security array */
		if (isset($this->security['organization_authmode'])) 
			return $this->security['organization_authmode'];
		
		/* Get the Organization DOM */
		$organizationDOM = $this->getOrganizationDOM();
		/* Get the Security Policy for the Organization Service from the WSDL */
		$this->organizationSecurityPolicy = self::findSecurityPolicy($organizationDOM, 'OrganizationService');
		/* Find the Authentication type used */
		$authType = $this->organizationSecurityPolicy->getElementsByTagName('Authentication')->item(0)->textContent;
		return $authType;
	}
        
        
        /**
	 * Search a Microsoft Dynamics CRM Security Policy for the Address for the Federated Security 
	 * @ignore
	 */
	protected static function getFederatedSecurityAddress(DOMNode $securityPolicyNode) {
		$securityURL = NULL;
		/* Find the EndorsingSupportingTokens tag */
		if ($securityPolicyNode->getElementsByTagName('EndorsingSupportingTokens')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens tag in provided security policy XML');
			return FALSE;
		}
		$estNode = $securityPolicyNode->getElementsByTagName('EndorsingSupportingTokens')->item(0);
		/* Find the Policy tag */
		if ($estNode->getElementsByTagName('Policy')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy tag in provided security policy XML');
			return FALSE;
		}
		$estPolicyNode = $estNode->getElementsByTagName('Policy')->item(0);
		/* Find the IssuedToken tag */
		if ($estPolicyNode->getElementsByTagName('IssuedToken')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken tag in provided security policy XML');
			return FALSE;
		}
		$issuedTokenNode = $estPolicyNode->getElementsByTagName('IssuedToken')->item(0);
		/* Find the Issuer tag */
		if ($issuedTokenNode->getElementsByTagName('Issuer')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer tag in provided security policy XML');
			return FALSE;
		}
		$issuerNode = $issuedTokenNode->getElementsByTagName('Issuer')->item(0);
		/* Find the Metadata tag */
		if ($issuerNode->getElementsByTagName('Metadata')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata tag in provided security policy XML');
			return FALSE;
		}
		$metadataNode = $issuerNode->getElementsByTagName('Metadata')->item(0);
		/* Find the Address tag */
		if ($metadataNode->getElementsByTagName('Address')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata/.../Address tag in provided security policy XML');
			return FALSE;
		}
		$addressNode = $metadataNode->getElementsByTagName('Address')->item(0);
		/* Get the URI */
		$securityURL = $addressNode->textContent;
		if ($securityURL == NULL) {
			throw new Exception('Could not find Security URL in provided security policy WSDL');
			return FALSE;
		}
		return $securityURL;
	}
        
        
        /**
	 * Search a Microsoft Dynamics CRM 2011 Security Policy for the Address for the Federated Security 
	 * @ignore
	 */
	protected static function getOnlineFederationSecurityAddress(DOMNode $securityPolicyNode) {
		$securityURL = NULL;
                
		/* Find the SignedSupportingTokens tag */
		if ($securityPolicyNode->getElementsByTagName('SignedSupportingTokens')->length == 0) {
			throw new Exception('Could not find SignedSupportingTokens tag in provided security policy XML');
			return FALSE;
		}
		$estNode = $securityPolicyNode->getElementsByTagName('SignedSupportingTokens')->item(0);
                
		/* Find the Policy tag */
		if ($estNode->getElementsByTagName('Policy')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy tag in provided security policy XML');
			return FALSE;
		}
		$estPolicyNode = $estNode->getElementsByTagName('Policy')->item(0);
		/* Find the IssuedToken tag */
		if ($estPolicyNode->getElementsByTagName('IssuedToken')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken tag in provided security policy XML');
			return FALSE;
		}
		$issuedTokenNode = $estPolicyNode->getElementsByTagName('IssuedToken')->item(0);
		/* Find the Issuer tag */
		if ($issuedTokenNode->getElementsByTagName('Issuer')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer tag in provided security policy XML');
			return FALSE;
		}
		$issuerNode = $issuedTokenNode->getElementsByTagName('Issuer')->item(0);
		/* Find the Metadata tag */
		if ($issuerNode->getElementsByTagName('Metadata')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata tag in provided security policy XML');
			return FALSE;
		}
                
		$metadataNode = $issuerNode->getElementsByTagName('Metadata')->item(0);
		/* Find the Address tag */
		if ($metadataNode->getElementsByTagName('Address')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata/.../Address tag in provided security policy XML');
			return FALSE;
		}
		$addressNode = $metadataNode->getElementsByTagName('Address')->item(0);
                
		/* Get the URI */
		$securityURL = $addressNode->textContent;
		if ($securityURL == NULL) {
			throw new Exception('Could not find Security URL in provided security policy WSDL');
			return FALSE;
		}
		return $securityURL;
	}
        
        
        /**
	 * Get the Trust Address for the Trust13UsernameMixed authentication method 
	 * @ignore
	 */
	protected static function getTrust13UsernameAddress(DOMDocument $authenticationDOM) {
		return self::getTrustAddress($authenticationDOM, 'UserNameWSTrustBinding_IWSTrust13Async');
	}
        
        
        /**
	 * Get the Trust Address for the Trust13UsernameMixed authentication method 
	 * @ignore
	 */
	protected static function getLoginOnmicrosoftAddress1(DOMDocument $authenticationDOM) {
		$securityURL = NULL;
                
		/* Find the SignedSupportingTokens tag */
		if ($securityPolicyNode->getElementsByTagName('SignedSupportingTokens')->length == 0) {
			throw new Exception('Could not find SignedSupportingTokens tag in provided security policy XML');
			return FALSE;
		}
		$estNode = $securityPolicyNode->getElementsByTagName('SignedSupportingTokens')->item(0);
                
		/* Find the Policy tag */
		if ($estNode->getElementsByTagName('Policy')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy tag in provided security policy XML');
			return FALSE;
		}
		$estPolicyNode = $estNode->getElementsByTagName('Policy')->item(0);
		/* Find the IssuedToken tag */
		if ($estPolicyNode->getElementsByTagName('IssuedToken')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken tag in provided security policy XML');
			return FALSE;
		}
		$issuedTokenNode = $estPolicyNode->getElementsByTagName('IssuedToken')->item(0);
		/* Find the Issuer tag */
		if ($issuedTokenNode->getElementsByTagName('Issuer')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer tag in provided security policy XML');
			return FALSE;
		}
		$issuerNode = $issuedTokenNode->getElementsByTagName('Issuer')->item(0);
		/* Find the Metadata tag */
		if ($issuerNode->getElementsByTagName('Metadata')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata tag in provided security policy XML');
			return FALSE;
		}
                
                if ($issuerNode->getElementsByTagName('Address')->length == 0) {
			throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Address tag in provided security policy XML');
			return FALSE;
		}
                
                $loginAddressNode = $issuerNode->getElementsByTagName('Address')->item(0);
                
                /* get the URI */
                $securityURL = $loginAddressNode->textContent;
                
		if ($securityURL == NULL) {
			throw new Exception('Could not find Security URL in provided security policy WSDL');
			return FALSE;
		}
		return $securityURL;
	}
	
	/**
	 * Search the WSDL from an ADFS server to find the correct end-point for a 
	 * call to RequestSecurityToken with a given set of parmameters 
	 * @ignore
	 */
	protected static function getTrustAddress(DOMDocument $authenticationDOM, $trustName) {
            
		/* Search the available Ports on the WSDL */
		$trustAuthNode = NULL;
		foreach ($authenticationDOM->getElementsByTagName('port') as $portNode) {
			if ($portNode->hasAttribute('name') && $portNode->getAttribute('name') == $trustName) {
				$trustAuthNode = $portNode;
				break;
			}
		}
		if ($trustAuthNode == NULL) {
			throw new Exception('Could not find Port for trust type <'.$trustName.'> in provided WSDL');
			return FALSE;
		}
		/* Get the Address from the Port */
		$authenticationURI = NULL;
		if ($trustAuthNode->getElementsByTagName('address')->length > 0) {
			$authenticationURI = $trustAuthNode->getElementsByTagName('address')->item(0)->getAttribute('location');
		}
		if ($authenticationURI == NULL) {
			throw new Exception('Could not find Address for trust type <'.$trustName.'> in provided WSDL');
			return FALSE;
		}
		/* Return the found URI */
		return $authenticationURI;
	}
        
        
        /**
	 * Search a WSDL XML DOM for "import" tags and import the files into 
	 * one large DOM for the entire WSDL structure 
	 * @ignore
	 */
	protected function mergeWSDLImports(DOMNode &$wsdlDOM, $continued = false, DOMDocument &$newRootDocument = NULL) {
		static $rootNode = NULL;
		static $rootDocument = NULL;
		/* If this is an external call, find the "root" defintions node */
		if ($continued == false) {
			$rootNode = $wsdlDOM->getElementsByTagName('definitions')->item(0);
			$rootDocument = $wsdlDOM;
		}
		if ($newRootDocument == NULL) $newRootDocument = $rootDocument;
		//if (self::$debugMode) echo "Processing Node: ".$wsdlDOM->nodeName." which has ".$wsdlDOM->childNodes->length." child nodes".PHP_EOL;
		$nodesToRemove = Array();
		/* Loop through the Child nodes of the provided DOM */
		foreach ($wsdlDOM->childNodes as $childNode) {
			//if (self::$debugMode) echo "\tProcessing Child Node: ".$childNode->nodeName." (".$childNode->localName.") which has ".$childNode->childNodes->length." child nodes".PHP_EOL;
			/* If this child is an IMPORT node, get the referenced WSDL, and remove the Import */
			if ($childNode->localName == 'import') {
				/* Get the location of the imported WSDL */
				if ($childNode->hasAttribute('location')) {
					$importURI = $childNode->getAttribute('location');
				} else if ($childNode->hasAttribute('schemaLocation')) {
					$importURI = $childNode->getAttribute('schemaLocation');
				} else {
					$importURI = NULL;
				}
				/* Only import if we found a URI - otherwise, don't change it! */
				if ($importURI != NULL) {
					if (self::$debugMode) echo "\tImporting data from: ".$importURI.PHP_EOL;
					$importDOM = new DOMDocument();
					@$importDOM->load($importURI);
					/* Find the "Definitions" on this imported node */
					$importDefinitions = $importDOM->getElementsByTagName('definitions')->item(0);
					/* If we have "Definitions", import them one by one - Otherwise, just import at this level */
					if ($importDefinitions != NULL) {
						/* Add all the attributes (namespace definitions) to the root definitions node */
						foreach ($importDefinitions->attributes as $attribute) {
							/* Don't copy the "TargetNamespace" attribute */
							if ($attribute->name != 'targetNamespace') {
								$rootNode->setAttributeNode($attribute);
							}
						}
						$this->mergeWSDLImports($importDefinitions, true, $importDOM);
						foreach ($importDefinitions->childNodes as $importNode) {
							//if (self::$debugMode) echo "\t\tInserting Child: ".$importNode->C14N(true).PHP_EOL;
							$importNode = $newRootDocument->importNode($importNode, true);
							$wsdlDOM->insertBefore($importNode, $childNode);
						}
					} else {
						//if (self::$debugMode) echo "\t\tInserting Child: ".$importNode->C14N(true).PHP_EOL;
						$importNode = $newRootDocument->importNode($importDOM->firstChild, true);
						$wsdlDOM->insertBefore($importNode, $childNode);
					}
					//if (self::$debugMode) echo "\t\tRemoving Child: ".$childNode->C14N(true).PHP_EOL;
					$nodesToRemove[] = $childNode;
				}
			} else {
				//if (self::$debugMode) echo 'Preserving node: '.$childNode->localName.PHP_EOL;
				if ($childNode->hasChildNodes()) {
					$this->mergeWSDLImports($childNode, true);
				}
			}
		}
		/* Actually remove the nodes (not done in the loop, as it messes up the ForEach pointer!) */
		foreach ($nodesToRemove as $node) {
			$wsdlDOM->removeChild($node);
		}
		return $wsdlDOM;
	}
        
        
        /**
	 * Parse the results of a RetrieveEntity into a useable PHP object
	 * @ignore
	 */
	protected static function parseRetrieveEntityResponse($soapResponse) {
            
		/* Load the XML into a DOMDocument */
		$soapResponseDOM = new DOMDocument();
		$soapResponseDOM->loadXML($soapResponse);
		/* Find the ExecuteResult node with Type b:RetrieveRecordChangeHistoryResponse */
		$executeResultNode = NULL;
		foreach ($soapResponseDOM->getElementsByTagName('ExecuteResult') as $node) {
			if ($node->hasAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type') && self::stripNS($node->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type')) == 'RetrieveEntityResponse') {
				$executeResultNode = $node;
				break;
			}
		}
		unset($node);
		if ($executeResultNode == NULL) {
			throw new Exception('Could not find ExecuteResult for RetrieveEntityResponse in XML provided');
			return FALSE;
		}
		/* Find the Value node with Type d:EntityMetadata */
		$entityMetadataNode = NULL;
		foreach ($executeResultNode->getElementsByTagName('value') as $node) {
			if ($node->hasAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type') && self::stripNS($node->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type')) == 'EntityMetadata') {
				$entityMetadataNode = $node;
				break;
			}
		}
		unset($node);
		if ($entityMetadataNode == NULL) {
			throw new Exception('Could not find returned EntityMetadata in XML provided');
			return FALSE;
		}
                
		/* Assemble a simpleXML class for the details to return  NOTE: always return false for some reason */
		//$responseData = simplexml_import_dom($entityMetadataNode);
                
                $returnValue = preg_replace('/(<)([a-z]:)/', '<', preg_replace('/(<\/)([a-z]:)/', '</', $soapResponse));

                $simpleXML = simplexml_load_string($returnValue);

                if (!$simpleXML){
                    throw new Exception('Unable to load metadata simple_xml_class');
                    return FALSE;
                }
                
                $responseData = $simpleXML->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value;

                if (!$responseData){
                    throw new Exception('Unable to load metadata simple_xml_class KeyValuePairOfstringanyType value');
                    return FALSE;
                }
		
		/* Return the SimpleXML object */
		return $responseData;
	}
        
        
        /**
	 * Parse the results of a RetrieveEntity into a useable PHP object
	 * @ignore
	 */
	public static function parseRetrieveAllEntitiesResponse($soapResponse) {
            
		/* Load the XML into a DOMDocument */
		$soapResponseDOM = new DOMDocument();
		$soapResponseDOM->loadXML($soapResponse);
		/* Find the ExecuteResult node with Type b:RetrieveRecordChangeHistoryResponse */
		$executeResultNode = NULL;
		foreach ($soapResponseDOM->getElementsByTagName('ExecuteResult') as $node) {
			if ($node->hasAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type') && 
                                (self::stripNS($node->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type')) == 'RetrieveEntityResponse'
                                ) || (self::stripNS($node->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type')) == 'RetrieveAllEntitiesResponse')) {
				$executeResultNode = $node;
				break;
			}
		}
		unset($node);
		if ($executeResultNode == NULL) {
			throw new Exception('Could not find ExecuteResult for RetrieveEntityResponse in XML provided');
			return FALSE;
		}
		/* Find the Value node with Type d:EntityMetadata */
		$entityMetadataNode = NULL;
		foreach ($executeResultNode->getElementsByTagName('value') as $node) {
                    
			//if ($node->hasAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type') ) {
				$entityMetadataNode = $node;
				break;
			//}
		}
		unset($node);
		if ($entityMetadataNode == NULL) {
			throw new Exception('Could not find returned EntityMetadata in XML provided');
			return FALSE;
		}
                
		/* Assemble a simpleXML class for the details to return  NOTE: always return false for some reason */
		//$responseData = simplexml_import_dom($entityMetadataNode);
                
                $returnValue = preg_replace('/(<)([a-z]:)/', '<', preg_replace('/(<\/)([a-z]:)/', '</', $soapResponse));

                $simpleXML = simplexml_load_string($returnValue);

                if (!$simpleXML){
                    throw new Exception('Unable to load metadata simple_xml_class');
                    return FALSE;
                }
                
                $responseData = $simpleXML->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value;

                if (!$responseData){
                    throw new Exception('Unable to load metadata simple_xml_class KeyValuePairOfstringanyType value');
                    return FALSE;
                }
		
		/* Return the SimpleXML object */
		return $responseData;
	}
        
        /**
	 * Parse the results of a RetrieveRequest into a useable PHP object
	 * @param AlexaSDK $conn
	 * @param String $entityLogicalName
	 * @param String $soapResponse
	 * @ignore
	 */
	protected static function parseRetrieveResponse(AlexaSDK $conn, $entityLogicalName, $soapResponse) {
		/* Load the XML into a DOMDocument */
		$soapResponseDOM = new DOMDocument();
		$soapResponseDOM->loadXML($soapResponse);
		/* Find the RetrieveResponse */
		$retrieveResponseNode = NULL;
		foreach ($soapResponseDOM->getElementsByTagName('RetrieveResponse') as $node) {
			$retrieveResponseNode = $node;
			break;
		}
		unset($node);
		if ($retrieveResponseNode == NULL) {
			throw new Exception('Could not find RetrieveResponse node in XML provided');
			return FALSE;
		}
		/* Find the RetrieveResult node */
		$retrieveResultNode = NULL;
		foreach ($retrieveResponseNode->getElementsByTagName('RetrieveResult') as $node) {
			$retrieveResultNode = $node;
			break;
		}
		unset($node);
		if ($retrieveResultNode == NULL) {
			throw new Exception('Could not find RetrieveResult node in XML provided');
			return FALSE;
		}
                		
		/* Generate a new Entity from the DOMNode */
		$entity = AlexaSDK_Entity::fromDOM($conn, $entityLogicalName, $retrieveResultNode);
		return $entity;
	}
        
        
        /**
	 * Utility function to validate the security details for the selected service
	 * @return boolean indicator showing if the security details are okay
	 * @ignore
	 */
	private function checkSecurity($service) {
                
                if (!isset($this->security[$service.'_authmode'])){
                        $this->security[$service.'_authmode'] = $this->settings->authMode;
                }
            
                if ($this->security[$service.'_authmode'] == NULL ) return FALSE;
		switch ($this->security[$service.'_authmode']) {
			case 'Federation':
				return $this->checkFederationSecurity($service);
				break;
                        case 'OnlineFederation':
				return $this->checkOnlineFederationSecurity($service);
				break;
		}
		return FALSE;
	}
	
	/**
	 * Utility function to validate Federation security details for the selected service
	 * Checks the Authentication Mode is Federation, and verifies all the necessary data exists
	 * @return boolean indicator showing if the security details are okay
	 * @ignore
	 */
	private function checkFederationSecurity($service) {
		if ($this->security[$service.'_authmode'] != 'Federation') return FALSE;
		if ($this->security[$service.'_authuri'] == NULL) return FALSE;
		if ($this->security[$service.'_authendpoint'] == NULL) return FALSE;
		if ($this->security['username'] == NULL || $this->security['password'] == NULL) {
			return FALSE;
		}
		return TRUE;
	}
        
        /**
	 * Utility function to validate Federation security details for the selected service
	 * Checks the Authentication Mode is Federation, and verifies all the necessary data exists
	 * @return boolean indicator showing if the security details are okay
	 * @ignore
	 */
	private function checkOnlineFederationSecurity($service) {
		if ($this->security[$service.'_authmode'] != 'OnlineFederation') return FALSE;
		if ($this->security[$service.'_authuri'] == NULL) return FALSE;
		if ($this->security[$service.'_authendpoint'] == NULL) return FALSE;
		if ($this->security['username'] == NULL || $this->security['password'] == NULL) {
			return FALSE;
		}
		return TRUE;
	}
        
        
        /**
	 * Get the SOAP Endpoint for the Federation Security service 
	 * @ignore
	 */
	public function getFederationSecurityURI($service) {
		/* If it's set, return the details from the Security array */
		if (isset($this->security[$service.'_authendpoint'])) 
			return $this->security[$service.'_authendpoint'];
		
		/* Fetch the WSDL for the Authentication Service as a parseable DOM Document */
		if (self::$debugMode) echo 'Getting WSDL data for Federation Security URI from: '.$this->security[$service.'_authuri'].PHP_EOL;
		$authenticationDOM = new DOMDocument();
		@$authenticationDOM->load($this->security[$service.'_authuri']);
		/* Flatten the WSDL and include all the Imports */
		$this->mergeWSDLImports($authenticationDOM);
		
		// Note: Find the real end-point to use for my security request - for now, we hard-code to Trust13 Username & Password using known values
		// See http://code.google.com/p/php-dynamics-crm-2011/issues/detail?id=4
		$authEndpoint = self::getTrust13UsernameAddress($authenticationDOM);
		return $authEndpoint;
	}
        
                
        /**
	 * Get the SOAP Endpoint for the OnlineFederation Security service 
	 * @ignore
	 */
	protected function getOnlineFederationSecurityURI($service) {
                /* If it's set, return the details from the Security array */
		if (isset($this->security[$service.'_authendpoint'])) 
			return $this->security[$service.'_authendpoint'];
		
		/* Fetch the WSDL for the Authentication Service as a parseable DOM Document */
		if (self::$debugMode) echo 'Getting WSDL data for OnlineFederation Security URI from: '.$this->security[$service.'_authuri'].PHP_EOL;
		$authenticationDOM = new DOMDocument();
		@$authenticationDOM->load($this->security[$service.'_authuri']);
		/* Flatten the WSDL and include all the Imports */
		$this->mergeWSDLImports($authenticationDOM);
                
                $authEndpoint = self::getLoginOnmicrosoftAddress($authenticationDOM);
                
		return $authEndpoint;
	}
        
        
        /**
	 * Search a Microsoft Dynamics CRM 2011 WSDL for the Security Policy for a given Service
	 * @ignore
	 */
	protected static function findSecurityPolicy(DOMDocument $wsdlDocument, $serviceName) {
		/* Find the selected Service definition from the WSDL */
		$selectedServiceNode = NULL;
                
		foreach ($wsdlDocument->getElementsByTagName('service') as $serviceNode) {
			if ($serviceNode->hasAttribute('name') && $serviceNode->getAttribute('name') == $serviceName) {
				$selectedServiceNode = $serviceNode;
				break;
			}
		}
		if ($selectedServiceNode == NULL) {
			throw new Exception('Could not find definition of Service <'.$serviceName.'> in provided WSDL');
			return FALSE;
		}
		/* Now find the Binding for the Service */
		$bindingName = NULL;
		foreach ($selectedServiceNode->getElementsByTagName('port') as $portNode) {
			if ($portNode->hasAttribute('name')) {
				$bindingName = $portNode->getAttribute('name');
				break;
			}
		}
		if ($bindingName == NULL) {
			throw new Exception('Could not find binding for Service <'.$serviceName.'> in provided WSDL');
			return FALSE;
		}
		/* Find the Binding definition from the WSDL */
		$bindingNode = NULL;
		foreach ($wsdlDocument->getElementsByTagName('binding') as $bindingNode) {
			if ($bindingNode->hasAttribute('name') && $bindingNode->getAttribute('name') == $bindingName) {
				break;
			}
		}
		if ($bindingNode == NULL) {
			throw new Exception('Could not find defintion of Binding <'.$bindingName.'> in provided WSDL');
			return FALSE;
		}
		/* Find the Policy Reference */
		$policyReferenceURI = NULL;
		foreach ($bindingNode->getElementsByTagName('PolicyReference') as $policyReferenceNode) {
			if ($policyReferenceNode->hasAttribute('URI')) {
				/* Strip the leading # from the PolicyReferenceURI to get the ID */
				$policyReferenceURI = substr($policyReferenceNode->getAttribute('URI'), 1);
				break;
			}
		}
		if ($policyReferenceURI == NULL) {
			throw new Exception('Could not find Policy Reference for Binding <'.$bindingName.'> in provided WSDL');
			return FALSE;
		}
		/* Find the Security Policy from the WSDL */
		$securityPolicyNode = NULL;
		foreach ($wsdlDocument->getElementsByTagName('Policy') as $policyNode) {
			if ($policyNode->hasAttribute('wsu:Id') && $policyNode->getAttribute('wsu:Id') == $policyReferenceURI) {
				$securityPolicyNode = $policyNode;
				break;
			}
		}
		if ($securityPolicyNode == NULL) {
			throw new Exception('Could not find Policy with ID <'.$policyReferenceURI.'> in provided WSDL');
			return FALSE;
		}
		/* Return the selected node */
		return $securityPolicyNode;
	}
        
        
        public function getOnlineFederationOrganizationURI(){
            
            $request = $this->authentication->requestRetrieveOrganization();
            
            $discovery_data = self::GetSOAPResponse($this->settings->discoveryUrl, $request);
            
            /* Parse the returned data to determine the correct EndPoint for the OrganizationService for the selected Organization */
            $organizationServiceURI = NULL;
            $organizationDomain = NULL;
            $discoveryDOM = new DOMDocument(); $discoveryDOM->loadXML($discovery_data);
            if ($discoveryDOM->getElementsByTagName('OrganizationDetail')->length > 0) {
                    foreach ($discoveryDOM->getElementsByTagName('OrganizationDetail') as $organizationNode) {
                            //if ($organizationNode->getElementsByTagName('UniqueName')->item(0)->textContent == $this->organizationUniqueName) {
                                    foreach ($organizationNode->getElementsByTagName('Endpoints')->item(0)->getElementsByTagName('KeyValuePairOfEndpointTypestringztYlk6OT') as $endpointDOM) {
                                            if ($endpointDOM->getElementsByTagName('key')->item(0)->textContent == 'OrganizationService') {
                                                    $organizationServiceURI = $endpointDOM->getElementsByTagName('value')->item(0)->textContent;
                                            }

                                            if ($endpointDOM->getElementsByTagName('key')->item(0)->textContent == 'WebApplication') {
                                                    $organizationDomain = $endpointDOM->getElementsByTagName('value')->item(0)->textContent;
                                            }
                                    }
                                    break;
                            //}
                    }
            } else {
                    throw new Exception('Error fetching Organization details:'.PHP_EOL.$discovery_data);
                    return FALSE;
            }
            if ($organizationServiceURI == NULL) {
                    throw new Exception('Could not find OrganizationService URI for the Organization <'.$this->organizationUniqueName.'>');
                    return FALSE;
            }
            
            $this->domain = $organizationDomain;
            $this->organizationUrl = $organizationServiceURI;
            //$this->cacheOrganizationDetails();
            return $organizationServiceURI;
        }
        
        
        /**
	 * Fetch and flatten the Organization Service WSDL as a DOM
	 * @ignore
	 */
	public function getOrganizationDOM() {
		/* If it's already been fetched, use the one we have */
		if ($this->organizationDOM != NULL) return $this->organizationDOM;
		if ($this->settings->organizationUrl == NULL) {
			throw new Exception('Cannot get Organization DOM before determining Organization URI');
		}
		
		/* Fetch the WSDL for the Organization Service as a parseable DOM Document */
		if (self::$debugMode) echo 'Getting WSDL data for Organization DOM from: '.$this->organizationUrl.'?wsdl'.PHP_EOL;
		$organizationDOM = new DOMDocument(); 
		@$organizationDOM->load($this->settings->organizationUrl.'?wsdl');
		/* Flatten the WSDL and include all the Imports */
		$this->mergeWSDLImports($organizationDOM);
		
		/* Cache the DOM in the current object */
		$this->organizationDOM = $organizationDOM;
                
		return $organizationDOM;
	}

        
        /** 
	 * Generate a Retrieve Entity Request
	 * @ignore
	 */
	protected static function generateRetrieveEntityRequest($entityType, $entityId = NULL, $entityFilters = NULL, $showUnpublished = false) {
		/* We can use either the entityType (Logical Name), or the entityId, but not both. */
		/* Use ID by preference, if not set, default to 0s */
		if ($entityId != NULL) $entityType = NULL;
		else $entityId = self::EmptyGUID;
		
		/* If no entityFilters are supplied, assume "All" */
		if ($entityFilters == NULL) $entityFilters = 'Entity Attributes Privileges Relationships';
		
		/* Generate the RetrieveEntityRequest message */
		$retrieveEntityRequestDOM = new DOMDocument();
		$executeNode = $retrieveEntityRequestDOM->appendChild($retrieveEntityRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Execute'));
		$requestNode = $executeNode->appendChild($retrieveEntityRequestDOM->createElement('request'));
		$requestNode->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'i:type', 'b:RetrieveEntityRequest');
		$requestNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts');
		$parametersNode = $requestNode->appendChild($retrieveEntityRequestDOM->createElement('b:Parameters'));
		$parametersNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic');
		/* EntityFilters */
		$keyValuePairNode1 = $parametersNode->appendChild($retrieveEntityRequestDOM->createElement('b:KeyValuePairOfstringanyType'));
		$keyValuePairNode1->appendChild($retrieveEntityRequestDOM->createElement('c:key', 'EntityFilters'));
		$valueNode1 = $keyValuePairNode1->appendChild($retrieveEntityRequestDOM->createElement('c:value', $entityFilters));
		$valueNode1->setAttribute('i:type', 'd:EntityFilters');
		$valueNode1->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/2011/Metadata');
		/* MetadataId */
		$keyValuePairNode2 = $parametersNode->appendChild($retrieveEntityRequestDOM->createElement('b:KeyValuePairOfstringanyType'));
		$keyValuePairNode2->appendChild($retrieveEntityRequestDOM->createElement('c:key', 'MetadataId'));
		$valueNode2 = $keyValuePairNode2->appendChild($retrieveEntityRequestDOM->createElement('c:value', $entityId));
		$valueNode2->setAttribute('i:type', 'd:guid');
		$valueNode2->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/2003/10/Serialization/');
		/* RetrieveAsIfPublished */
		$keyValuePairNode3 = $parametersNode->appendChild($retrieveEntityRequestDOM->createElement('b:KeyValuePairOfstringanyType'));
		$keyValuePairNode3->appendChild($retrieveEntityRequestDOM->createElement('c:key', 'RetrieveAsIfPublished'));
		$valueNode3 = $keyValuePairNode3->appendChild($retrieveEntityRequestDOM->createElement('c:value', ($showUnpublished?'true':'false')));
		$valueNode3->setAttribute('i:type', 'd:boolean');
		$valueNode3->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://www.w3.org/2001/XMLSchema');
		/* LogicalName */
		$keyValuePairNode4 = $parametersNode->appendChild($retrieveEntityRequestDOM->createElement('b:KeyValuePairOfstringanyType'));
		$keyValuePairNode4->appendChild($retrieveEntityRequestDOM->createElement('c:key', 'LogicalName'));
		$valueNode4 = $keyValuePairNode4->appendChild($retrieveEntityRequestDOM->createElement('c:value', $entityType));
		$valueNode4->setAttribute('i:type', 'd:string');
		$valueNode4->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://www.w3.org/2001/XMLSchema');
		/* Request ID and Name */
		$requestNode->appendChild($retrieveEntityRequestDOM->createElement('b:RequestId'))->setAttribute('i:nil', 'true');
		$requestNode->appendChild($retrieveEntityRequestDOM->createElement('b:RequestName', 'RetrieveEntity'));
		/* Return the DOMNode */
		return $executeNode;
	}
        
        
        /**
	 * Send a RetrieveEntity request to the Dynamics CRM 2011 server and return the results as a structured Object
	 *
	 * @param string $entityType the LogicalName of the Entity to be retrieved (Incident, Account etc.)
	 * @param string $entityId the internal Id of the Entity to be retrieved (without enclosing brackets)
	 * @param array $columnSet array listing all fields to be fetched, or null to get all columns
	 * @return stdClass a PHP Object containing all the data retrieved.
	 */
	public function retrieveEntity($entityType, $entityId = NULL, $entityFilters = NULL, $showUnpublished = false) {
		/* Get the raw XML data */
		$rawSoapResponse = $this->retrieveEntityRaw($entityType, $entityId, $entityFilters, $showUnpublished);
                
		/* Parse the raw XML data into an Object */
		$soapData = self::parseRetrieveEntityResponse($rawSoapResponse);
                
		/* Return the structured object */
		return $soapData;
	}
        
        /**
	 * Send a RetrieveEntity request to the Dynamics CRM server and return the results as raw XML
	 *
	 * This is particularly useful when debugging the responses from the server
	 * 
	 * @param string $entityType the LogicalName of the Entity to be retrieved (Incident, Account etc.)
	 * @return string the raw XML returned by the server, including all SOAP Envelope, Header and Body data.
	 */
	public function retrieveEntityRaw($entityType, $entityId = NULL, $entityFilters = NULL, $showUnpublished = false) {
		/* Send the sequrity request and get a security token */
		$securityToken = $this->authentication->getOrganizationSecurityToken();
		/* Generate the XML for the Body of a RetrieveEntity request */
		$executeNode = self::generateRetrieveEntityRequest($entityType, $entityId, $entityFilters, $showUnpublished);
		/* Turn this into a SOAP request, and send it */
		$retrieveEntityRequest = $this->generateSoapRequest($this->settings->organizationUrl, $this->getOrganizationExecuteAction(), $securityToken, $executeNode);
		$soapResponse = self::getSoapResponse($this->settings->organizationUrl, $retrieveEntityRequest);
		
		return $soapResponse;
	}
        
        /**
	 * Send a RetrieveMultipleEntities request to the Dynamics CRM server
	 * and return the results as a structured Object
	 * Each Entity returned is processed into an appropriate AlexaSDK_Entity object
	 *
	 * @param string $entityType logical name of entities to retrieve
	 * @param boolean $allPages indicates if the query should be resent until all possible data is retrieved
	 * @param string $pagingCookie if multiple pages are returned, send the paging cookie to get pages 2 and onwards.  Use NULL to get the first page.  Ignored if $allPages is specified.
	 * @param integer $limitCount maximum number of records to be returned per page
	 * @param boolean $simpleMode indicates if we should just use stdClass, instead of creating Entities
	 * @return stdClass a PHP Object containing all the data retrieved.
	 */
        public function retrieveMultipleEntities($entityType, $allPages = TRUE, $pagingCookie = NULL, $limitCount = NULL, $pageNumber = NULL, $simpleMode = FALSE){
                $queryXML = new DOMDocument();
                $fetch = $queryXML->appendChild($queryXML->createElement('fetch'));
                $fetch->setAttribute('version', '1.0');
                $fetch->setAttribute('output-format', 'xml-platform');
                $fetch->setAttribute('mapping', 'logical');
                $fetch->setAttribute('distinct', 'false');
                $entity = $fetch->appendChild($queryXML->createElement('entity'));
                $entity->setAttribute('name', $entityType);
                $entity->appendChild($queryXML->createElement('all-attributes'));
                $queryXML->saveXML($fetch);

                return $this->retrieveMultiple($queryXML->C14N(), $allPages, $pagingCookie, $limitCount, $pageNumber, $simpleMode);
        }
        
        
        /**
	 * Send a Retrieve request to the Dynamics CRM 2011 server and return the results as raw XML
	 * This function is typically used just after creating something (where you get the ID back
	 * as the return value), as it is more efficient to use RetrieveMultiple to search directly if 
	 * you don't already have the ID.
	 *
	 * This is particularly useful when debugging the responses from the server
	 * 
	 * @param AlexaSDK_Entity $entity the Entity to retrieve - must have an ID specified
	 * @param array $fieldSet array listing all fields to be fetched, or null to get all fields
	 * @return string the raw XML returned by the server, including all SOAP Envelope, Header and Body data.
	 */
	public function retrieveRaw(AlexaSDK_Entity $entity, $fieldSet = NULL) {
		/* Determine the Type & ID of the Entity */
		$entityType = $entity->LogicalName;
		$entityId = $entity->ID;
		/* Send the sequrity request and get a security token */
		$securityToken = $this->authentication->getOrganizationSecurityToken();
		/* Generate the XML for the Body of a RetrieveRecordChangeHistory request */
		$executeNode = self::generateRetrieveRequest($entityType, $entityId, $fieldSet);
		/* Turn this into a SOAP request, and send it */
		$retrieveRequest = $this->generateSoapRequest($this->settings->organizationUrl, $this->getOrganizationRetrieveAction(), $securityToken, $executeNode);
		$soapResponse = self::getSoapResponse($this->settings->organizationUrl, $retrieveRequest);
		
		return $soapResponse;
	}
        
        
        /**
	 * Utility function to get the SoapAction for the Execute method of the Organization Service
	 * @ignore
	 */
	private function getOrganizationExecuteAction() {
		/* If it's not cached, update the cache */
		if ($this->organizationExecuteAction == NULL) {
			$actions = $this->getAllOrganizationSoapActions();
			$this->organizationExecuteAction = $actions['Execute'];
		}
		
		return $this->organizationExecuteAction;
	}
        
        
        /** 
	 * Get all the Operations & corresponding SoapActions for the OrganizationService
	 */
	public function getAllOrganizationSoapActions() {
		/* If it is not cached, update the cache */
		if ($this->organizationSoapActions == NULL) {
			$this->organizationSoapActions = self::getAllSoapActions($this->getOrganizationDOM(), 'OrganizationService');
		}
		/* Return the cached value */
		return $this->organizationSoapActions;
	}
        
        
        
        /**
	 * Get the Discovery URL which is currently in use
	 * @return string the URL of the Organization s$this->organizationUniqueNameervice
	 * @throws Exception if the Discovery Service security details have not been set, 
	 * or the Organization Service URL cannot be found for the current Organization
	 */
	public function getOrganizationURI() {
		/* If it's set, return the details from the class instance */
		if ($this->settings->organizationUrl != NULL) return $this->settings->organizationUrl;
		
		/* Check we have the appropriate security details for the Discovery Service */
		if ($this->checkSecurity('discovery') == FALSE)
			throw new Exception('Cannot determine Organization URI before Discovery Service Security Details are set!');
		
		/* Request a Security Token for the Discovery Service */
		$securityToken = $this->authentication->requestSecurityToken($this->security['discovery_authendpoint'], $this->settings->discoveryUrl, $this->security['username'], $this->security['password']);
		
		/* Determine the Soap Action for the Execute method of the Discovery Service */
		$discoveryServiceSoapAction = $this->getDiscoveryExecuteAction();
		
		/* Generate a Soap Request for the Retrieve Organization Request method of the Discovery Service */
		$discoverySoapRequest = $this->generateSoapRequest($this->settings->discoveryUrl, $discoveryServiceSoapAction, $securityToken, self::generateRetrieveOrganizationRequest());
                
		$discovery_data = self::getSoapResponse($this->settings->discoveryUrl, $discoverySoapRequest);
		
                $this->organizationUniqueName = "wpsdk";
                
		/* Parse the returned data to determine the correct EndPoint for the OrganizationService for the selected Organization */
		$organizationServiceURI = NULL;
                $organizationDomain = NULL;
		$discoveryDOM = new DOMDocument(); $discoveryDOM->loadXML($discovery_data);
		if ($discoveryDOM->getElementsByTagName('OrganizationDetail')->length > 0) {
			foreach ($discoveryDOM->getElementsByTagName('OrganizationDetail') as $organizationNode) {
				//if ($organizationNode->getElementsByTagName('UniqueName')->item(0)->textContent == $this->organizationUniqueName) {
					foreach ($organizationNode->getElementsByTagName('Endpoints')->item(0)->getElementsByTagName('KeyValuePairOfEndpointTypestringztYlk6OT') as $endpointDOM) {
						if ($endpointDOM->getElementsByTagName('key')->item(0)->textContent == 'OrganizationService') {
							$organizationServiceURI = $endpointDOM->getElementsByTagName('value')->item(0)->textContent;
						}
                                                
                                                if ($endpointDOM->getElementsByTagName('key')->item(0)->textContent == 'WebApplication') {
							$organizationDomain = $endpointDOM->getElementsByTagName('value')->item(0)->textContent;
						}
					}
					break;
				//}
			}
		} else {
			throw new Exception('Error fetching Organization details:'.PHP_EOL.$discovery_data);
			return FALSE;
		}
		if ($organizationServiceURI == NULL) {
			throw new Exception('Could not find OrganizationService URI for the Organization <'.$this->organizationUniqueName.'>');
			return FALSE;
		}
                
                $this->domain = $organizationDomain;
		$this->organizationUrl = $organizationServiceURI;
		//$this->cacheOrganizationDetails();
		return $organizationServiceURI;
	}
        
        /**
	 * Get the connector timeout value
	 * @return int the maximum time the connector will wait for a response from the CRM in seconds
	 */
	public static function getConnectorTimeout() {
		return self::$connectorTimeout;
	}
	
	/**
	 * Set the connector timeout value
	 * @param int $_connectorTimeout maximum time the connector will wait for a response from the CRM in seconds
	 */
	public static function setConnectorTimeout($_connectorTimeout) {
		if (!is_int($_connectorTimeout)) return;
		self::$connectorTimeout = $_connectorTimeout;
	}
        
        /**
	 * Get the cache time value
	 * @return int cache data lifetime in seconds
	 */
	public static function getCacheTime() {
		return self::$cacheTime;
	}
        
        /**
	 * Set the cache time value
	 * @param int $_cacheTime cache data lifetime in seconds
	 */
	public static function setCacheTime($_cacheTime) {
		if (!is_int($_cacheTime)) return;
		self::$cacheTime = $_cacheTime;
	}
        
        /**
	 * Get the Discovery URL which is currently in use
	 * @return string the URL of the Discovery Service
	 */
	public function getDiscoveryURI() {
		return $this->discoveryURI;
	}
	
	/**
	 * Get the Organization Unique Name which is currently in use
	 * @return string the Unique Name of the Organization
	 */
	public function getOrganization() {
		return $this->organizationUniqueName;
	}
	
	/**
	 * Get the maximum records for a query
	 * @return int the maximum records that will be returned from RetrieveMultiple per page
	 */
	public static function getMaximumRecords() {
		return self::$maximumRecords;
	}
	
	/**
	 * Set the maximum records for a query
	 * @param int $_maximumRecords the maximum number of records to fetch per page
	 */
	public static function setMaximumRecords($_maximumRecords) {
		if (!is_int($_maximumRecords)) return;
		self::$maximumRecords = $_maximumRecords;
	}
	
        
        /* NEED TO REFACTOR
         * SEE GetSOAPResponse
         */
        private static function formatHeaders($url, $request, $type = "POST") {
            $scheme = parse_url($url);
            
            $headers = array(
                $type . " " . $scheme["path"] . " HTTP/1.1",
                "Host: " . $scheme["host"],
                'Connection: Keep-Alive',
                "Content-type: application/soap+xml; charset=UTF-8",
                "Content-length: " . strlen($request),
            );

            return $headers;
        }
        
        
        /**
	 * Request a Security Token from the ADFS server using Username & Password authentication 
	 * @ignore
	 */
	protected function requestSecurityToken($securityServerURI, $loginEndpoint, $loginUsername, $loginPassword) {
                //$securityServerURI = "https://login.microsoftonline.com/RST2.srf";
            
		/* Generate the Security Token Request XML */
		$loginSoapRequest = self::getLoginXML($securityServerURI, $loginEndpoint, $loginUsername, $loginPassword);
		/* Send the Security Token request */
                
		$security_xml = self::getSoapResponse($securityServerURI, $loginSoapRequest);
                
		/* Convert the XML into a DOMDocument */
		$securityDOM = new DOMDocument();
		$securityDOM->loadXML($security_xml);
		/* Get the two CipherValue keys */
		$cipherValues = $securityDOM->getElementsbyTagName("CipherValue");
		$securityToken0 =  $cipherValues->item(0)->textContent;
		$securityToken1 =  $cipherValues->item(1)->textContent;
		/* Get the KeyIdentifier */
		$keyIdentifier = $securityDOM->getElementsbyTagName("KeyIdentifier")->item(0)->textContent;
		/* Get the BinarySecret */
		$binarySecret = $securityDOM->getElementsbyTagName("BinarySecret")->item(0)->textContent;
		/* Make life easier - get the entire RequestedSecurityToken section */
		$requestedSecurityToken = $securityDOM->saveXML($securityDOM->getElementsByTagName("RequestedSecurityToken")->item(0));
		preg_match('/<trust:RequestedSecurityToken>(.*)<\/trust:RequestedSecurityToken>/', $requestedSecurityToken, $matches);
		$requestedSecurityToken = $matches[1];
		/* Find the Expiry Time */
		$expiryTime = $securityDOM->getElementsByTagName("RequestSecurityTokenResponse")->item(0)->getElementsByTagName('Expires')->item(0)->textContent;
		/* Convert it to a PHP Timestamp */
		$expiryTime = self::parseTime(substr($expiryTime, 0, -5), '%Y-%m-%dT%H:%M:%S');
		
		/* Return an associative Array */
		$securityToken = Array(
				'securityToken' => $requestedSecurityToken,
				'securityToken0' => $securityToken0,
				'securityToken1' => $securityToken1,
				'binarySecret' => $binarySecret,
				'keyIdentifier' => $keyIdentifier,
				'expiryTime' => $expiryTime
			);
		/* DEBUG logging */
		if (self::$debugMode) {
			echo 'Got Security Token - Expires at: '.date('r', $securityToken['expiryTime']).PHP_EOL;
			echo "\tKey Identifier\t: ".$securityToken['keyIdentifier'].PHP_EOL;
			echo "\tSecurity Token 0\t: ".substr($securityToken['securityToken0'], 0, 25).'...'.substr($securityToken['securityToken0'], -25).' ('.strlen($securityToken['securityToken0']).')'.PHP_EOL;
			echo "\tSecurity Token 1\t: ".substr($securityToken['securityToken1'], 0, 25).'...'.substr($securityToken['securityToken1'], -25).' ('.strlen($securityToken['securityToken1']).')'.PHP_EOL;
			echo "\tBinary Secret\t: ".$securityToken['binarySecret'].PHP_EOL.PHP_EOL;
		}
		/* Return an associative Array */
		return $securityToken;
	}
	
	
	
	/**
	 * Send the SOAP message, and get the response 
	 * @ignore
         * @return string response XML
	 */
	public static function getSoapResponse($soapUrl, $content, $throwException = true) {
            
		/* Separate the provided URI into Path & Hostname sections */
		$urlDetails = parse_url($soapUrl);
                
		// setup headers
		$headers = array(
				"POST ". $urlDetails['path'] ." HTTP/1.1",
				"Host: " . $urlDetails['host'],
				'Connection: Keep-Alive',
				"Content-type: application/soap+xml; charset=UTF-8",
				"Content-length: ".strlen($content),
		);
		
		$cURLHandle = curl_init();
		curl_setopt($cURLHandle, CURLOPT_URL, $soapUrl);
		curl_setopt($cURLHandle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cURLHandle, CURLOPT_TIMEOUT, self::$connectorTimeout);
		curl_setopt($cURLHandle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($cURLHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($cURLHandle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($cURLHandle, CURLOPT_POST, 1);
		curl_setopt($cURLHandle, CURLOPT_POSTFIELDS, $content);
		curl_setopt($cURLHandle, CURLOPT_HEADER, false);
		/* Execute the cURL request, get the XML response */
		$responseXML = curl_exec($cURLHandle);
		/* Check for cURL errors */
		if (curl_errno($cURLHandle) != CURLE_OK) {
			throw new Exception('cURL Error: '.curl_error($cURLHandle));
		}
		/* Check for HTTP errors */
		$httpResponse = curl_getinfo($cURLHandle, CURLINFO_HTTP_CODE);
		curl_close($cURLHandle);
                
		/* Determine the Action in the SOAP Response */
		$responseDOM = new DOMDocument();
		$responseDOM->loadXML($responseXML);
		/* Check we have a SOAP Envelope */
		if ($responseDOM->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Envelope')->length < 1) {
			throw new Exception('Invalid SOAP Response: HTTP Response '.$httpResponse.PHP_EOL.$responseXML.PHP_EOL);
		}
		/* Check we have a SOAP Header */
		if ($responseDOM->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Envelope')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Header')->length < 1) {
			throw new Exception('Invalid SOAP Response: No SOAP Header! '.PHP_EOL.$responseXML.PHP_EOL);
		}
		/* Get the SOAP Action */
		$actionString = $responseDOM->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Envelope')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Header')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2005/08/addressing', 'Action')->item(0)->textContent;
		if (self::$debugMode) echo __FUNCTION__.': SOAP Action in returned XML is "'.$actionString.'"'.PHP_EOL;
		
		/* Handle known Error Actions */
		if (in_array($actionString, self::$SOAPFaultActions) && $throwException) {
			// Get the Fault Code
			$faultCode = $responseDOM->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Envelope')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Body')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Fault')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Code')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Value')->item(0)->nodeValue;
			/* Strip any Namespace References from the fault code */
			$faultCode = self::stripNS($faultCode);
			// Get the Fault String
			$faultString = $responseDOM->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Envelope')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Body')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Fault')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Reason')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Text')->item(0)->nodeValue.PHP_EOL;
			throw new SoapFault($faultCode, $faultString);
		}
		
		return $responseXML;
	}

        
        /**
	 * Utility function to get the SoapAction for the Discovery Service
	 * @ignore
	 */
	private function getDiscoveryExecuteAction() {
		/* If it's not cached, update the cache */
		if ($this->discoveryExecuteAction == NULL) {
			$actions = $this->getAllDiscoverySoapActions();
			$this->discoveryExecuteAction = $actions['Execute'];
		}
		
		return $this->discoveryExecuteAction;
	}
        
        /** 
	 * Get all the Operations & corresponding SoapActions for the DiscoveryService 
	 */
	public function getAllDiscoverySoapActions() {
		/* If it is not cached, update the cache */
		if ($this->discoverySoapActions == NULL) {
			$this->discoverySoapActions = self::getAllSoapActions($this->getDiscoveryDOM(), 'DiscoveryService');
		}
		/* Return the cached value */
		return $this->discoverySoapActions;
	}
        
        /**
	 * Search a Microsoft Dynamics CRM WSDL for all available Operations/SoapActions on a Service
	 * @ignore
	 */
	private static function getAllSoapActions(DOMDocument $wsdlDocument, $serviceName) {
		/* Find the selected Service definition from the WSDL */
		$selectedServiceNode = NULL;
		foreach ($wsdlDocument->getElementsByTagName('service') as $serviceNode) {
			if ($serviceNode->hasAttribute('name') && $serviceNode->getAttribute('name') == $serviceName) {
				$selectedServiceNode = $serviceNode;
				break;
			}
		}
		if ($selectedServiceNode == NULL) {
			throw new Exception('Could not find definition of Service <'.$serviceName.'> in provided WSDL');
			return FALSE;
		}
		/* Now find the Binding for the Service */
		$bindingName = NULL;
		foreach ($selectedServiceNode->getElementsByTagName('port') as $portNode) {
			if ($portNode->hasAttribute('name')) {
				$bindingName = $portNode->getAttribute('name');
				break;
			}
		}
		if ($bindingName == NULL) {
			throw new Exception('Could not find binding for Service <'.$serviceName.'> in provided WSDL');
			return FALSE;
		}
		/* Find the Binding definition from the WSDL */
		$bindingNode = NULL;
		foreach ($wsdlDocument->getElementsByTagName('binding') as $bindingNode) {
			if ($bindingNode->hasAttribute('name') && $bindingNode->getAttribute('name') == $bindingName) {
				break;
			}
		}
		if ($bindingNode == NULL) {
			throw new Exception('Could not find defintion of Binding <'.$bindingName.'> in provided WSDL');
			return FALSE;
		}
		/* Array to store the list of Operations and SoapActions */
		$operationArray = Array();
		/* Find the Operations */
		foreach ($bindingNode->getElementsByTagName('operation') as $operationNode) {
			if ($operationNode->hasAttribute('name')) {
				/* Record the Name of this Operation */
				$operationName = $operationNode->getAttribute('name');
				/* Find the Operation SoapAction from the WSDL */
				foreach ($operationNode->getElementsByTagName('operation') as $soap12OperationNode) {
					if ($soap12OperationNode->hasAttribute('soapAction')) {
						/* Record the SoapAction for this Operation */
						$soapAction = $soap12OperationNode->getAttribute('soapAction');
						/* Store the soapAction in the Array */
						$operationArray[$operationName] = $soapAction;
					}
				}
				unset($soap12OperationNode);
			}
		}
		
		/* Return the array of available actions */
		return $operationArray;
	}
        
        
        /**
	 * Create the XML String for a Soap Request 
	 * @ignore
	 */
	protected function generateSoapRequest($serviceURI, $soapAction, $securityToken, DOMNode $bodyContentNode) {
		$soapRequestDOM = new DOMDocument();
		$soapEnvelope = $soapRequestDOM->appendChild($soapRequestDOM->createElementNS('http://www.w3.org/2003/05/soap-envelope', 's:Envelope'));
		$soapEnvelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://www.w3.org/2005/08/addressing');
		$soapEnvelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:u', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
		/* Get the SOAP Header */
		$soapHeaderNode = $this->generateSoapHeader($serviceURI, $soapAction, $securityToken);
		$soapEnvelope->appendChild($soapRequestDOM->importNode($soapHeaderNode, true));
		/* Create the SOAP Body */
		$soapBodyNode = $soapEnvelope->appendChild($soapRequestDOM->createElement('s:Body'));
		$soapBodyNode->appendChild($soapRequestDOM->importNode($bodyContentNode, true));
                	
		return $soapRequestDOM->saveXML($soapEnvelope);
	}
        
        /**
	 * Utility function to generate the XML for a Retrieve Organization request
	 * This XML can be sent as a SOAP message to the Discovery Service to determine all Organizations
	 * available on that service.
	 * @return DOMNode containing the XML for a RetrieveOrganizationRequest message
	 * @ignore
	 */
	protected static function generateRetrieveOrganizationRequest() {
		$retrieveOrganizationRequestDOM = new DOMDocument();
		$executeNode = $retrieveOrganizationRequestDOM->appendChild($retrieveOrganizationRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Discovery', 'Execute'));
		$requestNode = $executeNode->appendChild($retrieveOrganizationRequestDOM->createElement('request'));
		$requestNode->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'i:type', 'RetrieveOrganizationsRequest');
		$requestNode->appendChild($retrieveOrganizationRequestDOM->createElement('AccessType', 'Default'));
		$requestNode->appendChild($retrieveOrganizationRequestDOM->createElement('Release', 'Current'));
		
		return $executeNode;
	}
	
	/**
	 * Generate a Soap Header using the specified service URI and SoapAction
	 * Include the details from the Security Token for login
	 * @ignore
	 */
	protected function generateSoapHeader($serviceURI, $soapAction, $securityToken) {
		$soapHeaderDOM = new DOMDocument();
		$headerNode = $soapHeaderDOM->appendChild($soapHeaderDOM->createElement('s:Header'));
		$headerNode->appendChild($soapHeaderDOM->createElement('a:Action', $soapAction))->setAttribute('s:mustUnderstand', '1');
		$headerNode->appendChild($soapHeaderDOM->createElement('a:ReplyTo'))->appendChild($soapHeaderDOM->createElement('a:Address', 'http://www.w3.org/2005/08/addressing/anonymous'));
                $headerNode->appendChild($soapHeaderDOM->createElement('a:MessageId', 'urn:uuid:' . parent::getUuid()));
		$headerNode->appendChild($soapHeaderDOM->createElement('a:To', $serviceURI))->setAttribute('s:mustUnderstand', '1');
		$securityHeaderNode = $this->authentication->getSecurityHeaderNode($securityToken);
		$headerNode->appendChild($soapHeaderDOM->importNode($securityHeaderNode, true));
                
		return $headerNode;
	}
	
	/**
	 * Generate a DOMNode for the o:Security header required for SOAP requests 
	 * @ignore
	 */
	/*protected static function getSecurityHeaderNode(Array $securityToken) {
		$securityDOM = new DOMDocument();

		$securityHeader = $securityDOM->appendChild($securityDOM->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'o:Security'));
		$securityHeader->setAttribute('s:mustUnderstand', '1');
		$headerTimestamp = $securityHeader->appendChild($securityDOM->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd', 'u:Timestamp'));
		$headerTimestamp->setAttribute('u:Id', '_0');
		$headerTimestamp->appendChild($securityDOM->createElement('u:Created', self::getCurrentTime().'Z'));
		$headerTimestamp->appendChild($securityDOM->createElement('u:Expires', self::getExpiryTime().'Z'));

		$requestedSecurityToken = $securityDOM->createDocumentFragment();
		$requestedSecurityToken->appendXML($securityToken['securityToken']);
		$securityHeader->appendChild($requestedSecurityToken);

		$signatureNode = $securityHeader->appendChild($securityDOM->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Signature'));
		$signedInfoNode = $signatureNode->appendChild($securityDOM->createElement('SignedInfo'));
		$signedInfoNode->appendChild($securityDOM->createElement('CanonicalizationMethod'))->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
		$signedInfoNode->appendChild($securityDOM->createElement('SignatureMethod'))->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#hmac-sha1');
		$referenceNode = $signedInfoNode->appendChild($securityDOM->createElement('Reference'));
		$referenceNode->setAttribute('URI', '#_0');
		$referenceNode->appendChild($securityDOM->createElement('Transforms'))->appendChild($securityDOM->createElement('Transform'))->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
		$referenceNode->appendChild($securityDOM->createElement('DigestMethod'))->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
		$referenceNode->appendChild($securityDOM->createElement('DigestValue', base64_encode(sha1($headerTimestamp->C14N(true), true))));
		$signatureNode->appendChild($securityDOM->createElement('SignatureValue', base64_encode(hash_hmac('sha1', $signedInfoNode->C14N(true), base64_decode($securityToken['binarySecret']), true))));
		$keyInfoNode = $signatureNode->appendChild($securityDOM->createElement('KeyInfo'));
		$securityTokenReferenceNode = $keyInfoNode->appendChild($securityDOM->createElement('o:SecurityTokenReference'));
		$securityTokenReferenceNode->setAttributeNS('http://docs.oasis-open.org/wss/oasis-wss-wssecurity-secext-1.1.xsd', 'k:TokenType', 'http://docs.oasis-open.org/wss/oasis-wss-saml-token-profile-1.1#SAMLV1.1');
		$securityTokenReferenceNode->appendChild($securityDOM->createElement('o:KeyIdentifier', $securityToken['keyIdentifier']))->setAttribute('ValueType', 'http://docs.oasis-open.org/wss/oasis-wss-saml-token-profile-1.0#SAMLAssertionID');
		
		return $securityHeader;
	}*/
        
        
        /**
	 * Utility function that checks base CRM Connection settings
	 * Checks the Discovery URL, username and password in provided settings and verifies all the necessary data exists
	 * @return boolean indicator showing if the connection details are okay
	 * @ignore
	 */
	private function checkConnectionSettings() {
                /* username and password are common for authentication modes */
                if ($this->settings->username == NULL) return FALSE;
                if ($this->settings->password == NULL) return FALSE;
                
                switch($this->settings->authMode){
                    case "Federation":
                        /* Check Discovery Service URL for internet facing deployment */
                        if ($this->settings->discoveryUrl == NULL) return FALSE;
                        return TRUE;
                    case "OnlineFederation":
                        /* Additional checks  for CRM Online not required */
                        return TRUE;
                    default:
                        return FALSE;
                }
	}
        
        
        /**
	 * Send a RetrieveMultiple request to the Dynamics CRM server
	 * and return the results as a structured Object
	 * Each Entity returned is processed into an appropriate AlexaSDK_Entity object
	 *
	 * @param string $queryXML the Fetch XML string (as generated by the Advanced Find tool on Microsoft Dynamics CRM 2011)
	 * @param boolean $allPages indicates if the query should be resent until all possible data is retrieved
	 * @param string $pagingCookie if multiple pages are returned, send the paging cookie to get pages 2 and onwards.  Use NULL to get the first page.  Ignored if $allPages is specified.
	 * @param integer $limitCount maximum number of records to be returned per page
	 * @param boolean $simpleMode indicates if we should just use stdClass, instead of creating Entities
	 * @return stdClass a PHP Object containing all the data retrieved.
	 */
	public function retrieveMultiple($queryXML, $allPages = TRUE, $pagingCookie = NULL, $limitCount = NULL, $pageNumber = NULL, $simpleMode = FALSE) {
		/* Prepare an Object to hold the returned data */
		$soapData = NULL;
		/* If we need all pages, ignore any supplied paging cookie */
		if ($allPages) $pagingCookie = NULL;
		do {
			/* Get the raw XML data */
			$rawSoapResponse = $this->retrieveMultipleRaw($queryXML, $pagingCookie, $limitCount, $pageNumber);
			/* Parse the raw XML data into an Object */
			$tmpSoapData = self::parseRetrieveMultipleResponse($this, $rawSoapResponse, $simpleMode);
			/* If we already had some data, add the old Entities */
			if ($soapData != NULL) {
				$tmpSoapData->Entities = array_merge($soapData->Entities, $tmpSoapData->Entities);
				$tmpSoapData->Count += $soapData->Count;
			}
			/* Save the new Soap Data */
			$soapData = $tmpSoapData;
			
			/* Check if the PagingCookie is present & needed */
			if ($soapData->MoreRecords && $soapData->PagingCookie == NULL) {
				/* Paging Cookie is not present in returned data, but is expected! */
				/* Check if a Paging Cookie was supplied */
				if ($pagingCookie == NULL) {
					/* This was the first page */
					$pageNo = 1;
				} else {
					/* This is the page from the last PagingCookie, plus 1 */
					$pageNo = self::getPageNo($pagingCookie) + 1;
				}
				/* Create a new paging cookie for this page */
				$pagingCookie = '<cookie page="'.$pageNo.'"></cookie>';
				$soapData->PagingCookie = $pagingCookie;
			} else {
				/* PagingCookie exists, or is not needed */
				$pagingCookie = $soapData->PagingCookie;
			}
			
			/* Loop while there are more records, and we want all pages */
		} while ($soapData->MoreRecords && $allPages);
		
		/* Return the compiled structure */
		return $soapData;
	}
        
        /**
	 * Send a RetrieveMultiple request to the Dynamics CRM server
	 * and return the results as a structured Object
	 * Each Entity returned is processed into a simple stdClass
	 * 
	 * Note that this function is faster than using Entities, but not as strong
	 * at handling complicated return types.
	 *
	 * @param string $queryXML the Fetch XML string (as generated by the Advanced Find tool on Microsoft Dynamics CRM 2011)
	 * @param boolean $allPages indicates if the query should be resent until all possible data is retrieved
	 * @param string $pagingCookie if multiple pages are returned, send the paging cookie to get pages 2 and onwards.  Use NULL to get the first page.  Ignored if $allPages is specified.
	 * @param integer $limitCount maximum number of records to be returned per page
	 * @return stdClass a PHP Object containing all the data retrieved.
	 */
	public function retrieveMultipleSimple($queryXML, $allPages = TRUE, $pagingCookie = NULL, $pageNumber = NULL, $limitCount = NULL) {
		return $this->retrieveMultiple($queryXML, $allPages, $pagingCookie, $limitCount, $pageNumber, true);
	}
        
        /**
         * retrieve a single Entity based on queryXML
         * 
         * @param string $queryXML the Fetch XML string (as generated by the Advanced Find tool on Microsoft Dynamics CRM)
         * @return AlexaSDK_Entity a PHP Object containing all the data retrieved.
         */
        public function retrieveSingle($queryXML){
            $result = $this->retrieveMultiple($queryXML, FALSE, NULL, 1, NULL, false);
            
            return ($result->Count) ? $result->Entities[0] : NULL;
        }
        
        
        /**
	 * Send a RetrieveMultiple request to the Dynamics CRM server
	 * and return the results as raw XML
	 *
	 * This is particularly useful when debugging the responses from the server
	 * 
	 * @param string $queryXML the Fetch XML string (as generated by the Advanced Find tool on Microsoft Dynamics CRM 2011)
	 * @param string $pagingCookie if multiple pages are returned, send the paging cookie to get pages 2 and onwards.  Use NULL to get the first page
	 * @param integer $limitCount maximum number of records to be returned per page
	 * @return string the raw XML returned by the server, including all SOAP Envelope, Header and Body data.
	 */
	public function retrieveMultipleRaw($queryXML, $pagingCookie = NULL, $limitCount = NULL, $pageNumber = NULL) {
		/* Send the sequrity request and get a security token */
		$securityToken = $this->authentication->getOrganizationSecurityToken();
		/* Generate the XML for the Body of a RetrieveMulitple request */
		$executeNode = self::generateRetrieveMultipleRequest($queryXML, $pagingCookie, $limitCount, $pageNumber);
		/* Turn this into a SOAP request, and send it */
		$retrieveMultipleSoapRequest = $this->generateSoapRequest($this->settings->organizationUrl, $this->getOrganizationRetrieveMultipleAction(), $securityToken, $executeNode);
		$soapResponse = self::getSoapResponse($this->settings->organizationUrl, $retrieveMultipleSoapRequest);
		
		return $soapResponse;
	}
        
        
        /** 
	 * Generate a Retrieve Multiple Request
	 * @ignore
	 */
	protected static function generateRetrieveMultipleRequest($queryXML, $pagingCookie = NULL, $limitCount = NULL, $pageNumber = NULL) {
		if ($pagingCookie != NULL) {
			/* Turn the queryXML into a DOMDocument so we can manipulate it */
			$queryDOM = new DOMDocument(); $queryDOM->loadXML($queryXML);
                        if ($pageNumber == NULL){
                            $newPage = self::getPageNo($pagingCookie) + 1;
                            //echo 'Doing paging - Asking for page: '.$newPage.PHP_EOL;
                        }else{
                            $newPage = $pageNumber;
                        }
			/* Modify the query that we send: Add the Page number */
			$queryDOM->documentElement->setAttribute('page', $newPage);
			/* Modify the query that we send: Add the Paging-Cookie (note - HTMLENTITIES automatically applied by DOMDocument!) */
			$queryDOM->documentElement->setAttribute('paging-cookie', $pagingCookie);
			/* Update the Query XML with the new structure */
			$queryXML = $queryDOM->saveXML($queryDOM->documentElement);
			//echo PHP_EOL.PHP_EOL.$queryXML.PHP_EOL.PHP_EOL;
		}
		/* Turn the queryXML into a DOMDocument so we can manipulate it */
		$queryDOM = new DOMDocument(); 
                $queryDOM->loadXML($queryXML);
		/* Find the current limit, if there is one */
		$currentLimit = self::$maximumRecords+1;
		if ($queryDOM->documentElement->hasAttribute('count')) {
			$currentLimit = $queryDOM->documentElement->getAttribute('count');
		}
		/* Determine the preferred limit (passed by argument, or 5000 if not set) */
		$preferredLimit = ($limitCount == NULL) ? self::$maximumRecords : $limitCount;
		if ($preferredLimit > self::$maximumRecords) $preferredLimit = self::$maximumRecords;
		/* If the current limit is not set, or is greater than the preferred limit, over-ride it */
		if ($currentLimit > $preferredLimit) {
			/* Modify the query that we send: Change the Count */
			$queryDOM->documentElement->setAttribute('count', $preferredLimit);
			/* Update the Query XML with the new structure */
			$queryXML = $queryDOM->saveXML($queryDOM->documentElement);
			//echo PHP_EOL.PHP_EOL.$queryXML.PHP_EOL.PHP_EOL;
		}
		/* Generate the RetrieveMultipleRequest message */
		$retrieveMultipleRequestDOM = new DOMDocument();
		$retrieveMultipleNode = $retrieveMultipleRequestDOM->appendChild($retrieveMultipleRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'RetrieveMultiple'));
		$queryNode = $retrieveMultipleNode->appendChild($retrieveMultipleRequestDOM->createElement('query'));
		$queryNode->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'i:type', 'b:FetchExpression');
		$queryNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts');
		$queryNode->appendChild($retrieveMultipleRequestDOM->createElement('b:Query', htmlentities($queryXML)));           
		/* Return the DOMNode */                
		return $retrieveMultipleNode;
	}
        
        
        /** 
	 * Generate a Retrieve Request
	 * @ignore
	 */
	protected static function generateRetrieveRequest($entityType, $entityId, $columnSet) {
		/* Generate the RetrieveRequest message */
		$retrieveRequestDOM = new DOMDocument();
		$retrieveNode = $retrieveRequestDOM->appendChild($retrieveRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Retrieve'));
		$retrieveNode->appendChild($retrieveRequestDOM->createElement('entityName', $entityType));
		$retrieveNode->appendChild($retrieveRequestDOM->createElement('id', $entityId));
		$columnSetNode = $retrieveNode->appendChild($retrieveRequestDOM->createElement('columnSet'));
		$columnSetNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts');
		$columnSetNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance');
		/* Add the columns requested, if specified */
		if ($columnSet != NULL && count($columnSet) > 0) {
			$columnSetNode->appendChild($retrieveRequestDOM->createElement('b:AllColumns', 'false'));
			$columnsNode = $columnSetNode->appendChild($retrieveRequestDOM->createElement('b:Columns'));
			$columnsNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.microsoft.com/2003/10/Serialization/Arrays');
			foreach ($columnSet as $columnName) {
				$columnsNode->appendChild($retrieveRequestDOM->createElement('c:string', strtolower($columnName)));
			}
		} else {
			/* No columns specified, request all of them */
			$columnSetNode->appendChild($retrieveRequestDOM->createElement('b:AllColumns', 'true'));
		}
		/* Return the DOMNode */
		return $retrieveNode;
	}
        
        
        /**
	 * Parse the results of a RetrieveMultipleRequest into a useable PHP object
	 * @param AlexaSDK $conn
	 * @param String $soapResponse
	 * @param Boolean $simpleMode
	 * @ignore
	 */
	public static function parseRetrieveMultipleResponse(AlexaSDK $conn, $soapResponse, $simpleMode) {
		/* Load the XML into a DOMDocument */
		$soapResponseDOM = new DOMDocument();
		$soapResponseDOM->loadXML($soapResponse);
		/* Find the RetrieveMultipleResponse */
		$retrieveMultipleResponseNode = NULL;
		foreach ($soapResponseDOM->getElementsByTagName('RetrieveMultipleResponse') as $node) {
			$retrieveMultipleResponseNode = $node;
			break;
		}
		unset($node);
		if ($retrieveMultipleResponseNode == NULL) {
			throw new Exception('Could not find RetrieveMultipleResponse node in XML provided');
			return FALSE;
		}
		/* Find the RetrieveMultipleResult node */
		$retrieveMultipleResultNode = NULL;
		foreach ($retrieveMultipleResponseNode->getElementsByTagName('RetrieveMultipleResult') as $node) {
			$retrieveMultipleResultNode = $node;
			break;
		}
		unset($node);
		if ($retrieveMultipleResultNode == NULL) {
			throw new Exception('Could not find RetrieveMultipleResult node in XML provided');
			return FALSE;
		}
		/* Assemble an associative array for the details to return */
		$responseDataArray = Array();
		$responseDataArray['EntityName'] = $retrieveMultipleResultNode->getElementsByTagName('EntityName')->length == 0 ? NULL : $retrieveMultipleResultNode->getElementsByTagName('EntityName')->item(0)->textContent;
		$responseDataArray['MoreRecords'] = ($retrieveMultipleResultNode->getElementsByTagName('MoreRecords')->item(0)->textContent == 'true');
		$responseDataArray['PagingCookie'] = $retrieveMultipleResultNode->getElementsByTagName('PagingCookie')->length == 0 ? NULL : $retrieveMultipleResultNode->getElementsByTagName('PagingCookie')->item(0)->textContent;
		$responseDataArray['Entities'] = Array();
		/* Loop through the Entities returned */
		foreach ($retrieveMultipleResultNode->getElementsByTagName('Entities')->item(0)->getElementsByTagName('Entity') as $entityNode) {
			/* If we are in "SimpleMode", just create the Attributes as a stdClass */
			if ($simpleMode) {
				/* Create an Array to hold the Entity properties */
				$entityArray = Array();
				/* Identify the Attributes */
				$keyValueNodes = $entityNode->getElementsByTagName('Attributes')->item(0)->getElementsByTagName('KeyValuePairOfstringanyType');
				/* Add the Attributes in the Key/Value Pairs of String/AnyType to the Array */
				self::addAttributes($entityArray, $keyValueNodes);
				/* Identify the FormattedValues */
				$keyValueNodes = $entityNode->getElementsByTagName('FormattedValues')->item(0)->getElementsByTagName('KeyValuePairOfstringstring');
				/* Add the Formatted Values in the Key/Value Pairs of String/String to the Array */
				self::addFormattedValues($entityArray, $keyValueNodes);
				/* Add the Entity to the Entities Array as a stdClass Object */
				$responseDataArray['Entities'][] = (Object)$entityArray;
			} else {
				/* Generate a new Entity from the DOMNode */
				$entity = AlexaSDK_Entity::fromDOM($conn, $responseDataArray['EntityName'], $entityNode);
				/* Add the Entity to the Entities Array as a AlexaSDK_Entity Object */
				$responseDataArray['Entities'][] = $entity;
			}
		}
		/* Record the number of Entities */
		$responseDataArray['Count'] = count($responseDataArray['Entities']);
		
		/* Convert the Array to a stdClass Object */
		$responseData = (Object)$responseDataArray;
		return $responseData;
	}
        
        
        /**
	 * Parse the results of a RetrieveMultipleRequest into a useable PHP object
	 * @param AlexaSDK $conn
	 * @param String $soapResponse
	 * @param Boolean $simpleMode
	 * @ignore
	 */
	protected static function parseRetrieveMultipleFormResponse(AlexaSDK $conn, $soapResponse, $simpleMode) {
		/* Load the XML into a DOMDocument */
		$soapResponseDOM = new DOMDocument();
		$soapResponseDOM->loadXML($soapResponse);
		/* Find the RetrieveMultipleResponse */
		$retrieveMultipleResponseNode = NULL;
		foreach ($soapResponseDOM->getElementsByTagName('RetrieveMultipleResponse') as $node) {
			$retrieveMultipleResponseNode = $node;
			break;
		}
		unset($node);
		if ($retrieveMultipleResponseNode == NULL) {
			throw new Exception('Could not find RetrieveMultipleResponse node in XML provided');
			return FALSE;
		}
		/* Find the RetrieveMultipleResult node */
		$retrieveMultipleResultNode = NULL;
		foreach ($retrieveMultipleResponseNode->getElementsByTagName('RetrieveMultipleResult') as $node) {
			$retrieveMultipleResultNode = $node;
			break;
		}
		unset($node);
		if ($retrieveMultipleResultNode == NULL) {
			throw new Exception('Could not find RetrieveMultipleResult node in XML provided');
			return FALSE;
		}
		/* Assemble an associative array for the details to return */
		$responseDataArray = Array();
		$responseDataArray['EntityName'] = $retrieveMultipleResultNode->getElementsByTagName('EntityName')->length == 0 ? NULL : $retrieveMultipleResultNode->getElementsByTagName('EntityName')->item(0)->textContent;
		$responseDataArray['MoreRecords'] = ($retrieveMultipleResultNode->getElementsByTagName('MoreRecords')->item(0)->textContent == 'true');
		$responseDataArray['PagingCookie'] = $retrieveMultipleResultNode->getElementsByTagName('PagingCookie')->length == 0 ? NULL : $retrieveMultipleResultNode->getElementsByTagName('PagingCookie')->item(0)->textContent;
		$responseDataArray['Entities'] = Array();
		/* Loop through the Entities returned */
		foreach ($retrieveMultipleResultNode->getElementsByTagName('Entities')->item(0)->getElementsByTagName('Entity') as $entityNode) {
			/* If we are in "SimpleMode", just create the Attributes as a stdClass */
			if ($simpleMode) {
				/* Create an Array to hold the Entity properties */
				$entityArray = Array();
				/* Identify the Attributes */
				$keyValueNodes = $entityNode->getElementsByTagName('Attributes')->item(0)->getElementsByTagName('KeyValuePairOfstringanyType');
				/* Add the Attributes in the Key/Value Pairs of String/AnyType to the Array */
				self::addAttributes($entityArray, $keyValueNodes);
				/* Identify the FormattedValues */
				$keyValueNodes = $entityNode->getElementsByTagName('FormattedValues')->item(0)->getElementsByTagName('KeyValuePairOfstringstring');
				/* Add the Formatted Values in the Key/Value Pairs of String/String to the Array */
				self::addFormattedValues($entityArray, $keyValueNodes);
				/* Add the Entity to the Entities Array as a stdClass Object */
				$responseDataArray['Entities'][] = (Object)$entityArray;
			} else {
				/* Generate a new Entity from the DOMNode */
				$entity = AlexaSDK_Entity::fromDOM($conn, $responseDataArray['EntityName'], $entityNode);
				/* Add the Entity to the Entities Array as a AlexaSDK_Entity Object */
				$responseDataArray['Entities'][] = $entity;
			}
		}
		/* Record the number of Entities */
		$responseDataArray['Count'] = count($responseDataArray['Entities']);
		
		/* Convert the Array to a stdClass Object */
		$responseData = (Object)$responseDataArray;
		return $responseData;
	}
        
        
        /**
	 * Add a list of Attributes to an Array of Attributes, using appropriate handling
	 * of the Attribute type, and avoiding over-writing existing attributes
	 * already in the array 
	 * 
	 * Optionally specify an Array of sub-keys, and a particular sub-key
	 * - If provided, each sub-key in the Array will be created as an Object attribute,
	 *   and the value will be set on the specified sub-key only (e.g. (New, Old) / New)
	 * 
	 * @ignore
	 */
	protected static function addAttributes(Array &$targetArray, DOMNodeList $keyValueNodes, Array $keys = NULL, $key1 = NULL) {
		foreach ($keyValueNodes as $keyValueNode) {
			/* Get the Attribute name (key) */
			$attributeKey = $keyValueNode->getElementsByTagName('key')->item(0)->textContent;
			/* Check the Value Type */
			$attributeValueType = $keyValueNode->getElementsByTagName('value')->item(0)->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type');
			/* Strip any Namespace References from the Type */
			$attributeValueType = self::stripNS($attributeValueType);
			switch ($attributeValueType) {
				case 'AliasedValue':
					/* For an AliasedValue, the Key is Alias.Field, so just get the Alias */
					list($attributeKey, ) = explode('.', $attributeKey, 2);
					/* Entity Logical Name => the Object Type */
					$entityLogicalName = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('EntityLogicalName')->item(0)->textContent;
					/* Attribute Logical Name => the actual Attribute of the Aliased Object */
					$attributeLogicalName = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('AttributeLogicalName')->item(0)->textContent;
					$entityAttributeValue = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->textContent;
					/* See if this Alias is already in the Array */
					if (array_key_exists($attributeKey, $targetArray)) {
						/* It already exists, so grab the existing Object and set the new Attribute */
						$attributeValue = $targetArray[$attributeKey];
						$attributeValue->$attributeLogicalName = $entityAttributeValue;
						/* Pull it from the array, so we don't set a duplicate */
						unset($targetArray[$attributeKey]);
					} else {
						/* Create a new Object with the Logical Name, and this Attribute */
						$attributeValue = (Object)Array('LogicalName' => $entityLogicalName, $attributeLogicalName => $entityAttributeValue);
					}
					break;
				case 'EntityReference':
					$attributeLogicalName = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('LogicalName')->item(0)->textContent;
					$attributeId = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Id')->item(0)->textContent;
					$attributeName = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Name')->item(0)->textContent;
					$attributeValue = (Object)Array('LogicalName' => $attributeLogicalName,
								'Id' => $attributeId,
								'Name' => $attributeName);
					break;
				case 'OptionSetValue':
					$attributeValue = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->textContent;
					break;
				case 'dateTime':
					$attributeValue = $keyValueNode->getElementsByTagName('value')->item(0)->textContent;
					$attributeValue = self::parseTime($attributeValue, '%Y-%m-%dT%H:%M:%SZ');
					break;
				default:
					$attributeValue = $keyValueNode->getElementsByTagName('value')->item(0)->textContent;
			}
			/* If we are working normally, just store the data in the array */
			if ($keys == NULL) {
				/* Assume that if there is a duplicate, it's a formatted version of this */
				if (array_key_exists($attributeKey, $targetArray)) {
					$responseDataArray[$attributeKey] = (Object)Array('Value' => $attributeValue, 
							'FormattedValue' => $targetArray[$attributeKey]);
				} else {
					$targetArray[$attributeKey] = $attributeValue;
				}
			} else {
				/* Store the data in the array for this AuditRecord's properties */
				if (array_key_exists($attributeKey, $targetArray)) {
					/* We assume it's already a "good" Object, and just set this key */
					if (isset($targetArray[$attributeKey]->$key1)) {
						/* It's already set, so add the Un-formatted version */
						$targetArray[$attributeKey]->$key1 = (Object)Array(
								'Value' => $attributeValue,
								'FormattedValue' => $targetArray[$attributeKey]->$key1);
					} else {
						/* It's not already set, so just set this as a value */
						$targetArray[$attributeKey]->$key1 = $attributeValue;
					}
				} else {
					/* We need to create the Object */
					$obj = (Object)Array();
					foreach ($keys as $k) { $obj->$k = NULL; }
					/* And set the particular property */
					$obj->$key1 = $attributeValue;
					/* And store the Object in the target Array */
					$targetArray[$attributeKey] = $obj;
				}
			}
		}
	}
        
        /**
	 * Find the PageNumber in a PagingCookie
	 * 
	 * @param String $pagingCookie
	 * @ignore
	 */
	private static function getPageNo($pagingCookie) {
		/* Turn the pagingCookie into a DOMDocument so we can read it */
		$pagingDOM = new DOMDocument(); $pagingDOM->loadXML($pagingCookie);
		/* Find the page number */
		$pageNo = $pagingDOM->documentElement->getAttribute('page');
		return (int)$pageNo;
	}
        
	
	/**
	 * Utility function to get the SoapAction for the RetrieveMultiple method
	 * @ignore
	 */
	private function getOrganizationRetrieveMultipleAction() {
		/* If it's not cached, update the cache */
		if ($this->organizationRetrieveMultipleAction == NULL) {
			$actions = $this->getAllOrganizationSoapActions();
			$this->organizationRetrieveMultipleAction = $actions['RetrieveMultiple'];
		}
		
		return $this->organizationRetrieveMultipleAction;
	}
	
	/**
	 * Utility function to get the SoapAction for the Retrieve method
	 * @ignore
	 */
	private function getOrganizationRetrieveAction() {
		/* If it's not cached, update the cache */
		if ($this->organizationRetrieveAction == NULL) {
			$actions = $this->getAllOrganizationSoapActions();
			$this->organizationRetrieveAction = $actions['Retrieve'];
		}
		
		return $this->organizationRetrieveAction;
	}
	
	/**
	 * Utility function to get the SoapAction for the Create method
	 * @ignore
	 */
	private function getOrganizationCreateAction() {
		/* If it's not cached, update the cache */
		if ($this->organizationCreateAction == NULL) {
			$actions = $this->getAllOrganizationSoapActions();
			$this->organizationCreateAction = $actions['Create'];
		}
	
		return $this->organizationCreateAction;
	}
	
	/**
	 * Utility function to get the SoapAction for the Delete method
	 * @ignore
	 */
	private function getOrganizationDeleteAction() {
		/* If it's not cached, update the cache */
		if ($this->organizationDeleteAction == NULL) {
			$actions = $this->getAllOrganizationSoapActions();
			$this->organizationDeleteAction = $actions['Delete'];
		}
	
		return $this->organizationDeleteAction;
	}
	
	/**
	 * Utility function to get the SoapAction for the Update method
	 * @ignore
	 */
	private function getOrganizationUpdateAction() {
		/* If it's not cached, update the cache */
		if ($this->organizationUpdateAction == NULL) {
			$actions = $this->getAllOrganizationSoapActions();
			$this->organizationUpdateAction = $actions['Update'];
		}
	
		return $this->organizationUpdateAction;
	}
        
        
        /**
	 * Check if an Entity Definition has been cached
	 * 
	 * @param String $entityLogicalName Logical Name of the entity to check for in the Cache
	 * @return boolean true if this Entity has been cached
	 */
	public function isEntityDefinitionCached($entityLogicalName) {
		/* Check if this entityLogicalName is in the Cache */
		if (array_key_exists($entityLogicalName, $this->cachedEntityDefintions)) {
                        if (self::$debugMode) echo "entity definition cached";
			return true;
		} else {
			return false;
		}
	}
        
        
        /**
	 * Cache the definition of an Entity
	 * 
	 * @param String $entityLogicalName
	 * @param SimpleXMLElement $entityData
	 * @param Array $propertiesArray
	 * @param Array $propertyValuesArray
	 * @param Array $mandatoriesArray
	 * @param Array $optionSetsArray
	 * @param String $displayName
	 */
	public function setCachedEntityDefinition($entityLogicalName, 
			SimpleXMLElement $entityData, Array $propertiesArray, Array $propertyValuesArray,
			Array $mandatoriesArray, Array $optionSetsArray, $displayName, $entitytypecode, $entityDisplayName, $entityDisplayCollectionName, $entityDescription ) {
		/* Store the details of the Entity Definition in the Cache */
		$this->cachedEntityDefintions[$entityLogicalName] = Array(
				/*$entityData->asXML(),*/ $propertiesArray, $propertyValuesArray, 
				$mandatoriesArray, $optionSetsArray, $displayName, $entitytypecode, $entityDisplayName, $entityDisplayCollectionName, $entityDescription);
            
                /*$this->cachedEntityDefintions[$entityLogicalName] = Array(
				$propertiesArray, $propertyValuesArray, 
				$mandatoriesArray, $optionSetsArray, $displayName);*/
                
                // Write products to Cache in 10 minutes with same keyword
                $this->cacheClass->set("entities", serialize($this->cachedEntityDefintions) , self::$cacheTime);
	}
        
        
        
        /**
	 * Get the Definition of an Entity from the Cache
	 * 
	 * @param String $entityLogicalName
	 * @param SimpleXMLElement $entityData
	 * @param Array $propertiesArray
	 * @param Array $propertyValuesArray
	 * @param Array $mandatoriesArray
	 * @param Array $optionSetsArray
	 * @param String $displayName
	 * @return boolean true if the Cache was retrieved
	 */
	public function getCachedEntityDefinition($entityLogicalName, 
			&$entityData, Array &$propertiesArray, Array &$propertyValuesArray, Array &$mandatoriesArray,
			Array &$optionSetsArray, &$displayName, &$entitytypecode, &$entityDisplayName, &$entityDisplayCollectionName, &$entityDescription) {
		/* Check that this Entity Definition has been Cached */
		if ($this->isEntityDefinitionCached($entityLogicalName)) {
			/* Populate the containers and return true
			 * Note that we rely on PHP's "Copy on Write" functionality to prevent massive memory use:
			 * the only array that is ever updated inside an Entity is the propertyValues array (and the
			 * localProperties array) - the other data therefore becomes a single reference during
			 * execution.
			 */
			/*$entityData = $this->cachedEntityDefintions[$entityLogicalName][0];
			$propertiesArray = $this->cachedEntityDefintions[$entityLogicalName][1];
			$propertyValuesArray = $this->cachedEntityDefintions[$entityLogicalName][2];
			$mandatoriesArray = $this->cachedEntityDefintions[$entityLogicalName][3];
			$optionSetsArray = $this->cachedEntityDefintions[$entityLogicalName][4];
			$displayName = $this->cachedEntityDefintions[$entityLogicalName][5];
                        $entitytypecode = $this->cachedEntityDefintions[$entityLogicalName][6];
                        $entityDisplayName = $this->cachedEntityDefintions[$entityLogicalName][7];
                        $entityDisplayCollectionName = $this->cachedEntityDefintions[$entityLogicalName][8];
                        $entityDescription = $this->cachedEntityDefintions[$entityLogicalName][9];*/
                        
			$propertiesArray = $this->cachedEntityDefintions[$entityLogicalName][0];
			$propertyValuesArray = $this->cachedEntityDefintions[$entityLogicalName][1];
			$mandatoriesArray = $this->cachedEntityDefintions[$entityLogicalName][2];
			$optionSetsArray = $this->cachedEntityDefintions[$entityLogicalName][3];
			$displayName = $this->cachedEntityDefintions[$entityLogicalName][4];
                        $entitytypecode = $this->cachedEntityDefintions[$entityLogicalName][5];
                        $entityDisplayName = $this->cachedEntityDefintions[$entityLogicalName][6];
                        $entityDisplayCollectionName = $this->cachedEntityDefintions[$entityLogicalName][7];
                        $entityDescription = $this->cachedEntityDefintions[$entityLogicalName][8];
                        
			return true;
		} else {
			/* Not found - clear passed containers and return false */
			$entityData = NULL;
			$propertiesArray = NULL;
			$propertyValuesArray = NULL;
			$mandatoriesArray = NULL;
			$optionSetsArray = NULL;
			$displayName = NULL;
                        $entitytypecode = NULL;
                        $entityDisplayName = NULL;
                        $entityDisplayCollectionName = NULL;
                        $entityDescription = NULL;
			return false;
		}
	}
        
        /**
	 * Get all the details of the Connector that would be needed to
	 * bypass the normal login process next time...
	 * Note that the Entity definition cache, the DOMs and the security 
	 * policies are excluded from the Cache.
	 * @return Array
	 */
	public function getLoginCache() {
		return Array(
				$this->discoveryURI,
				$this->organizationUniqueName,
				$this->settings->organizationUrl,
				$this->security,
				NULL,
				$this->discoverySoapActions,
				$this->discoveryExecuteAction,
				NULL,
				NULL,
				$this->organizationSoapActions,
				$this->organizationCreateAction,
				$this->organizationDeleteAction,
				$this->organizationExecuteAction,
				$this->organizationRetrieveAction,
				$this->organizationRetrieveMultipleAction,
				$this->organizationUpdateAction,
				NULL,
				$this->organizationSecurityToken,
				Array(),
				self::$connectorTimeout,
				self::$maximumRecords,);
	}
	
	/**
	 * Restore the cached details
	 * @param Array $loginCache
	 */
	private function loadLoginCache(Array $loginCache) {
		list(
				$this->discoveryURI,
				$this->organizationUniqueName,
				$this->organizationURI,
				$this->security,
				$this->discoveryDOM,
				$this->discoverySoapActions,
				$this->discoveryExecuteAction,
				$this->discoverySecurityPolicy,
				$this->organizationDOM,
				$this->organizationSoapActions,
				$this->organizationCreateAction,
				$this->organizationDeleteAction,
				$this->organizationExecuteAction,
				$this->organizationRetrieveAction,
				$this->organizationRetrieveMultipleAction,
				$this->organizationUpdateAction,
				$this->organizationSecurityPolicy,
				$this->organizationSecurityToken,
				/*$this->cachedEntityDefintions,*/
				self::$connectorTimeout,
				self::$maximumRecords) = $loginCache;
	}
        
        /**
	 * Restore the cached Entity Definitions details
	 */
        private function loadEntityDefinitionCache() {
            
        }
        
        /**
	 * Send a Retrieve request to the Dynamics CRM 2011 server and return the results as a structured Object
	 * This function is typically used just after creating something (where you get the ID back
	 * as the return value), as it is more efficient to use RetrieveMultiple to search directly if 
	 * you don't already have the ID.
	 *
	 * @param AlexaSDK_Entity $entity the Entity to retrieve - must have an ID specified
	 * @param array $fieldSet array listing all fields to be fetched, or null to get all fields
	 * @return AlexaSDK_Entity (subclass) a Strongly-Typed Entity containing all the data retrieved.
	 */
	public function retrieve(AlexaSDK_Entity $entity, $fieldSet = NULL) {
		/* Only allow "Retrieve" for an Entity with an ID */
		if ($entity->ID == self::EmptyGUID) {
			throw new Exception('Cannot Retrieve an Entity without an ID.');
			return FALSE;
		}
		
		/* Get the raw XML data */
		$rawSoapResponse = $this->retrieveRaw($entity, $fieldSet);
                
		/* Parse the raw XML data into an Object */
		$newEntity = self::parseRetrieveResponse($this, $entity->LogicalName, $rawSoapResponse);
		/* Return the structured object */
		return $newEntity;
	}
        
        
        /**
	 * Send a Create request to the Dynamics CRM server, and return the ID of the newly created Entity
	 * 
	 * @param AlexaSDK_Entity $entity the Entity to create
	 */
	public function create(AlexaSDK_Entity &$entity) {
		/* Only allow "Create" for an Entity with no ID */
		if ($entity->ID != self::EmptyGUID) {
			throw new Exception('Cannot Create an Entity that already exists.');
			return FALSE;
		}
		
		/* Send the sequrity request and get a security token */
		$securityToken = $this->authentication->getOrganizationSecurityToken();
		/* Generate the XML for the Body of a Create request */
		$createNode = self::generateCreateRequest($entity);
		
		if (self::$debugMode) echo PHP_EOL.'Create Request: '.PHP_EOL.$createNode->C14N().PHP_EOL.PHP_EOL;
		
		/* Turn this into a SOAP request, and send it */
		$createRequest = $this->generateSoapRequest($this->settings->organizationUrl, $this->getOrganizationCreateAction(), $securityToken, $createNode);
		$soapResponse = self::getSoapResponse($this->settings->organizationUrl, $createRequest);
		
		if (self::$debugMode) echo PHP_EOL.'Create Response: '.PHP_EOL.$soapResponse.PHP_EOL.PHP_EOL;
		
		/* Load the XML into a DOMDocument */
		$soapResponseDOM = new DOMDocument();
		$soapResponseDOM->loadXML($soapResponse);
		
		/* Find the CreateResponse */
		$createResponseNode = NULL;
		foreach ($soapResponseDOM->getElementsByTagName('CreateResponse') as $node) {
			$createResponseNode = $node;
			break;
		}
		unset($node);
		if ($createResponseNode == NULL) {
			throw new Exception('Could not find CreateResponse node in XML returned from Server');
			return FALSE;
		}
		
		/* Get the EntityID from the CreateResult tag */
		$entityID = $createResponseNode->getElementsByTagName('CreateResult')->item(0)->textContent;
		$entity->ID = $entityID;
		$entity->reset();
		return $entityID;
	}
        
        /**
	 * Generate a Create Request
	 * @ignore
	 */
	protected static function generateCreateRequest(AlexaSDK_Entity $entity) {
		/* Generate the CreateRequest message */
		$createRequestDOM = new DOMDocument();
		$createNode = $createRequestDOM->appendChild($createRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Create'));
		$createNode->appendChild($createRequestDOM->importNode($entity->getEntityDOM(), true));
		/* Return the DOMNode */
		return $createNode;
	}
        
        
        
        /**
	 * Send an Update request to the Dynamics CRM server, and return update response status
	 *
	 * @param AlexaSDK_Entity $entity the Entity to update
	 */
	public function update(AlexaSDK_Entity &$entity) {
		/* Only allow "Update" for an Entity with an ID */
		if ($entity->ID == self::EmptyGUID) {
			throw new Exception('Cannot Update an Entity without an ID.');
			return FALSE;
		}
		
		/* Send the sequrity request and get a security token */
		$securityToken = $this->authentication->getOrganizationSecurityToken();
		/* Generate the XML for the Body of an Update request */
		$updateNode = self::generateUpdateRequest($entity);
	
		if (self::$debugMode) echo PHP_EOL.'Update Request: '.PHP_EOL.$updateNode->C14N().PHP_EOL.PHP_EOL;
	
		/* Turn this into a SOAP request, and send it */
		$updateRequest = $this->generateSoapRequest($this->settings->organizationUrl, $this->getOrganizationUpdateAction(), $securityToken, $updateNode);
                
		$soapResponse = self::getSoapResponse($this->settings->organizationUrl, $updateRequest);
                
		if (self::$debugMode) echo PHP_EOL.'Update Response: '.PHP_EOL.$soapResponse.PHP_EOL.PHP_EOL;
	
		/* Load the XML into a DOMDocument */
		$soapResponseDOM = new DOMDocument();
		$soapResponseDOM->loadXML($soapResponse);
	
		/* Find the UpdateResponse */
		$updateResponseNode = NULL;
		foreach ($soapResponseDOM->getElementsByTagName('UpdateResponse') as $node) {
			$updateResponseNode = $node;
			break;
		}
		unset($node);
		if ($updateResponseNode == NULL) {
			throw new Exception('Could not find UpdateResponse node in XML returned from Server');
			return FALSE;
		}
		/* Update occurred successfully */
		return $updateResponseNode->C14N();
	}
        
        
        /**
	 * Generate an Update Request
	 * @ignore
	 */
	protected static function generateUpdateRequest(AlexaSDK_Entity $entity) {
		/* Generate the UpdateRequest message */
		$updateRequestDOM = new DOMDocument();
		$updateNode = $updateRequestDOM->appendChild($updateRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Update'));
		$updateNode->appendChild($updateRequestDOM->importNode($entity->getEntityDOM(), true));
		/* Return the DOMNode */
		return $updateNode;
	}
        
        /**
	 * Send a Delete request to the Dynamics CRM server, and return delete response status
	 *
	 * @param AlexaSDK_Entity $entity the Entity to delete
	 */
	public function delete(AlexaSDK_Entity &$entity) {
		/* Only allow "Delete" for an Entity with an ID */
		if ($entity->ID == self::EmptyGUID) {
			throw new Exception('Cannot Delete an Entity without an ID.');
			return FALSE;
		}
		
		/* Send the sequrity request and get a security token */
		$securityToken = $this->authentication->getOrganizationSecurityToken();
		/* Generate the XML for the Body of a Delete request */
		$deleteNode = self::generateDeleteRequest($entity);
	
		if (self::$debugMode) echo PHP_EOL.'Delete Request: '.PHP_EOL.$deleteNode->C14N().PHP_EOL.PHP_EOL;
	
		/* Turn this into a SOAP request, and send it */
		$deleteRequest = $this->generateSoapRequest($this->settings->organizationUrl, $this->getOrganizationDeleteAction(), $securityToken, $deleteNode);
		$soapResponse = self::getSoapResponse($this->settings->organizationUrl, $deleteRequest);
	
		if (self::$debugMode) echo PHP_EOL.'Delete Response: '.PHP_EOL.$soapResponse.PHP_EOL.PHP_EOL;
	
		/* Load the XML into a DOMDocument */
		$soapResponseDOM = new DOMDocument();
		$soapResponseDOM->loadXML($soapResponse);
	
 		/* Find the DeleteResponse */
 		$deleteResponseNode = NULL;
 		foreach ($soapResponseDOM->getElementsByTagName('DeleteResponse') as $node) {
 			$deleteResponseNode = $node;
 			break;
 		}
 		unset($node);
 		if ($deleteResponseNode == NULL) {
 			throw new Exception('Could not find DeleteResponse node in XML returned from Server');
 			return FALSE;
 		}
 		/* Delete occurred successfully */
		return TRUE;
	}
        
        
        /**
	 * Generate a Delete Request
         * @param AlexaSDK_Entity $entity the Entity to delete
	 * @ignore
	 */
	protected static function generateDeleteRequest(AlexaSDK_Entity $entity) {
		/* Generate the DeleteRequest message */
		$deleteRequestDOM = new DOMDocument();
		$deleteNode = $deleteRequestDOM->appendChild($deleteRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Delete'));
		$deleteNode->appendChild($deleteRequestDOM->createElement('entityName', $entity->logicalName));
		$deleteNode->appendChild($deleteRequestDOM->createElement('id', $entity->ID));
		/* Return the DOMNode */
		return $deleteNode;
	}
        
        
        
        
        /*
         *  METHODS TO REFACTOR 
         */
        
        
        /** 
         * @ignore
	 * @deprecated Wil be changed soon
         * NEED TO REFACTOR
	 */
        public function getEntityFormByName($entity, $formName){
            $this->Authenticate();
            
            $header = $this->authentication->getHeader('RetrieveMultiple');
            
            $request = '
            <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                '.$header.'
                    <s:Body>
                        <RetrieveMultiple xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                            <query i:type="b:QueryExpression" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                <b:ColumnSet>
                                    <b:AllColumns>false</b:AllColumns>
                                    <b:Columns xmlns:c="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                                        <c:string>name</c:string>
                                        <c:string>formxml</c:string>
                                        <c:string>objecttypecode</c:string>
                                    </b:Columns>
                                </b:ColumnSet>
                                <b:Criteria>
                                    <b:Conditions/>
                                    <b:FilterOperator>And</b:FilterOperator>
                                    <b:Filters>
                                        <b:FilterExpression>
                                            <b:Conditions>
                                                <b:ConditionExpression>
                                                    <b:AttributeName>objecttypecode</b:AttributeName>
                                                    <b:Operator>Equal</b:Operator>
                                                    <b:Values xmlns:c="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                                                        <c:anyType i:type="d:string" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$entity.'</c:anyType>
                                                    </b:Values>
                                                    <b:EntityName i:nil="true"/>
                                                </b:ConditionExpression>
                                                <b:ConditionExpression>
                                                    <b:AttributeName>name</b:AttributeName>
                                                    <b:Operator>Equal</b:Operator>
                                                    <b:Values xmlns:c="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                                                        <c:anyType i:type="d:string" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$formName.'</c:anyType>
                                                    </b:Values>
                                                    <b:EntityName i:nil="true"/>
                                                </b:ConditionExpression>
                                            </b:Conditions>
                                            <b:FilterOperator>And</b:FilterOperator>
                                            <b:Filters/>
                                        </b:FilterExpression>
                                    </b:Filters>
                                </b:Criteria>
                                <b:Distinct>false</b:Distinct>
                                <b:EntityName>systemform</b:EntityName>
                                <b:LinkEntities/>
                                <b:Orders/>
                                <b:PageInfo>
                                    <b:Count>0</b:Count>
                                    <b:PageNumber>0</b:PageNumber>
                                    <b:PagingCookie i:nil="true"/>
                                    <b:ReturnTotalRecordCount>false</b:ReturnTotalRecordCount>
                                </b:PageInfo>
                                <b:NoLock>false</b:NoLock>
                            </query>
                        </RetrieveMultiple>
                    </s:Body>
                </s:Envelope>';
            
            $response = self::GetSOAPResponse($this->settings->organizationUrl, $request);
            
            /* Convert the XML into a DOMDocument */
            $DOM = new DOMDocument();
            $DOM->loadXML($response);
            
            try{
                if (
                        ($DOM->getElementsByTagNameNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'Entities')->item(0)
                        ->getElementsByTagNameNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'Entity')->length) > 0){


                    $form = $DOM->getElementsByTagNameNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'Entities')->item(0)
                            ->getElementsByTagNameNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'Entity')->item(0)
                            ->getElementsByTagNameNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'Attributes')->item(0)
                            ->getElementsByTagNameNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'KeyValuePairOfstringanyType')->item(1)
                            ->getElementsByTagNameNS('http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'value')->item(0)->textContent;

                    if ($form){
                        return $form;
                    }else{
                        return false;
                    }


                }else{
                    return false;
                }
            }catch(Exception $ex){
                return false;
            }
        }
        
        
        /** 
         * @ignore
	 * @deprecated Wil be changed soon
         * NEED TO REFACTOR
         * ADD cache definition to retrieved entities
	 */
        function retrieveAllEntities(){
            
            $securityToken = $this->authentication->getOrganizationSecurityToken();
            
            $request = '<Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                        <request i:type="b:RetrieveAllEntitiesRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                            <b:Parameters xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
                                <b:KeyValuePairOfstringanyType>
                                    <c:key>EntityFilters</c:key>
                                    <c:value i:type="d:EntityFilters" xmlns:d="http://schemas.microsoft.com/xrm/2011/Metadata">Entity</c:value>
                                </b:KeyValuePairOfstringanyType>
                                <b:KeyValuePairOfstringanyType>
                                    <c:key>RetrieveAsIfPublished</c:key>
                                    <c:value i:type="d:boolean" xmlns:d="http://www.w3.org/2001/XMLSchema">false</c:value>
                                </b:KeyValuePairOfstringanyType>
                            </b:Parameters>
                            <b:RequestId i:nil="true"/>
                            <b:RequestName>RetrieveAllEntities</b:RequestName>
                        </request>
                    </Execute>';
            
            
            $doc = new DOMDocument();
            $doc->loadXML($request);
            $executeNode = $doc->getElementsByTagName('Execute')->item(0);
            
            
            $retrieveEntityRequest = $this->generateSoapRequest($this->settings->organizationUrl, $this->getOrganizationExecuteAction(), $securityToken, $executeNode);
            /* Determine the Action in the SOAP Response */
            $responseDOM = new DOMDocument();
            $responseDOM->loadXML($retrieveEntityRequest);

            $result = $this->getSoapResponse($this->settings->organizationUrl, $retrieveEntityRequest);
            
            return self::parseRetrieveAllEntitiesResponse($result);
        }
        
        
        /* NEED TO REFACTOR
         * TODO: Add discovery service methods to CRM Oline
	 * @ignore
	 * @deprecated Wil be changed soon
	 */
        public function discover(){
            
            $settings = null;
            
            /* Store the security details */
            $this->security['username'] = $this->settings->username;
            $this->security['password'] = $this->settings->password;
            
            $result = '';
            
            if ($this->settings->authMode == "OnlineFederation"){
                
                $userRealm = $this->getUserRealm($this->settings->username);
                
                if (!$userRealm || $userRealm->NameSpaceType  == "Unknown"){
                    throw new Exception("Check your organization login");
                }
                
                
                $crmRegionsArray = array("crmna:dynamics.com", "crmsam:dynamics.com", "crmemea:dynamics.com", "crmapac:dynamics.com");
                
                $result = "";
                
                foreach ($crmRegionsArray as $crmRegion){
                    /* Request a Security Token for the Discovery Service */
                    
                    try{
                        $securityToken = $this->authentication->requestSecurityToken('https://login.microsoftonline.com/RST2.srf', $crmRegion, $this->security['username'], $this->security['password']);
                    }catch(Exception $ex){
                        throw new Exception("Authentication failure, check password for specified User Name");
                    }
                    
                    
                    $source = '<Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Discovery">
                                    <request i:type="RetrieveOrganizationsRequest" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                        <AccessType>Default</AccessType>
                                        <Release>Current</Release>
                                    </request>
                                </Execute>';

                    $doc = new DOMDocument();
                    $doc->loadXML($source);
                    $executeNode = $doc->getElementsByTagName('Execute')->item(0);
                    
                    switch($crmRegion){
                        case 'crmna:dynamics.com':
                            $discoveryUrl = "https://disco.crm.dynamics.com/XRMServices/2011/Discovery.svc";
                            $region = 'crmna:dynamics.com';
                        break;
                        case 'crmsa:dynamics.com':
                            $discoveryUrl = "https://disco.crm2.dynamics.com/XRMServices/2011/Discovery.svc";
                            $region = 'crmsa:dynamics.com';
                        break;
                        case 'crmemea:dynamics.com':
                            $discoveryUrl = "https://disco.crm4.dynamics.com/XRMServices/2011/Discovery.svc";
                            $region = 'crmemea:dynamics.com';
                        break;
                    
                        case 'crmapac:dynamics.com':
                            $discoveryUrl = "https://disco.crm5.dynamics.com/XRMServices/2011/Discovery.svc";
                            $region = 'crmapac:dynamics.com';
                        break;
                    }

                    try{
                    
                        $retrieveEntityRequest = $this->generateSoapRequest($discoveryUrl, 'http://schemas.microsoft.com/xrm/2011/Contracts/Discovery/IDiscoveryService/Execute', $securityToken, $executeNode);
                        /* Determine the Action in the SOAP Response */
                        $responseDOM = new DOMDocument();
                        $responseDOM->loadXML($retrieveEntityRequest);
                        
                        $result = $this->getSoapResponse($discoveryUrl, $retrieveEntityRequest, false);
                    }catch(Exception $ex){
                        // break;
                    }
                     
                    $responseDOM = new DOMDocument();
                    $responseDOM->loadXML($result);
                     
                    if($responseDOM->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Envelope')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Body')->item(0)
				->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope', 'Fault')->item(0)){
                        continue;
			
                    }
                    
                    $this->settings->discoveryUrl = $discoveryUrl;
                    $this->settings->crmRegion = $region;
                    
                    $discovery_data = $result;
                    
                    /* Parse the returned data to determine the correct EndPoint for the OrganizationService for the selected Organization */
                    $organizationServiceURI = NULL;
                    $organizationDomain = NULL;
                    $discoveryDOM = new DOMDocument(); 
                    $discoveryDOM->loadXML($discovery_data);
                    if ($discoveryDOM->getElementsByTagName('OrganizationDetail')->length > 0) {
                            foreach ($discoveryDOM->getElementsByTagName('OrganizationDetail') as $organizationNode) {
                                    //if ($organizationNode->getElementsByTagName('UniqueName')->item(0)->textContent == $this->organizationUniqueName) {
                                            foreach ($organizationNode->getElementsByTagName('Endpoints')->item(0)->getElementsByTagName('KeyValuePairOfEndpointTypestringztYlk6OT') as $endpointDOM) {
                                                    if ($endpointDOM->getElementsByTagName('key')->item(0)->textContent == 'OrganizationService') {
                                                            $organizationServiceURI = $endpointDOM->getElementsByTagName('value')->item(0)->textContent;
                                                    }

                                                    if ($endpointDOM->getElementsByTagName('key')->item(0)->textContent == 'WebApplication') {
                                                            $organizationDomain = $endpointDOM->getElementsByTagName('value')->item(0)->textContent;
                                                    }
                                            }
                                            break;
                                    //}
                            }
                    } else {
                            throw new Exception('Error fetching Organization details:'.PHP_EOL.$discovery_data);
                            return FALSE;
                    }
                    if ($organizationServiceURI == NULL) {
                            throw new Exception('Could not find OrganizationService URI for the Organization <'.$this->organizationUniqueName.'>');
                            return FALSE;
                    }
                    
                    $settings['region'] = $region;
                    $settings['organization_url'] = $organizationServiceURI;
                    $settings['domain'] = $organizationDomain;
                    $settings['server'] = $organizationDomain;
                }
            }

            /* Determine the Security used by this Organization */
            $discovery_authmode = $this->getDiscoveryAuthenticationMode();
            
            if (in_array("Federation", $discovery_authmode, true )){
                
                $this->security['discovery_authmode'] = "Federation";
                
                /* Determine the address to send security requests to */
                $this->security['discovery_authuri'] = $this->getDiscoveryAuthenticationAddress($this->security['discovery_authmode']);
                
                 /* Store the Security Service Endpoint for future use */
                $this->security['discovery_authendpoint'] = $this->getFederationSecurityURI('discovery');
                
                $this->organizationUrl = $this->getOrganizationURI();
                
                $settings['organization_url'] = $this->organizationUrl;
                $settings['domain'] = $this->domain;
                $settings['region'] = NULL;
                
            }else if ( in_array("OnlineFederation", $discovery_authmode, true )){
                
                /* Parse discovery methods for Online Federation sometimes works bad, 
                 * I'll return for it's rework later
                 */
                
                $this->security['discovery_authendpoint'] = 'https://login.microsoftonline.com/RST2.srf';
                $this->security['discovery_authmode'] = "OnlineFederation";
                
                /* Determine the address to send security requests to */
                
                /*
                $this->security['discovery_authuri'] = $this->getDiscoveryAuthenticationAddress($this->security['discovery_authmode']);
                
                
                $this->authentication = new AlexaSDK_Office365($this->settings);
                
                $this->Authenticate();
                
                $this->security['discovery_authendpoint'] = $this->getOnlineFederationOrganizationURI();
                
                $settings['organization_url'] = $this->organizationUrl;
                $settings['domain'] = $this->domain;
                 */
                
            }else if (in_array("LiveId", $discovery_authmode, true )){
                
                $this->security['discovery_authmode'] = "LiveId";
                
                /* LiveId authentication method is not supported */
                throw new UnexpectedValueException(get_class($this).' does not support "'.$this->security['discovery_authmode'].'" authentication mode used by Discovery Service');
           
            }
            
            
            $settings['crm_loginurl'] = $this->security['discovery_authendpoint'];
            $settings["authMode"] = $this->security["discovery_authmode"];
            $settings["discovery_url"] = $this->settings->discoveryUrl;
            $settings['crmadmin_login'] = $this->settings->username;
            $settings['crmadmin_password'] = $this->settings->password;
            
            $settings["port"] = $this->settings->port;
            $settings['server'] = $this->settings->server;
            $settings['use_ssl'] = $this->settings->use_ssl;
            
            return $settings;
        }
        
        /**
	 * @ignore
	 * @deprecated Wil be changed soon
	 */
        private static function parseOrganizationName($domain){
            $parse = parse_url($domain);
            
            $arr = explode(".", $parse["host"]);
            
            return $arr[0];
        }
        
        
        
        public function getUserRealm($username, $requestXML = false){
            /* Constant URL to get User Realm */
            $url = "https://login.microsoftonline.com/GetUserRealm.srf";
            /* Configure return type XML or JSON supported */
            $xmlParam = ($requestXML) ? "&xml=1" : "";
            /* Build request content */
            $content = "login=".urlencode($username).$xmlParam;
            /* Separate the provided URI into Path & Hostname sections */
            $urlDetails = parse_url($url);
            // setup headers
            $headers = array(
                            "POST ". $urlDetails['path'] ." HTTP/1.1",
                            "Host: " . $urlDetails['host'],
                            'Connection: Keep-Alive',
                            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
                            "Content-length: ".strlen($content),
            );
		
            $cURLHandle = curl_init();
            curl_setopt($cURLHandle, CURLOPT_URL, $url);
            curl_setopt($cURLHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($cURLHandle, CURLOPT_TIMEOUT, self::$connectorTimeout);
            curl_setopt($cURLHandle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($cURLHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($cURLHandle, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($cURLHandle, CURLOPT_POST, 1);
            curl_setopt($cURLHandle, CURLOPT_POSTFIELDS, $content);
            curl_setopt($cURLHandle, CURLOPT_HEADER, false);
            /* Execute the cURL request, get the XML response */
            $response = curl_exec($cURLHandle);
            /* Check for cURL errors */
            if (curl_errno($cURLHandle) != CURLE_OK) {
                    throw new Exception('cURL Error: '.curl_error($cURLHandle));
            }
            /* Check for HTTP errors */
            $httpResponse = curl_getinfo($cURLHandle, CURLINFO_HTTP_CODE);
            curl_close($cURLHandle);

            if ($requestXML){
                /* Return XML string */
                return $response;
            }else{
                /* Parse JSON from returned string */
                $result = json_decode($response);
                if (json_last_error() == JSON_ERROR_NONE){
                    return $result;
                }else{
                    return FALSE;
                }
            }
        }
        
        public function whoAmI(){
                /* Send the sequrity request and get a security token */
		$securityToken = $this->authentication->getOrganizationSecurityToken();
		/* Generate the XML for the Body of a WhoAmI request */
		$executeNode = self::generateWhoAmIRequest();
		/* Turn this into a SOAP request, and send it */
		$retrieveEntityRequest = $this->generateSoapRequest($this->settings->organizationUrl, $this->getOrganizationExecuteAction(), $securityToken, $executeNode);
		$soapResponse = self::getSoapResponse($this->settings->organizationUrl, $retrieveEntityRequest);
		
		return $soapResponse;
        }
        
        /* Debug it later */
        protected static function generateWhoAmIRequest(){
                $req = '<Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                            <request i:type="c:WhoAmIRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:c="http://schemas.microsoft.com/crm/2011/Contracts">
                                <b:Parameters xmlns:d="http://schemas.datacontract.org/2004/07/System.Collections.Generic"/>
                                <b:RequestId i:nil="true"/>
                                <b:RequestName>WhoAmI</b:RequestName>
                            </request>
                        </Execute>';
            
            
                /* Generate the DeleteRequest message */
		$whoamiRequestDOM = new DOMDocument();
                
                $executeNode = $whoamiRequestDOM->appendChild($whoamiRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Execute'));
		$requestNode = $executeNode->appendChild($whoamiRequestDOM->createElement('request'));
		$requestNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts');
                $requestNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.microsoft.com/xrm/2011/Contracts');
                $requestNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance');
                $requestNode->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'i:type', 'c:WhoAmIRequest');
                $parametersNode = $requestNode->appendChild($whoamiRequestDOM->createElement('b:Parameters'));
		$parametersNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic');
                
                $requiestIdNode = $requestNode->appendChild($whoamiRequestDOM->createElement('b:RequestId'));
                $requiestIdNode->setAttribute('i:nil', 'true');
                $requestNode->appendChild($whoamiRequestDOM->createElement('b:RequestName', 'WhoAmI'));
                
                return $executeNode;
        }
        
        
        
        
        
        /**
	 * @ignore
	 * @deprecated Wil be changed soon
	 */
        public function sandbox(){
            
            $this->security['username'] = $this->settings->username;
            $this->security['password'] = $this->settings->password;
            
            $discovery_authmode = $this->getDiscoveryAuthenticationMode();
            
            if ( in_array("OnlineFederation", $discovery_authmode, true )){
                
                $this->security['discovery_authmode'] = "OnlineFederation";
                
                /* Determine the address to send security requests to */
                $this->security['discovery_authuri'] = $this->getDiscoveryAuthenticationAddress($this->security['discovery_authmode']);
                
                $this->authentication->region = "crmemea:dynamics.com";
                
                $this->Authenticate();
                
                $this->security['discovery_authendpoint'] = $this->getOnlineFederationSecurityURI('discovery');
                
                
                self::GetSOAPResponse($this->settings->discoveryUrl, $this->authentication->requestRetrieveOrganization());
                
                /*
                $this->authentication = new AlexaSDK_Office365($this->settings);
                
                $this->Authenticate();
                
                $this->security['discovery_authendpoint'] = $this->getOnlineFederationOrganizationURI();
                
                $settings['organization_url'] = $this->organizationUrl;
                $settings['domain'] = $this->domain;*/
                
            }
        }
        
        
    }
    
    
endif;