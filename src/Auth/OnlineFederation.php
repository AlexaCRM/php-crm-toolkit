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
use DOMDocument;

/**
 * Handles authentication within Microsoft Dynamics CRM Online / 365
 */
class OnlineFederation extends Authentication {

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
     * Generates a Security section for SOAP envelope header.
     *
     * @param string $service
     *
     * @return \DOMNode
     */
    public function generateTokenHeader( $service ) {
        $securityDOM = new DOMDocument();

        $token = $this->getToken( $service );

        $securityHeader = $securityDOM->appendChild( $securityDOM->createElementNS( 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'o:Security' ) );
        $securityHeader->setAttribute( 's:mustUnderstand', '1' );

        $headerTimestamp = $securityHeader->appendChild( $securityDOM->createElement( 'u:Timestamp' ) );

        $headerTimestamp->setAttribute( 'u:Id', '_0' );
        $headerTimestamp->appendChild( $securityDOM->createElement( 'u:Created', Client::getCurrentTime() . 'Z' ) );
        $headerTimestamp->appendChild( $securityDOM->createElement( 'u:Expires', Client::getExpiryTime() . 'Z' ) );

        $requestedSecurityToken = $securityDOM->createDocumentFragment();
        $requestedSecurityToken->appendXML( $token->securityToken );
        $securityHeader->appendChild( $requestedSecurityToken );

        return $securityHeader;
    }

}
