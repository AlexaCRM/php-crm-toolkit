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

use AlexaCRM\CRMToolkit\Settings;
use AlexaCRM\CRMToolkit\Client;

/**
 * AlexaCRM\CRMToolkit\Auth\AlexaSDK_Authentication.class.php
 *
 * @author alexacrm.com
 * @version 1.0
 * @package AlexaCRM\CRMToolkit\AlexaSDK\Authentication
 * @subpackage Authentication
 */
class Authentication extends Client {

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
	 *  Token that used to construct SOAP requests
	 *
	 * @var array
	 */
	protected $organizationSecurityToken = null;

	/**
	 *  Token that used to construct SOAP requests
	 *
	 * @var array
	 */
	protected $discoverySecurityToken = null;

	public function setCachedSecurityToken( $service, $token ) {
		if ( $this->client->isCacheEnabled() ) {
			$cacheKey = $this->getSecurityTokenCacheKey( $service );
			$this->client->cache->set( $cacheKey, $token, floor( $token['expiryTime'] - time() ) );
		}
	}

	public function getCachedSecurityToken( $service, &$securityToken ) {
		if ( $this->client->isCacheEnabled() ) {
			$cacheKey = $this->getSecurityTokenCacheKey( $service );
			return $this->client->cache->exists( $cacheKey );
		}

		return false;
	}

	/**
	 * Generates a security token cache key for given service (discovery, organization)
	 *
	 * @param string $service
	 *
	 * @return string
	 */
	protected function getSecurityTokenCacheKey( $service ) {
		return strtolower( $service . '_security_token' );
	}

}
