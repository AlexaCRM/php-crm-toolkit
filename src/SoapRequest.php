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
use DOMNode;

/**
 * AlexaCRM\CRMToolkit\AlexaSDK_SoapRequest.php
 *
 * @author alexacrm.com
 * @version 1.0
 * @package AlexaSDK
 */
class SoapRequest {

	protected $authentication;

	public function create( Entity &$entity ) {
		/* Send the security request and get a security token */
		$securityToken = $this->authentication->getOrganizationSecurityToken();
		/* Generate the XML for the Body of a Create request */
		$createNode = SoapRequestsGenerator::generateCreateRequest( $entity );

		SoapRequestsGenerator::generateCreateRequest( $entity );
	}


	/**
	 * Create the XML String for a Soap Request
	 *
	 * @ignore
	 */
	protected function generateSoapRequest( $serviceURI, $soapAction, $securityToken, DOMNode $bodyContentNode ) {
		$soapRequestDOM = new DOMDocument();
		$soapEnvelope   = $soapRequestDOM->appendChild( $soapRequestDOM->createElementNS( 'http://www.w3.org/2003/05/soap-envelope', 's:Envelope' ) );
		$soapEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://www.w3.org/2005/08/addressing' );
		$soapEnvelope->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:u', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd' );
		/* Get the SOAP Header */
		$soapHeaderNode = $this->generateSoapHeader( $serviceURI, $soapAction, $securityToken );
		$soapEnvelope->appendChild( $soapRequestDOM->importNode( $soapHeaderNode, true ) );
		/* Create the SOAP Body */
		$soapBodyNode = $soapEnvelope->appendChild( $soapRequestDOM->createElement( 's:Body' ) );
		$soapBodyNode->appendChild( $soapRequestDOM->importNode( $bodyContentNode, true ) );

		return $soapRequestDOM->saveXML( $soapEnvelope );
	}


	public function request( $requestType ) {
		switch ( strtolower( $requestType ) ) {
			case "create":
				SoapRequestsGenerator::generateCreateRequest( $entity );
				break;
			case "update":
				break;
			case "delete":
				break;
			case "retrievemetadatachanges":
				break;
			case "retrieve":
				break;
			case "retrieveorganization":
				break;
			case "retrievemultiple":
				break;
			case "retrieveentity":
				break;
			case "executeaction":
				break;
		}
	}
}

