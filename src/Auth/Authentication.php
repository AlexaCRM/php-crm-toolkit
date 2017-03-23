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

use AlexaCRM\CRMToolkit\SecurityToken;
use AlexaCRM\CRMToolkit\Settings;
use AlexaCRM\CRMToolkit\Client;

/**
 * Handles authentication within Dynamics CRM web services.
 */
abstract class Authentication {

    /**
     * Global SDK settings
     *
     * @var Settings Instance of AlexaCRM\CRMToolkit\Settings class
     */
    public $settings;

    /**
     * Object of AlexaCRM\CRMToolkit\Client class
     *
     * @var Client
     */
    protected $client;

    /**
     * Stores security tokens for Organization and Discovery services.
     *
     * @var SecurityToken[]
     */
    protected $tokens = [];

    /**
     * Create a new instance of the AlexaCRM\CRMToolkit\AlexaSDK
     *
     * @param Settings $settings
     * @param Client $client
     */
    public function __construct( $settings, $client ) {
        $this->settings = $settings;
        $this->client   = $client;
    }

    /**
     * @param string $service
     *
     * @return string
     */
    protected abstract function generateTokenRequest( $service );

    /**
     * Generates a Security section for SOAP envelope header.
     *
     * @param string $service
     *
     * @return \DOMNode
     */
    public abstract function generateTokenHeader( $service );

    /**
     * Retrieves a security token for the specified service.
     *
     * @param string $service   Can be 'organization', 'discovery'
     *
     * @return SecurityToken
     */
    public function getToken( $service ) {
        if ( $this->isTokenLoaded( $service ) && !$this->isTokenExpired( $service ) ) {
            return $this->tokens[$service];
        }

        $tokenCacheKey = $this->getTokenCacheKey( $service );

        $cachedToken = $this->client->cache->get( $tokenCacheKey );
        if ( $cachedToken instanceof SecurityToken && !$cachedToken->hasExpired() ) {
            $this->tokens[$service] = $cachedToken; // save in memory

            return $cachedToken;
        }

        $this->tokens[$service] = $newToken = $this->retrieveToken( $service );
        $this->client->cache->set( $tokenCacheKey, $newToken, floor( $newToken->expiryTime - time() - 60 ) );

        return $newToken;
    }

    /**
     * Retrieves the security token from the STS.
     *
     * @param string $service
     *
     * @return SecurityToken
     */
    protected function retrieveToken( $service ) {
        $tokenResponse = $this->client->attemptSoapResponse( 'sts', function() use ( $service ) {
            return $this->generateTokenRequest( $service );
        } );

        $securityDOM = new \DOMDocument();
        $securityDOM->loadXML( $tokenResponse );

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

        $this->client->logger->info( 'Issued a new ' . $service . ' security token - expires at: ' . date( 'r', $newToken->expiryTime ) );

        return $newToken;
    }

    /**
     * Tells whether the given service token has expired.
     *
     * @param string $service
     *
     * @return bool
     */
    protected function isTokenExpired( $service ) {
        if ( !$this->isTokenLoaded( $service ) ) {
            $this->tokens[$service] = $this->getToken( $service );
        }

        return $this->tokens[$service]->hasExpired();
    }

    /**
     * Checks whether security token for given service is loaded into memory.
     *
     * @param string $service
     *
     * @return bool
     */
    protected function isTokenLoaded( $service ) {
        return ( array_key_exists( $service, $this->tokens ) && is_array( $this->tokens[$service] ) );
    }

    /**
     * Generates a security token cache key for given service (discovery, organization)
     *
     * @param string $service
     *
     * @return string
     */
    protected function getTokenCacheKey( $service ) {
        return strtolower( $service . '_security_token' );
    }

    /**
     * Invalidates the token for a given service.
     *
     * @param string $service
     *
     * @return void
     */
    public function invalidateToken( $service ) {
        unset( $this->tokens[$service] );

        $cacheKey = $this->getTokenCacheKey( $service );
        $this->client->cache->delete( $cacheKey );

        $this->client->logger->notice( 'Invalidated token for ' . ucfirst( $service ) . 'Service' );
    }

    /**
     * Returns server, endpoint, username, password.
     *
     * @return array
     */
    protected function getTokenCredentials() {
        return [
            'server' => $this->settings->getServiceEndpoint( 'sts' ),
            'endpoint' => $this->settings->getAuthenticationEndpoint(),
            'username' => $this->settings->username,
            'password' => $this->settings->password,
        ];
    }

}
