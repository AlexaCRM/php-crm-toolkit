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

namespace AlexaCRM\CRMToolkit\Auth;

use AlexaCRM\CRMToolkit\Client;
use AlexaCRM\CRMToolkit\InvalidSecurityException;
use AlexaCRM\CRMToolkit\SecurityToken;
use AlexaCRM\CRMToolkit\Settings;
use DOMDocument;

/**
 * Handles authentication within Microsoft Dynamics CRM Online / 365
 */
class OnlineFederation extends Authentication {

    /**
     * Create a new instance of the AlexaCRM\CRMToolkit\AlexaSDK
     *
     * @param Settings $settings
     * @param Client $client
     *
     * @throws \Exception Thrown if TLS 1.2 is not supported by the environment.
     */
    public function __construct( Settings $settings, Client $client ) {
        parent::__construct( $settings, $client );

        $curlVersion = curl_version();
        if ( version_compare( $curlVersion['version'], '7.34', '<' ) || !defined( 'CURL_SSLVERSION_TLSv1_2' ) ) {
            $client->logger->critical( 'TLS v1.2 might not be supported by cURL', [ 'curlVersion' => $curlVersion, 'CURL_SSLVERSION_TLSv1_2' => defined( 'CURL_SSLVERSION_TLSv1_2' ) ] );
        }
    }

    /**
     * Retrieves the security token from the STS.
     *
     * @param string $service
     *
     * @return SecurityToken
     */
    protected function retrieveToken( $service ) {
        $stsUri = $this->getSTSUrl( $this->settings->username );

        if ( $stsUri !== null ) {
            $assertResponse = $this->client->attemptSoapResponse( $stsUri, function() use ( $stsUri ) {
                return $this->generateAssertRequest( $stsUri );
            } );

            $assertToken = $this->extractAssertToken( $assertResponse );

            $tokenResponse = $this->client->attemptSoapResponse( 'sts', function() use ( $assertToken ) {
                return $this->generateAssertTokenRequest( $assertToken );
            } );
        } else {
            $tokenResponse = $this->client->attemptSoapResponse( 'sts', function() use ( $service ) {
                return $this->generateTokenRequest( $service );
            } );
        }

        $token = $this->extractToken( $tokenResponse );

        $this->client->logger->info( 'Issued a new security token [' . $service . '] - expires at: ' . date( 'r', $token->expiryTime ) );

        return $token;
    }

    protected function extractAssertToken( $tokenXML ) {
        $securityDOM = new \DOMDocument();
        $securityDOM->loadXML( $tokenXML );

        if ( $securityDOM->getElementsByTagName( 'Fault')->length > 0 ) {
            $q = new \DOMXPath( $securityDOM );
            throw new \Exception( $q->query( '/s:Envelope/s:Body/s:Fault/s:Reason/s:Text' )->item( 0 )->nodeValue );
        }

        $newToken = new SecurityToken();

        $newToken->securityToken = $securityDOM->saveXML( $securityDOM->getElementsByTagName( "RequestedSecurityToken" )->item( 0 )->firstChild );

        $expiryTime = $securityDOM->getElementsByTagName( "RequestSecurityTokenResponse" )->item( 0 )->getElementsByTagName( 'Expires' )->item( 0 )->textContent;
        $newToken->expiryTime = Client::parseTime( substr( $expiryTime, 0, -1 ), '%Y-%m-%dT%H:%M:%S' );

        return $newToken;
    }

    protected function extractToken( $tokenXML ) {
        $securityDOM = new \DOMDocument();
        $securityDOM->loadXML( $tokenXML );
        $q = new \DOMXPath( $securityDOM );
        $q->registerNamespace( 'S', 'http://www.w3.org/2003/05/soap-envelope' );
        $q->registerNamespace( 'psf', 'http://schemas.microsoft.com/Passport/SoapServices/SOAPFault' );
        if ( $q->query( '/S:Envelope/S:Body/S:Fault' )->length > 0 ) {
            $exceptionString = $q->evaluate( 'string(/S:Envelope/S:Body/S:Fault/S:Reason/S:Text)' );
            $description = $q->evaluate( 'string(/S:Envelope/S:Body/S:Fault/S:Detail/psf:error/psf:internalerror/psf:text)' );
            if ( $description !== '' ) {
                $exceptionString .= '. ' . $description;
            }
            throw new \Exception( $exceptionString );
        }

        $newToken = new SecurityToken();

        $cipherValues = $securityDOM->getElementsByTagName( 'CipherValue' );
        $newToken->securityToken0 = $cipherValues->item( 0 )->textContent;
        $newToken->securityToken1 = $cipherValues->item( 1 )->textContent;

        $newToken->keyIdentifier = $securityDOM->getElementsByTagName( 'KeyIdentifier' )->item( 0 )->textContent;

        $newToken->binarySecret = $securityDOM->getElementsByTagName( 'BinarySecret' )->item( 0 )->textContent;

        if ( $securityDOM->getElementsByTagName( 'SecurityTokenReference' )->item( 0 )->prefix === 'wsse' ) {
            $securityDOM->getElementsByTagName( 'SecurityTokenReference' )->item( 0 )->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd' );
        }

        $newToken->securityToken = $securityDOM->saveXML( $securityDOM->getElementsByTagName( "RequestedSecurityToken" )->item( 0 )->firstChild );

        $expiryTime = $securityDOM->getElementsByTagName( "RequestSecurityTokenResponse" )->item( 0 )->getElementsByTagName( 'Expires' )->item( 0 )->textContent;
        $newToken->expiryTime = Client::parseTime( substr( $expiryTime, 0, -1 ), '%Y-%m-%dT%H:%M:%S' );

        return $newToken;
    }

