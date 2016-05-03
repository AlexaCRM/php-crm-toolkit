<?php

/**
 * AlexaSDK_OnlineFederation.php
 * 
 * This file defines the AlexaSDK_OnlineFederation class that used to authenticate and 
 * request SOAP headers for Microsoft Dynamics CRM Online from Microsoft Office and 
 * Portal services through SOAP calls from PHP.
 * 
 * @author alexacrm.com.au
 * @version 1.0
 * @package AlexaSDK\Authentication
 */

/**
 * This class used to authenticate to Microsoft Dynamics CRM Online
 */
class AlexaSDK_OnlineFederation extends AlexaSDK_Authentication {

		/**
		 * Create a new instance of the AlexaSDK
		 * 
		 * @param AlexaSDK_Settings $_settings
		 * 
		 * @return AlexaSDK_Federation
		 */
		function __construct($_settings, $_auth) {
			$this->settings = $_settings;
			$this->auth = $_auth;
		}

		/**
		 * Get the current Organization Service security token, or get a new one if necessary 
		 * @ignore
		 */
		public function getOrganizationSecurityToken() {
			/* Check if there is an existing token */
			if ($this->organizationSecurityToken != NULL) {
				/* Check if the Security Token is still valid */
				if ($this->organizationSecurityToken['expiryTime'] > time()) {
					/* Use the existing token */
					return $this->organizationSecurityToken;
				}
			} else {
				/* Check if Security Token cached  */
				$isDefined = $this->getCachedSecurityToken("organization", $this->organizationSecurityToken);
				/* Check if the Security Token is still valid */
				if ($isDefined && $this->organizationSecurityToken['expiryTime'] > time()) {
					/* Use cached token */
					return $this->organizationSecurityToken;
				}
			}
			/* Request a new Security Token for the Organization Service */
			$this->organizationSecurityToken = $this->requestSecurityToken($this->settings->loginUrl, $this->settings->crmRegion, $this->settings->username, $this->settings->password);
			/* Cache retrieved token */
			$this->setCachedSecurityToken('organization', $this->organizationSecurityToken);
			/* Save the token, and return it */
			return $this->organizationSecurityToken;
		}

		/**
		 * Get the current Discovery Service security token, or get a new one if necessary 
		 * @ignore
		 */
		public function getDiscoverySecurityToken() {
			/* Check if there is an existing token */
			if ($this->discoverySecurityToken != NULL) {
				/* Check if the Security Token is still valid */
				if ($this->discoverySecurityToken['expiryTime'] > time()) {
					/* Use the existing token */
					return $this->discoverySecurityToken;
				}
			} else {
				/* Check if Security Token cached  */
				$isDefined = $this->getCachedSecurityToken("discovery", $this->discoverySecurityToken);
				/* Check if the Security Token is still valid */
				if ($isDefined && $this->discoverySecurityToken['expiryTime'] > time()) {
					/* Use cached token */
					return $this->discoverySecurityToken;
				}
			}
			/* Request a new Security Token for the Organization Service */
			$this->discoverySecurityToken = $this->requestSecurityToken($this->settings->loginUrl, $this->settings->crmRegion, $this->settings->username, $this->settings->password);
			/* Cache retrieved token */
			$this->setCachedSecurityToken('discovery', $this->discoverySecurityToken);
			/* Save the token, and return it */
			return $this->discoverySecurityToken;
		}

		/**
		 * Generate a DOMNode for the o:Security header required for SOAP requests 
		 * @ignore
		 */
		protected function getSecurityHeaderNode(Array $securityToken) {
			$securityDOM = new DOMDocument();

			$securityHeader = $securityDOM->appendChild($securityDOM->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'o:Security'));
			$securityHeader->setAttribute('s:mustUnderstand', '1');

			$headerTimestamp = $securityHeader->appendChild($securityDOM->createElement('u:Timestamp'));

			$headerTimestamp->setAttribute('u:Id', '_0');
			$headerTimestamp->appendChild($securityDOM->createElement('u:Created', self::getCurrentTime() . 'Z'));
			$headerTimestamp->appendChild($securityDOM->createElement('u:Expires', self::getExpiryTime() . 'Z'));

			$requestedSecurityToken = $securityDOM->createDocumentFragment();
			$requestedSecurityToken->appendXML($securityToken['securityToken']);
			$securityHeader->appendChild($requestedSecurityToken);

