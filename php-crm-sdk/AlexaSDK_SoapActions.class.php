<?php

class AlexaSDK_SoapActions {

		/**
		 * Object of AlexaSDK class
		 * 
		 * @var AlexaSDK
		 */
		private $conn;

		/**
		 * @ignore
		 */
		private $organizationSoapActions;

		/**
		 * @ignore
		 */
		private $discoverySoapActions;

		/**
		 * Create a new usable Dynamics CRM Soap Actions object for Discovery Service and Organization service
		 *    
		 * 
		 * @param AlexaSDK $conn Connection to the Dynamics CRM server - should be active already.
		 */
		public function __construct(AlexaSDK $conn) {
			$this->conn = $conn;
			$this->organizationSoapActions = $this->getCachedSoapActions('organization');
			$this->discoverySoapActions = $this->getCachedSoapActions('discovery');
		}

		/**
		 * Sets Soap Actions to cache
		 * 
		 * @param type $service
		 * @param type $soapActions
		 */
		private function setCachedSoapActions($service, $soapActions) {
			$this->conn->cacheClass->set($service . "_soap_actions", serialize($soapActions), $this->conn->getCacheTime());
		}

		/**
		 * Retrieves Soap Actions from cache
		 * 
		 * @param string $service
		 * @return array Soap Action array of strings, or NULL if action not cached
		 */
		private function getCachedSoapActions($service) {
			$soapActions = $this->conn->cacheClass->get($service . '_soap_actions');
			if ($soapActions) {
				return unserialize($soapActions);
			}
			return NULL;
		}

		/**
		 * Utility function to get the SoapAction
		 * 
		 * @param string $service Dynamics CRM soap service, can be 'organization' or 'discovery'
		 * @param string $soapAction Action for soap method
		 * @return type
		 * @throws Exception
		 */
		public function getSoapAction($service, $soapAction) {
			/* Capitalize first char in action name */
			$action = $soapAction; /* ucfirst(strtolower($soapAction)); */
			/* Switch service for soap action */
			switch (strtolower($service)) {
				case "organization":
					return $this->getOrganizationAction($action);
					break;
				case "discovery":
					return $this->getDiscoveryAction($action);
					break;
				default:
					throw new Exception("Undefined service(" . $service . ") for soap action(" . $action . ")");
					return FALSE;
			}
		}

		public function getOrganizationAction($action) {
			$soapActions = $this->getAllOrganizationSoapActions();
			return $soapActions[$action];
		}

		public function getDiscoveryAction($action) {
			$soapActions = $this->getAllDiscoverySoapActions();
			return $soapActions[$action];
		}

		/**
		 * Get all the Operations & corresponding SoapActions for the DiscoveryService 
		 */
		private function getAllDiscoverySoapActions() {
			/* If it is not cached, update the cache */
			if ($this->discoverySoapActions == NULL) {
				$this->discoverySoapActions = self::getAllSoapActions($this->conn->getDiscoveryDOM(), 'DiscoveryService');
				$this->setCachedSoapActions('discovery', $this->discoverySoapActions);
			}
			/* Return the cached value */
			return $this->discoverySoapActions;
		}

		/**
		 * Get all the Operations & corresponding SoapActions for the OrganizationService
		 */
		private function getAllOrganizationSoapActions() {
			/* If it is not cached, update the cache */
			if ($this->organizationSoapActions == NULL) {
				$this->organizationSoapActions = self::getAllSoapActions($this->conn->getOrganizationDOM(), 'OrganizationService');
				$this->setCachedSoapActions('organization', $this->organizationSoapActions);
			}
			/* Return the cached value */
			return $this->organizationSoapActions;
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
				throw new Exception('Could not find definition of Service <' . $serviceName . '> in provided WSDL');
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
				throw new Exception('Could not find binding for Service <' . $serviceName . '> in provided WSDL');
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
				throw new Exception('Could not find defintion of Binding <' . $bindingName . '> in provided WSDL');
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
}
