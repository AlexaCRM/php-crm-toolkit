<?php
/**
 * AlexaSDK_Federation.php
 * 
 * @author alexacrm.com.au
 * @version 1.0
 * @package AlexaSDK
 */


class AlexaSDK_Federation extends AlexaSDK{
    
        public $username;
        public $password;
        public $organizationUrl;
        public $discoveryUrl;
        public $loginUrl;
        public $settings;
        
        /* Security fields */
        private $X509IssuerName;
        private $X509SerialNumber;
        private $chipperValue;
        private $chipperValue2;
        private $BinarySecret;
        private $keyInfo;
        
        
        private $organizationSecurityToken;
        
        function __construct($settings){
            $this->username = $settings->username;
            $this->password = $settings->password;
            $this->organizationUrl = $settings->organizationUrl;
            $this->discoveryUrl = $settings->discoveryUrl;
            $this->loginUrl = $settings->loginUrl;
            $this->settings = $settings;
        }
        
        function BuildAuthSoap(){
            
            $request = '
            <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <s:Header>
                    <a:Action s:mustUnderstand="1">http://docs.oasis-open.org/ws-sx/ws-trust/200512/RST/Issue</a:Action>
                    <a:MessageID>urn:uuid:' . parent::getUuid() . '</a:MessageID>
                    <a:ReplyTo>
                        <a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address>
                    </a:ReplyTo>
                    <a:To s:mustUnderstand="1">' . $this->loginUrl . '</a:To>
                    <o:Security s:mustUnderstand="1" xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                        <u:Timestamp u:Id="_0">
                            <u:Created>' . parent::getCurrentTime() . 'Z</u:Created>
                            <u:Expires>' . parent::getExpiryTime() . 'Z</u:Expires>
                        </u:Timestamp>
                        <o:UsernameToken u:Id="uuid-0978ded4-79ce-4226-97ac-fab2c8893423-8"> 
                            <o:Username>' . $this->username . '</o:Username>
                            <o:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' . $this->password . '</o:Password>
                        </o:UsernameToken>
                    </o:Security>
                </s:Header>
                <s:Body>
                    <trust:RequestSecurityToken xmlns:trust="http://docs.oasis-open.org/ws-sx/ws-trust/200512">
                        <wsp:AppliesTo xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy">
                            <a:EndpointReference>
                                <a:Address>' . $this->organizationUrl . '</a:Address>
                            </a:EndpointReference>
                        </wsp:AppliesTo>
                        <trust:RequestType>http://docs.oasis-open.org/ws-sx/ws-trust/200512/Issue</trust:RequestType>
                    </trust:RequestSecurityToken>
                </s:Body>
            </s:Envelope>';
            
            return $request;
        }
        
