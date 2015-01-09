<?php

if (!class_exists("AlexaSDK_Office365")) :
    
    
    class AlexaSDK_Office365 extends AlexaSDK{
    
        public $username;
        public $password;
        public $organizationUrl;
        public $discoveryUrl;
        public $region;
        public $settings;
        
        /* Security fields */
        public $keyIdentifier;
        public $securityToken0;
        public $securityToken1;
        
        private $organizationSecurityToken;
        
        function __construct($settings){
            
            $this->username = $settings->username;
            $this->password = $settings->password;
            $this->organizationUrl = $settings->organizationUrl;
            $this->discoveryUrl = $settings->discoveryUrl;
            $this->region = $settings->crmRegion;
            $this->settings = $settings;
        }
        
        function BuildAuthSoap() {

            $OCPRequest = '
                <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
	                xmlns:a="http://www.w3.org/2005/08/addressing"
	                xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
	                <s:Header>
		                <a:Action s:mustUnderstand="1">http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue
		                </a:Action>
		                <a:MessageID>urn:uuid:' . parent::getUuid() . '
		                </a:MessageID>
		                <a:ReplyTo>
			                <a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address>
		                </a:ReplyTo>
		                <a:To s:mustUnderstand="1">https://login.microsoftonline.com/RST2.srf</a:To>
		                <o:Security s:mustUnderstand="1"
			                xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			                <u:Timestamp u:Id="_0">
				                <u:Created>' . parent::getCurrentTime() . 'Z</u:Created>
                                                <u:Expires>' . parent::getExpiryTime() . 'Z</u:Expires>
			                </u:Timestamp>
			                <o:UsernameToken u:Id="uuid-14bed392-2320-44ae-859d-fa4ec83df57a-1">
				                <o:Username>' . $this->username . '</o:Username>
				                <o:Password
					                Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' . $this->password . '</o:Password>
			                </o:UsernameToken>
		                </o:Security>
	                </s:Header>
	                <s:Body>
		                <t:RequestSecurityToken xmlns:t="http://schemas.xmlsoap.org/ws/2005/02/trust">
			                <wsp:AppliesTo xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy">
				                <a:EndpointReference>
					                <a:Address>urn:' . $this->region . '</a:Address>
				                </a:EndpointReference>
			                </wsp:AppliesTo>
			                <t:RequestType>http://schemas.xmlsoap.org/ws/2005/02/trust/Issue
			                </t:RequestType>
		                </t:RequestSecurityToken>
	                </s:Body>
                </s:Envelope>';
            
            return $OCPRequest;
        }
    
        public function Authenticate() {     
            try{
                $SOAPresult = parent::GetSOAPResponse('https://login.microsoftonline.com/RST2.srf', $this->BuildAuthSoap());
     
                $responsedom = new DomDocument();
                $responsedom->loadXML($SOAPresult);

                $cipherValues = $responsedom->getElementsbyTagName("CipherValue");

                if (isset($cipherValues) && $cipherValues->length > 0) {
                    $this->securityToken0 = $cipherValues->item(0)->textContent;
                    $this->securityToken1 = $cipherValues->item(1)->textContent;
                    $this->keyIdentifier = $responsedom->getElementsbyTagName("KeyIdentifier")->item(0)->textContent;
                    $this->uuid_identifier = $responsedom->getElementsbyTagName("KeyIdentifier")->item(1)->textContent;
                    $this->serverSecret = $responsedom->getElementsbyTagName("BinarySecret")->item(0)->textContent;
                    return true;
                } else {
                    return false;
                }
            }  catch (Exception $e) {
                return false;
            }
        }
        
        public function getHeader($soapAction) {
            $header = '<s:Header>
                   <a:Action s:mustUnderstand="1">http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/' . $soapAction . '</a:Action>
                    <a:MessageID>urn:uuid:' . parent::getUuid() . '</a:MessageID>
                    <a:ReplyTo>
                      <a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address>
                    </a:ReplyTo>
                   <a:To s:mustUnderstand="1">' . $this->organizationUrl . '</a:To>
                    <o:Security s:mustUnderstand="1"
                    xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                      <u:Timestamp u:Id="_0">
                        <u:Created>' . parent::getCurrentTime() . 'Z</u:Created>
                        <u:Expires>' . parent::getExpiryTime() . 'Z</u:Expires>
                      </u:Timestamp>
                      <EncryptedData Id="Assertion0"
                      Type="http://www.w3.org/2001/04/xmlenc#Element"
                      xmlns="http://www.w3.org/2001/04/xmlenc#">
                        <EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#tripledes-cbc">
                        </EncryptionMethod>
                        <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                          <EncryptedKey>
                            <EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p">
                            </EncryptionMethod>
                            <ds:KeyInfo Id="keyinfo">
                              <wsse:SecurityTokenReference xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                                <wsse:KeyIdentifier EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary"
                                ValueType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509SubjectKeyIdentifier">
                                ' . $this->keyIdentifier . '</wsse:KeyIdentifier>
                              </wsse:SecurityTokenReference>
                            </ds:KeyInfo>
                            <CipherData>
                              <CipherValue>
                              ' . $this->securityToken0 . '</CipherValue>
                            </CipherData>
                          </EncryptedKey>
                        </ds:KeyInfo>
                        <CipherData>
                          <CipherValue>
                          ' . $this->securityToken1 . '</CipherValue>
                        </CipherData>
                      </EncryptedData>
                    </o:Security>
                  </s:Header>';

            return $header;
        }
        
        
        
        public function requestRetrieveOrganization(){
            
            $request = '<s:envelope xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:s="http://www.w3.org/2003/05/soap-envelope">
                        '.$this->getDiscoveryHeader('Execute').'
                        <s:body>
                            <Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Discovery">
                                <request i:type="RetrieveOrganizationResponse" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                    <AccessType>Default</AccessType>
                                    <Release>Current</Release>
                                </request>
                            </Execute>
                        </s:body>
                    </s:envelope>';
            
            return $request;
            
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
		$this->organizationSecurityToken = $this->requestSecurityToken($this->settings->loginUrl, $this->region, $this->settings->username, $this->settings->password);
                
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
                
                $headerTimestamp = $securityHeader->appendChild($securityDOM->createElement('u:Timestamp'));
		
                $headerTimestamp->setAttribute('u:Id', '_0');
		$headerTimestamp->appendChild($securityDOM->createElement('u:Created', self::getCurrentTime().'Z'));
		$headerTimestamp->appendChild($securityDOM->createElement('u:Expires', self::getExpiryTime().'Z'));

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
                
                /* Set NS attribute to wsse:SecurityTokenReference element */
                $securityDOM->getElementsByTagName('SecurityTokenReference')->item(0)->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
                
                /* Make life easier - get the entire RequestedSecurityToken section */
		$requestedSecurityToken = $securityDOM->saveXML($securityDOM->getElementsByTagName("RequestedSecurityToken")->item(0));
                
		preg_match('/<wst:RequestedSecurityToken>(.*)<\/wst:RequestedSecurityToken>/', $requestedSecurityToken, $matches);
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
		$loginHeader->appendChild($loginSoapRequest->createElement('a:Action', 'http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue'))->setAttribute('s:mustUnderstand', "1");
                $loginHeader->appendChild($loginSoapRequest->createElement('a:MessageId', 'urn:uuid:' . parent::getUuid()));
		$loginHeader->appendChild($loginSoapRequest->createElement('a:ReplyTo'))->appendChild($loginSoapRequest->createElement('a:Address', 'http://www.w3.org/2005/08/addressing/anonymous'));
		$loginHeader->appendChild($loginSoapRequest->createElement('a:To', $securityServerURI))->setAttribute('s:mustUnderstand', "1");
		$loginSecurity = $loginHeader->appendChild($loginSoapRequest->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'o:Security'));
		$loginSecurity->setAttribute('s:mustUnderstand', "1");
		$loginTimestamp = $loginSecurity->appendChild($loginSoapRequest->createElement('u:Timestamp'));
		$loginTimestamp->setAttribute('u:Id', '_0');
		$loginTimestamp->appendChild($loginSoapRequest->createElement('u:Created', self::getCurrentTime().'Z'));
		$loginTimestamp->appendChild($loginSoapRequest->createElement('u:Expires', self::getExpiryTime().'Z'));
		$loginUsernameToken = $loginSecurity->appendChild($loginSoapRequest->createElement('o:UsernameToken'));
		$loginUsernameToken->setAttribute('u:Id', 'uuid-14bed392-2320-44ae-859d-fa4ec83df57a-1');
		$loginUsernameToken->appendChild($loginSoapRequest->createElement('o:Username', $loginUsername));
		$loginUsernameToken->appendChild($loginSoapRequest->createElement('o:Password', $loginPassword))->setAttribute('Type', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText');
		
		$loginBody = $loginEnvelope->appendChild($loginSoapRequest->createElementNS('http://www.w3.org/2003/05/soap-envelope', 's:Body'));
		$loginRST = $loginBody->appendChild($loginSoapRequest->createElementNS('http://schemas.xmlsoap.org/ws/2005/02/trust', 't:RequestSecurityToken'));
		$loginAppliesTo = $loginRST->appendChild($loginSoapRequest->createElementNS('http://schemas.xmlsoap.org/ws/2004/09/policy', 'wsp:AppliesTo'));
		$loginEndpointReference = $loginAppliesTo->appendChild($loginSoapRequest->createElement('a:EndpointReference'));
		$loginEndpointReference->appendChild($loginSoapRequest->createElement('a:Address', "urn:".$loginEndpoint));
		$loginRST->appendChild($loginSoapRequest->createElement('t:RequestType', 'http://schemas.xmlsoap.org/ws/2005/02/trust/Issue'));
		
		return $loginSoapRequest->saveXML($loginEnvelope);
	}
        
        
        
        
        
        /**
	 * @ignore
	 * @deprecated
	 */
        public function getDiscoveryHeader($action) {
            $header = '<s:Header>
                <a:Action s:mustUnderstand="1">http://schemas.microsoft.com/xrm/2011/Contracts/Discovery/IDiscoveryService/Execute</a:Action>
                <a:MessageID>urn:uuid:' . parent::getUuid() . '</a:MessageID>
                <a:ReplyTo>
                    <a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address>
                </a:ReplyTo>
                <a:To s:mustUnderstand="1">https://disco.crm4.dynamics.com/XRMServices/2011/Discovery.svc</a:To>
                <o:Security s:mustUnderstand="1"
                    xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                    <u:Timestamp u:Id="_0">
                        <u:Created>' . parent::getCurrentTime() . 'Z</u:Created>
                        <u:Expires>' . parent::getExpiryTime() . 'Z</u:Expires>
                    </u:Timestamp>
                     <EncryptedData Id="Assertion0"
                      Type="http://www.w3.org/2001/04/xmlenc#Element"
                      xmlns="http://www.w3.org/2001/04/xmlenc#">
                        <EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#tripledes-cbc">
                        </EncryptionMethod>
                        <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                          <EncryptedKey>
                            <EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p">
                            </EncryptionMethod>
                            <ds:KeyInfo Id="keyinfo">
                              <wsse:SecurityTokenReference xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                                <wsse:KeyIdentifier EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary"
                                ValueType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509SubjectKeyIdentifier">' . $this->keyIdentifier . '</wsse:KeyIdentifier>
                              </wsse:SecurityTokenReference>
                            </ds:KeyInfo>
                            <CipherData>
                              <CipherValue>' . $this->securityToken0 . '</CipherValue>
                            </CipherData>
                          </EncryptedKey>
                        </ds:KeyInfo>
                        <CipherData>
                          <CipherValue>' . $this->securityToken1 . '</CipherValue>
                        </CipherData>
                      </EncryptedData>
                </o:Security>
            </s:Header>';

            return $header;
        }
        
        
    }
    
    
endif;