			return $securityHeader;
		}

		/**
		 * Request a Security Token from the login microsoftonline server using Username & Password authentication 
		 * @ignore
		 */
		protected function requestSecurityToken($securityServerURI, $loginEndpoint, $loginUsername, $loginPassword) {
			try{
				/* Generate the Security Token Request XML */
				$loginSoapRequest = self::getLoginXML($securityServerURI, $loginEndpoint, $loginUsername, $loginPassword);
				/* Send the Security Token request */
				$security_xml = self::getSoapResponse($securityServerURI, $loginSoapRequest);
				/* Convert the XML into a DOMDocument */
				$securityDOM = new DOMDocument();
				$securityDOM->loadXML($security_xml);
				/* Get the two CipherValue keys */
				$cipherValues = $securityDOM->getElementsbyTagName("CipherValue");
				$securityToken0 = $cipherValues->item(0)->textContent;
				$securityToken1 = $cipherValues->item(1)->textContent;
				/* Get the KeyIdentifier */
				$keyIdentifier = $securityDOM->getElementsbyTagName("KeyIdentifier")->item(0)->textContent;
				/* Get the BinarySecret */
				$binarySecret = $securityDOM->getElementsbyTagName("BinarySecret")->item(0)->textContent;
				/* Set NS attribute to wsse:SecurityTokenReference element */
				$securityDOM->getElementsByTagName('SecurityTokenReference')->item(0)->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
				/* Make life easier - get the entire RequestedSecurityToken section */
				$requestedSecurityToken = $securityDOM->saveXML($securityDOM->getElementsByTagName("RequestedSecurityToken")->item(0));

				preg_match('/<wst:RequestedSecurityToken>(.*)<\/wst:RequestedSecurityToken>/', $requestedSecurityToken, $matches);
				$requestedSecurityToken = $matches[1];
				/* Find the Expiry Time */
				$expiryTime = $securityDOM->getElementsByTagName("RequestSecurityTokenResponse")->item(0)->getElementsByTagName('Expires')->item(0)->textContent;
				/* Convert it to a PHP Timestamp */
				$expiryTime = self::parseTime(substr($expiryTime, 0, -1), '%Y-%m-%dT%H:%M:%S');
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
				AlexaSDK_Logger::log('Got Security Token - Expires at: ' . date('r', $securityToken['expiryTime']));
				AlexaSDK_Logger::log("\tKey Identifier\t: " . $securityToken['keyIdentifier']);
				AlexaSDK_Logger::log("\tSecurity Token 0\t: " . substr($securityToken['securityToken0'], 0, 25) . '...' . substr($securityToken['securityToken0'], -25) . ' (' . strlen($securityToken['securityToken0']) . ')');
				AlexaSDK_Logger::log("\tSecurity Token 1\t: " . substr($securityToken['securityToken1'], 0, 25) . '...' . substr($securityToken['securityToken1'], -25) . ' (' . strlen($securityToken['securityToken1']) . ')');
				AlexaSDK_Logger::log("\tBinary Secret\t: " . $securityToken['binarySecret'] . PHP_EOL);
				/* Return an associative Array */
				return $securityToken;
			}catch(Exception $e){
				AlexaSDK_Logger::log("Exception", $e);
				throw $e;
			}
		}

		/**
		 * Get the XML needed to send a login request to the Username & Password Trust service 
		 * @ignore
		 */
		protected static function getLoginXML($securityServerURI, $loginEndpoint, $loginUsername, $loginPassword) {
			$loginSoapRequest = new DOMDocument();
			$loginEnvelope = $loginSoapRequest->appendChild($loginSoapRequest->createElementNS('http://www.w3.org/2003/05/soap-envelope', 's:Envelope'));
			$loginEnvelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://www.w3.org/2005/08/addressing');
			$loginEnvelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:u', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
			$loginHeader = $loginEnvelope->appendChild($loginSoapRequest->createElement('s:Header'));
			$loginHeader->appendChild($loginSoapRequest->createElement('a:Action', 'http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue'))->setAttribute('s:mustUnderstand', "1");
			$loginHeader->appendChild($loginSoapRequest->createElement('a:MessageId', 'urn:uuid:' . parent::getUuid()));
			$loginHeader->appendChild($loginSoapRequest->createElement('a:ReplyTo'))->appendChild($loginSoapRequest->createElement('a:Address', 'http://www.w3.org/2005/08/addressing/anonymous'));
			$loginHeader->appendChild($loginSoapRequest->createElement('a:To', $securityServerURI))->setAttribute('s:mustUnderstand', "1");
			$loginSecurity = $loginHeader->appendChild($loginSoapRequest->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'o:Security'));
			$loginSecurity->setAttribute('s:mustUnderstand', "1");
			$loginTimestamp = $loginSecurity->appendChild($loginSoapRequest->createElement('u:Timestamp'));
			$loginTimestamp->setAttribute('u:Id', '_0');
			$loginTimestamp->appendChild($loginSoapRequest->createElement('u:Created', self::getCurrentTime() . 'Z'));
			$loginTimestamp->appendChild($loginSoapRequest->createElement('u:Expires', self::getExpiryTime() . 'Z'));
			$loginUsernameToken = $loginSecurity->appendChild($loginSoapRequest->createElement('o:UsernameToken'));
			$loginUsernameToken->setAttribute('u:Id', 'uuid-14bed392-2320-44ae-859d-fa4ec83df57a-1');
			$loginUsernameToken->appendChild($loginSoapRequest->createElement('o:Username', $loginUsername));
			$loginUsernameToken->appendChild($loginSoapRequest->createElement('o:Password', $loginPassword))->setAttribute('Type', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText');

			$loginBody = $loginEnvelope->appendChild($loginSoapRequest->createElementNS('http://www.w3.org/2003/05/soap-envelope', 's:Body'));
			$loginRST = $loginBody->appendChild($loginSoapRequest->createElementNS('http://schemas.xmlsoap.org/ws/2005/02/trust', 't:RequestSecurityToken'));
			$loginAppliesTo = $loginRST->appendChild($loginSoapRequest->createElementNS('http://schemas.xmlsoap.org/ws/2004/09/policy', 'wsp:AppliesTo'));
			$loginEndpointReference = $loginAppliesTo->appendChild($loginSoapRequest->createElement('a:EndpointReference'));
			$loginEndpointReference->appendChild($loginSoapRequest->createElement('a:Address', "urn:" . $loginEndpoint));
			$loginRST->appendChild($loginSoapRequest->createElement('t:RequestType', 'http://schemas.xmlsoap.org/ws/2005/02/trust/Issue'));

			return $loginSoapRequest->saveXML($loginEnvelope);
		}

}
