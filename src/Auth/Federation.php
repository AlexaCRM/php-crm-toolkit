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
 * Handles authentication within Internet-Facing Deployment of Microsoft Dynamics CRM
 */
class Federation extends Authentication {

    /**
     * @param string $service
     *
     * @return string
     */
    protected function generateTokenRequest( $service ) {
        $credentials = $this->getTokenCredentials();

        $loginSoapRequest = new DOMDocument();
        $loginEnvelope    = $loginSoapRequest->appendChild( $loginSoapRequest->createElementNS( 'http://www.w3.org/2003/05/soap-envelope', 's:Envelope' ) );
        $loginEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://www.w3.org/2005/08/addressing' );
        $loginEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:u', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd' );
        $loginHeader = $loginEnvelope->appendChild( $loginSoapRequest->createElement( 's:Header' ) );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:Action', 'http://docs.oasis-open.org/ws-sx/ws-trust/200512/RST/Issue' ) )->setAttribute( 's:mustUnderstand', "1" );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:ReplyTo' ) )->appendChild( $loginSoapRequest->createElement( 'a:Address', 'http://www.w3.org/2005/08/addressing/anonymous' ) );
        $loginHeader->appendChild( $loginSoapRequest->createElement( 'a:To', $credentials['server'] ) )->setAttribute( 's:mustUnderstand', "1" );
        $loginSecurity = $loginHeader->appendChild( $loginSoapRequest->createElementNS( 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'o:Security' ) );
        $loginSecurity->setAttribute( 's:mustUnderstand', "1" );
        $loginTimestamp = $loginSecurity->appendChild( $loginSoapRequest->createElement( 'u:Timestamp' ) );
        $loginTimestamp->setAttribute( 'u:Id', '_0' );
        $loginTimestamp->appendChild( $loginSoapRequest->createElement( 'u:Created', Client::getCurrentTime() . 'Z' ) );
        $loginTimestamp->appendChild( $loginSoapRequest->createElement( 'u:Expires', Client::getExpiryTime() . 'Z' ) );
        $loginUsernameToken = $loginSecurity->appendChild( $loginSoapRequest->createElement( 'o:UsernameToken' ) );
        $loginUsernameToken->setAttribute( 'u:Id', 'user' );

        $usernameNode = $loginSoapRequest->createElement( 'o:Username' );
        $usernameNode->appendChild( $loginSoapRequest->createTextNode( $credentials['username'] ) );
        $loginUsernameToken->appendChild( $usernameNode );

        $passwordNode = $loginSoapRequest->createElement( 'o:Password' );
        $passwordNode->setAttribute( 'Type', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText' );
        $passwordNode->appendChild( $loginSoapRequest->createTextNode( $credentials['password'] ) );
        $loginUsernameToken->appendChild( $passwordNode );

        $loginBody              = $loginEnvelope->appendChild( $loginSoapRequest->createElementNS( 'http://www.w3.org/2003/05/soap-envelope', 's:Body' ) );
        $loginRST               = $loginBody->appendChild( $loginSoapRequest->createElementNS( 'http://docs.oasis-open.org/ws-sx/ws-trust/200512', 'trust:RequestSecurityToken' ) );
        $loginAppliesTo         = $loginRST->appendChild( $loginSoapRequest->createElementNS( 'http://schemas.xmlsoap.org/ws/2004/09/policy', 'wsp:AppliesTo' ) );
        $loginEndpointReference = $loginAppliesTo->appendChild( $loginSoapRequest->createElement( 'a:EndpointReference' ) );
        $loginEndpointReference->appendChild( $loginSoapRequest->createElement( 'a:Address', $credentials['endpoint'] ) );
        $loginRST->appendChild( $loginSoapRequest->createElement( 'trust:RequestType', 'http://docs.oasis-open.org/ws-sx/ws-trust/200512/Issue' ) );

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
        $headerTimestamp = $securityHeader->appendChild( $securityDOM->createElementNS( 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd', 'u:Timestamp' ) );
        $headerTimestamp->setAttribute( 'u:Id', '_0' );
        $headerTimestamp->appendChild( $securityDOM->createElement( 'u:Created', Client::getCurrentTime() . 'Z' ) );
        $headerTimestamp->appendChild( $securityDOM->createElement( 'u:Expires', Client::getExpiryTime() . 'Z' ) );

        $requestedSecurityToken = $securityDOM->createDocumentFragment();
        $requestedSecurityToken->appendXML( $token->securityToken );
        $securityHeader->appendChild( $requestedSecurityToken );

        $signatureNode  = $securityHeader->appendChild( $securityDOM->createElementNS( 'http://www.w3.org/2000/09/xmldsig#', 'Signature' ) );
        $signedInfoNode = $signatureNode->appendChild( $securityDOM->createElement( 'SignedInfo' ) );
        $signedInfoNode->appendChild( $securityDOM->createElement( 'CanonicalizationMethod' ) )->setAttribute( 'Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#' );
        $signedInfoNode->appendChild( $securityDOM->createElement( 'SignatureMethod' ) )->setAttribute( 'Algorithm', 'http://www.w3.org/2000/09/xmldsig#hmac-sha1' );
        $referenceNode = $signedInfoNode->appendChild( $securityDOM->createElement( 'Reference' ) );
        $referenceNode->setAttribute( 'URI', '#_0' );
        $referenceNode->appendChild( $securityDOM->createElement( 'Transforms' ) )->appendChild( $securityDOM->createElement( 'Transform' ) )->setAttribute( 'Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#' );
        $referenceNode->appendChild( $securityDOM->createElement( 'DigestMethod' ) )->setAttribute( 'Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1' );
        $referenceNode->appendChild( $securityDOM->createElement( 'DigestValue', base64_encode( sha1( $headerTimestamp->C14N( true ), true ) ) ) );
        $signatureNode->appendChild( $securityDOM->createElement( 'SignatureValue', base64_encode( hash_hmac( 'sha1', $signedInfoNode->C14N( true ), base64_decode( $token->binarySecret ), true ) ) ) );
        $keyInfoNode                = $signatureNode->appendChild( $securityDOM->createElement( 'KeyInfo' ) );
        $securityTokenReferenceNode = $keyInfoNode->appendChild( $securityDOM->createElement( 'o:SecurityTokenReference' ) );
        $securityTokenReferenceNode->setAttributeNS( 'http://docs.oasis-open.org/wss/oasis-wss-wssecurity-secext-1.1.xsd', 'k:TokenType', 'http://docs.oasis-open.org/wss/oasis-wss-saml-token-profile-1.1#SAMLV1.1' );
        $securityTokenReferenceNode->appendChild( $securityDOM->createElement( 'o:KeyIdentifier', $token->keyIdentifier ) )->setAttribute( 'ValueType', 'http://docs.oasis-open.org/wss/oasis-wss-saml-token-profile-1.0#SAMLAssertionID' );

        return $securityHeader;
    }

}
