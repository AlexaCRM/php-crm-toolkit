<?php
/**
 * AlexaSDK_Federation.php
 * 
 * @author alexacrm.com.au
 * @version 1.0
 * @package AlexaSDK\Authentication
 * @subpackage Authentication
 */

/**
 * This class used to authenticate to Internet-Facing Deployment Microsoft Dynamics CRM
 */  
class AlexaSDK_Federation extends AlexaSDK{
    
        /**
         * Global SDK settings
         * 
         * @var AlexaSDK_Settings Instance of AlexaSDK_Settings class
         */
        public $settings;
        
        /**
         *  Token that used to construct SOAP requests
         * 
         * @var Array 
         */
        private $organizationSecurityToken;
        
        
        /**
         * Create a new instance of the AlexaSDK
         * 
         * @param AlexaSDK_Settings $_settings
         * 
         * @return AlexaSDK_Federation
         */
        function __construct($_settings){
            
                $this->settings = $_settings;
            
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
		}
		/* Request a new Security Token for the Organization Service */
		$this->organizationSecurityToken = $this->requestSecurityToken($this->settings->loginUrl, $this->settings->organizationUrl, $this->settings->username, $this->settings->password);
		/* Save the token, and return it */
		return $this->organizationSecurityToken;
	}
        
        
        /**
	 * Generate a DOMNode for the o:Security header required for SOAP requests 
	 * @ignore
	 */
	protected function getSecurityHeaderNode(Array $securityToken) {
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
	}
        
        
        /**
	 * Request a Security Token from the ADFS server using Username & Password authentication 
	 * @ignore
	 */
	protected function requestSecurityToken($securityServerURI, $loginEndpoint, $loginUsername, $loginPassword) {
            
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
	 * Get the XML needed to send a login request to the Username & Password Trust service 
	 * @ignore
	 */
	protected static function getLoginXML($securityServerURI, $loginEndpoint, $loginUsername, $loginPassword) {
		$loginSoapRequest = new DOMDocument();
		$loginEnvelope = $loginSoapRequest->appendChild($loginSoapRequest->createElementNS('http://www.w3.org/2003/05/soap-envelope', 's:Envelope'));
		$loginEnvelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://www.w3.org/2005/08/addressing');
		$loginEnvelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:u', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
		$loginHeader = $loginEnvelope->appendChild($loginSoapRequest->createElement('s:Header'));
		$loginHeader->appendChild($loginSoapRequest->createElement('a:Action', 'http://docs.oasis-open.org/ws-sx/ws-trust/200512/RST/Issue'))->setAttribute('s:mustUnderstand', "1");
		$loginHeader->appendChild($loginSoapRequest->createElement('a:ReplyTo'))->appendChild($loginSoapRequest->createElement('a:Address', 'http://www.w3.org/2005/08/addressing/anonymous'));
		$loginHeader->appendChild($loginSoapRequest->createElement('a:To', $securityServerURI))->setAttribute('s:mustUnderstand', "1");
		$loginSecurity = $loginHeader->appendChild($loginSoapRequest->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'o:Security'));
		$loginSecurity->setAttribute('s:mustUnderstand', "1");
		$loginTimestamp = $loginSecurity->appendChild($loginSoapRequest->createElement('u:Timestamp'));
		$loginTimestamp->setAttribute('u:Id', '_0');
		$loginTimestamp->appendChild($loginSoapRequest->createElement('u:Created', self::getCurrentTime().'Z'));
		$loginTimestamp->appendChild($loginSoapRequest->createElement('u:Expires', self::getExpiryTime().'Z'));
		$loginUsernameToken = $loginSecurity->appendChild($loginSoapRequest->createElement('o:UsernameToken'));
		$loginUsernameToken->setAttribute('u:Id', 'user');
		$loginUsernameToken->appendChild($loginSoapRequest->createElement('o:Username', $loginUsername));
		$loginUsernameToken->appendChild($loginSoapRequest->createElement('o:Password', $loginPassword))->setAttribute('Type', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText');
		
		$loginBody = $loginEnvelope->appendChild($loginSoapRequest->createElementNS('http://www.w3.org/2003/05/soap-envelope', 's:Body'));
		$loginRST = $loginBody->appendChild($loginSoapRequest->createElementNS('http://docs.oasis-open.org/ws-sx/ws-trust/200512', 'trust:RequestSecurityToken'));
		$loginAppliesTo = $loginRST->appendChild($loginSoapRequest->createElementNS('http://schemas.xmlsoap.org/ws/2004/09/policy', 'wsp:AppliesTo'));
		$loginEndpointReference = $loginAppliesTo->appendChild($loginSoapRequest->createElement('a:EndpointReference'));
		$loginEndpointReference->appendChild($loginSoapRequest->createElement('a:Address', $loginEndpoint));
		$loginRST->appendChild($loginSoapRequest->createElement('trust:RequestType', 'http://docs.oasis-open.org/ws-sx/ws-trust/200512/Issue'));
		
		return $loginSoapRequest->saveXML($loginEnvelope);
	}
    
}