        public function Authenticate(){
            try{
                $response = parent::GetSOAPResponse($this->loginUrl, $this->BuildAuthSoap());

                $responsedom = new DomDocument();
                $responsedom->loadXML($response);

                $cipherValues = $responsedom->getElementsbyTagName("CipherValue");

                if (isset($cipherValues) && $cipherValues->length > 0) {
                    $this->chipperValue = $cipherValues->item(0)->textContent;
                    $this->chipperValue2 = $cipherValues->item(1)->textContent;
                    $this->X509IssuerName = $responsedom->getElementsbyTagName("X509IssuerName")->item(0)->textContent;
                    $this->X509SerialNumber = $responsedom->getElementsbyTagName("X509SerialNumber")->item(0)->textContent;
                    $this->BinarySecret = $responsedom->getElementsbyTagName("BinarySecret")->item(0)->textContent;

                    $response = str_replace("o:KeyIdentifier", "KeyInfo1", $response);

                    $responsedom = new DomDocument();
                    $responsedom->loadXML($response);

                    $this->keyInfo = $responsedom->getElementsbyTagName("KeyInfo1")->item(0)->textContent;
                    
                    return true;
                } else {
                    return false;
                }
            }  catch (Exception $e) {
                return false;
            }
        }
        
        
        public function getHeader($soapAction)
        {
            $created = parent::getCurrentTime();
            $expires = parent::getExpiryTime();

            $timestamp = '<u:Timestamp xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" u:Id="_0"><u:Created>' . $created . 'Z</u:Created><u:Expires>' . $expires . 'Z</u:Expires></u:Timestamp>';

            $t = new DOMDocument();
            $t->loadXML($timestamp);
            $canonicalTime = $t->documentElement->C14N(TRUE, FALSE);

            $digestValue = base64_encode(sha1($canonicalTime, true));

            $signedInfo = '<SignedInfo xmlns="http://www.w3.org/2000/09/xmldsig#"><CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></CanonicalizationMethod><SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#hmac-sha1"></SignatureMethod><Reference URI="#_0"><Transforms><Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></Transform></Transforms><DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"></DigestMethod><DigestValue>' . $digestValue . '</DigestValue></Reference></SignedInfo>';

            $d = new DOMDocument();
            $d->loadXML($signedInfo);
            $canonicalXml = $d->documentElement->C14N(TRUE, FALSE);

            $signatureValue = base64_encode(hash_hmac("sha1", $canonicalXml, base64_decode($this->BinarySecret), true));

            $header = '<s:Header><a:Action s:mustUnderstand="1">http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/'.$soapAction.'</a:Action><SdkClientVersion xmlns="http://schemas.microsoft.com/xrm/2011/Contracts">6.1.0000.0542</SdkClientVersion><a:MessageID>urn:uuid:1f53e235-607c-4361-950b-6c28cec3bee2</a:MessageID><a:ReplyTo><a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address></a:ReplyTo><a:To s:mustUnderstand="1">' . $this->organizationUrl . '</a:To><o:Security s:mustUnderstand="1" xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><u:Timestamp u:Id="_0"><u:Created>' . $created . 'Z</u:Created><u:Expires>' . $expires . 'Z</u:Expires></u:Timestamp><xenc:EncryptedData Type="http://www.w3.org/2001/04/xmlenc#Element" xmlns:xenc="http://www.w3.org/2001/04/xmlenc#"><xenc:EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#aes256-cbc"/><KeyInfo xmlns="http://www.w3.org/2000/09/xmldsig#"><e:EncryptedKey xmlns:e="http://www.w3.org/2001/04/xmlenc#"><e:EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p"><DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/></e:EncryptionMethod><KeyInfo><o:SecurityTokenReference><X509Data><X509IssuerSerial><X509IssuerName>CN=StartCom Class 2 Primary Intermediate Server CA, OU=Secure Digital Certificate Signing, O=StartCom Ltd., C=IL</X509IssuerName><X509SerialNumber>139034</X509SerialNumber></X509IssuerSerial></X509Data></o:SecurityTokenReference></KeyInfo><e:CipherData><e:CipherValue>' . $this->chipperValue . '</e:CipherValue></e:CipherData></e:EncryptedKey></KeyInfo><xenc:CipherData><xenc:CipherValue>' . $this->chipperValue2 . '</xenc:CipherValue></xenc:CipherData></xenc:EncryptedData><Signature xmlns="http://www.w3.org/2000/09/xmldsig#"><SignedInfo><CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/><SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#hmac-sha1"/><Reference URI="#_0"><Transforms><Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/></Transforms><DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/><DigestValue>' . $digestValue . '</DigestValue></Reference></SignedInfo><SignatureValue>' . $signatureValue . '</SignatureValue><KeyInfo><o:SecurityTokenReference k:TokenType="http://docs.oasis-open.org/wss/oasis-wss-saml-token-profile-1.1#SAMLV1.1" xmlns:k="http://docs.oasis-open.org/wss/oasis-wss-wssecurity-secext-1.1.xsd"><o:KeyIdentifier ValueType="http://docs.oasis-open.org/wss/oasis-wss-saml-token-profile-1.0#SAMLAssertionID">' . $this->keyInfo . '</o:KeyIdentifier></o:SecurityTokenReference></KeyInfo></Signature></o:Security></s:Header>';
            
            return $header;
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
		$this->organizationSecurityToken = $this->requestSecurityToken($this->settings->loginUrl, $this->organizationUrl, $this->settings->username, $this->settings->password);
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