    protected function generateAssertRequest( $stsUri ) {
        $messageID = Client::getUuid();
        $createdDate = Client::getCurrentTime() . 'Z';
        $expiresDate = Client::getExpiryTime() . 'Z';

        $encUsername = htmlspecialchars( $this->settings->username, ENT_COMPAT | ENT_XML1 );
        $encPassword = htmlspecialchars( $this->settings->password, ENT_COMPAT | ENT_XML1 );

        $request = <<<XML
<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing"
            xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
    <s:Header>
        <a:Action s:mustUnderstand="1">http://docs.oasis-open.org/ws-sx/ws-trust/200512/RST/Issue</a:Action>
        <a:MessageID>urn:uuid:{$messageID}</a:MessageID>
        <a:ReplyTo>
            <a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address>
        </a:ReplyTo>
        <a:To s:mustUnderstand="1">{$stsUri}</a:To>
        <o:Security s:mustUnderstand="1"
                    xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <u:Timestamp u:Id="_0">
                <u:Created>{$createdDate}</u:Created>
                <u:Expires>{$expiresDate}</u:Expires>
            </u:Timestamp>
            <o:UsernameToken u:Id="uuid-317d2e39-d05c-46d6-ac19-b43ffe24d6b6-25">
                <o:Username>{$encUsername}</o:Username>
                <o:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$encPassword}</o:Password>
            </o:UsernameToken>
        </o:Security>
    </s:Header>
    <s:Body>
        <trust:RequestSecurityToken xmlns:trust="http://docs.oasis-open.org/ws-sx/ws-trust/200512">
            <wsp:AppliesTo xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy">
                <wsa:EndpointReference xmlns:wsa="http://www.w3.org/2005/08/addressing">
                    <wsa:Address>urn:federation:MicrosoftOnline</wsa:Address>
                </wsa:EndpointReference>
            </wsp:AppliesTo>
            <trust:KeyType>http://docs.oasis-open.org/ws-sx/ws-trust/200512/Bearer</trust:KeyType>
            <trust:RequestType>http://docs.oasis-open.org/ws-sx/ws-trust/200512/Issue</trust:RequestType>
        </trust:RequestSecurityToken>
    </s:Body>
</s:Envelope>
XML;

        return $request;
    }

    /**
     * @param SecurityToken $assertToken
     *
     * @return string
     */
    protected function generateAssertTokenRequest( $assertToken ) {
        $credentials = $this->getTokenCredentials( 'sts' );

        $loginSoapRequest = new DOMDocument();
        $loginEnvelope    = $loginSoapRequest->appendChild( $loginSoapRequest->createElementNS( 'http://www.w3.org/2003/05/soap-envelope', 's:Envelope' ) );
        $loginEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://www.w3.org/2005/08/addressing' );
        $loginEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:u', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd' );
        $loginHeader = $loginEnvelope->appendChild( $loginSoapRequest->createElement( 's:Header' ) );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:Action', 'http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue' ) )->setAttribute( 's:mustUnderstand', "1" );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:MessageId', 'urn:uuid:' . Client::getUuid() ) );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:ReplyTo' ) )->appendChild( $loginSoapRequest->createElement( 'a:Address', 'http://www.w3.org/2005/08/addressing/anonymous' ) );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:To', $credentials['server'] ) )->setAttribute( 's:mustUnderstand', "1" );
        $loginSecurity = $loginHeader->appendChild( $loginSoapRequest->createElementNS( 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'o:Security' ) );
        $loginSecurity->setAttribute( 's:mustUnderstand', "1" );
        $loginTimestamp = $loginSecurity->appendChild( $loginSoapRequest->createElement( 'u:Timestamp' ) );
        $loginTimestamp->setAttribute( 'u:Id', '_0' );
        $loginTimestamp->appendChild( $loginSoapRequest->createElement( 'u:Created', Client::getCurrentTime() . 'Z' ) );
        $loginTimestamp->appendChild( $loginSoapRequest->createElement( 'u:Expires', Client::getExpiryTime() . 'Z' ) );

        $assert = $loginSoapRequest->createDocumentFragment();
        $assert->appendXML( $assertToken->securityToken );
        $loginSecurity->appendChild( $assert );

        $loginBody              = $loginEnvelope->appendChild( $loginSoapRequest->createElementNS( 'http://www.w3.org/2003/05/soap-envelope', 's:Body' ) );
        $loginRST               = $loginBody->appendChild( $loginSoapRequest->createElementNS( 'http://schemas.xmlsoap.org/ws/2005/02/trust', 't:RequestSecurityToken' ) );
        $loginAppliesTo         = $loginRST->appendChild( $loginSoapRequest->createElementNS( 'http://schemas.xmlsoap.org/ws/2004/09/policy', 'wsp:AppliesTo' ) );
        $loginEndpointReference = $loginAppliesTo->appendChild( $loginSoapRequest->createElement( 'a:EndpointReference' ) );
        $loginEndpointReference->appendChild( $loginSoapRequest->createElement( 'a:Address', "urn:" . $credentials['endpoint'] ) );
        $loginRST->appendChild( $loginSoapRequest->createElement( 't:RequestType', 'http://schemas.xmlsoap.org/ws/2005/02/trust/Issue' ) );

        return $loginSoapRequest->saveXML( $loginEnvelope );
    }

    /**
     * @param string $service
     *
     * @return string
     */
    protected function generateTokenRequest( $service ) {
        $credentials = $this->getTokenCredentials( $service );

        $loginSoapRequest = new DOMDocument();
        $loginEnvelope    = $loginSoapRequest->appendChild( $loginSoapRequest->createElementNS( 'http://www.w3.org/2003/05/soap-envelope', 's:Envelope' ) );
        $loginEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://www.w3.org/2005/08/addressing' );
        $loginEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:u', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd' );
        $loginHeader = $loginEnvelope->appendChild( $loginSoapRequest->createElement( 's:Header' ) );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:Action', 'http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue' ) )->setAttribute( 's:mustUnderstand', "1" );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:MessageId', 'urn:uuid:' . Client::getUuid() ) );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:ReplyTo' ) )->appendChild( $loginSoapRequest->createElement( 'a:Address', 'http://www.w3.org/2005/08/addressing/anonymous' ) );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:To', $credentials['server'] ) )->setAttribute( 's:mustUnderstand', "1" );
        $loginSecurity = $loginHeader->appendChild( $loginSoapRequest->createElementNS( 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'o:Security' ) );
        $loginSecurity->setAttribute( 's:mustUnderstand', "1" );
        $loginTimestamp = $loginSecurity->appendChild( $loginSoapRequest->createElement( 'u:Timestamp' ) );
        $loginTimestamp->setAttribute( 'u:Id', '_0' );
        $loginTimestamp->appendChild( $loginSoapRequest->createElement( 'u:Created', Client::getCurrentTime() . 'Z' ) );
        $loginTimestamp->appendChild( $loginSoapRequest->createElement( 'u:Expires', Client::getExpiryTime() . 'Z' ) );
        $loginUsernameToken = $loginSecurity->appendChild( $loginSoapRequest->createElement( 'o:UsernameToken' ) );
        $loginUsernameToken->setAttribute( 'u:Id', 'uuid-14bed392-2320-44ae-859d-fa4ec83df57a-1' );

        $usernameNode = $loginSoapRequest->createElement( 'o:Username' );
        $usernameNode->appendChild( $loginSoapRequest->createTextNode( $credentials['username'] ) );
        $loginUsernameToken->appendChild( $usernameNode );

        $passwordNode = $loginSoapRequest->createElement( 'o:Password' );
        $passwordNode->setAttribute( 'Type', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText' );
        $passwordNode->appendChild( $loginSoapRequest->createTextNode( $credentials['password'] ) );
        $loginUsernameToken->appendChild( $passwordNode );

        $loginBody              = $loginEnvelope->appendChild( $loginSoapRequest->createElementNS( 'http://www.w3.org/2003/05/soap-envelope', 's:Body' ) );
        $loginRST               = $loginBody->appendChild( $loginSoapRequest->createElementNS( 'http://schemas.xmlsoap.org/ws/2005/02/trust', 't:RequestSecurityToken' ) );
        $loginAppliesTo         = $loginRST->appendChild( $loginSoapRequest->createElementNS( 'http://schemas.xmlsoap.org/ws/2004/09/policy', 'wsp:AppliesTo' ) );
        $loginEndpointReference = $loginAppliesTo->appendChild( $loginSoapRequest->createElement( 'a:EndpointReference' ) );
        $loginEndpointReference->appendChild( $loginSoapRequest->createElement( 'a:Address', "urn:" . $credentials['endpoint'] ) );
        $loginRST->appendChild( $loginSoapRequest->createElement( 't:RequestType', 'http://schemas.xmlsoap.org/ws/2005/02/trust/Issue' ) );

        return $loginSoapRequest->saveXML( $loginEnvelope );
    }

    /**
     * Retrieves the correct STS endpoint URL. Useful for federated AAD configurations.
     *
     * @param $login string
     *
     * @return null|string
     * @throws \Exception
     */
    protected function getSTSUrl( $login ) {
        $content = [ 'login' => $this->settings->username, 'xml' => 1 ];

        $cURLHandle = curl_init();
        curl_setopt( $cURLHandle, CURLOPT_URL, 'https://login.microsoftonline.com/GetUserRealm.srf' );
        curl_setopt( $cURLHandle, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $cURLHandle, CURLOPT_TIMEOUT, Client::getConnectorTimeout() );

        if ( $this->settings->caPath ) {
            curl_setopt( $cURLHandle, CURLOPT_CAINFO, $this->settings->caPath );
        }

        if ( $this->settings->ignoreSslErrors ) {
            curl_setopt( $cURLHandle, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt( $cURLHandle, CURLOPT_SSL_VERIFYHOST, 0 );
        }

        curl_setopt( $cURLHandle, CURLOPT_SSLVERSION, defined( 'CURL_SSLVERSION_TLSv1_2' )? CURL_SSLVERSION_TLSv1_2 : 6 );

        if( $this->settings->proxy ) {
          curl_setopt( $cURLHandle, CURLOPT_PROXY, $this->settings->proxy );
        }

        curl_setopt( $cURLHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt( $cURLHandle, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded' ] );
        curl_setopt( $cURLHandle, CURLOPT_POST, 1 );
        curl_setopt( $cURLHandle, CURLOPT_POSTFIELDS, http_build_query( $content ) );
        curl_setopt( $cURLHandle, CURLOPT_FOLLOWLOCATION, true ); // follow redirects
        curl_setopt( $cURLHandle, CURLOPT_POSTREDIR, 7 ); // 1 | 2 | 4 (301, 302, 303 redirects mask)
        curl_setopt( $cURLHandle, CURLOPT_HEADER, false );

        /* Execute the cURL request, get the XML response */
        $responseXML = curl_exec( $cURLHandle );

        /* Check for cURL errors */
        if ( curl_errno( $cURLHandle ) != CURLE_OK ) {
            throw new \Exception( 'cURL Error: ' . curl_error( $cURLHandle ) );
        }
        /* Check for HTTP errors */
        $httpResponse = curl_getinfo( $cURLHandle, CURLINFO_HTTP_CODE );
        $curlInfo = curl_getinfo( $cURLHandle );
        $curlErrNo = curl_errno( $cURLHandle );
        curl_close( $cURLHandle );

        if ( empty( $responseXML ) ) {
            throw new \Exception( 'Empty response from the GetUserRealm endpoint.' );
        }

        if ( strpos( $responseXML, '<IsFederatedNS>true</IsFederatedNS>' ) === false ) {
            return null;
        }

        preg_match( '~<STSAuthURL>(.*?)</STSAuthURL>~', $responseXML, $stsMatch );
        $authUri = $stsMatch[1];

        /*
         * The toolkit only supports one ADFS endpoint, /adfs/services/trust/13/usernamemixed.
         * In non-standard environments, STSAuthURL may point to a different endpoint.
         * The default behavior is to always point to the UsernameMixed endpoint.
         * This setting allows to override such behavior and instead use the exact presented value.
         */
        if ( $this->settings->strictFederatedSTS ) {
            return $authUri;
        }

        return preg_replace( '~^https?://(.*?)/.*$~', 'https://$1/adfs/services/trust/13/usernamemixed', $authUri );
    }

    /**
     * Generates a Security section for SOAP envelope header.
     *
     * @param string $service
     *
     * @return \DOMNode
     */
    public function generateTokenHeader( $service ) {
        $token = $this->getToken( $service );

        $tokenHeader = '<o:Security xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" s:mustUnderstand="1" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">'
                       . '<u:Timestamp u:Id="_0"><u:Created>' . Client::getCurrentTime() . 'Z</u:Created>'
                       . '<u:Expires>' . Client::getExpiryTime() . 'Z</u:Expires></u:Timestamp>'
                       . $token->securityToken
                       . '</o:Security>';

        $headerDom = new DOMDocument();
        $headerDom->loadXML( $tokenHeader );

        return $headerDom->documentElement;
    }

}
