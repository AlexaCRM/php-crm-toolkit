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

/**
 * SDK configuration class, used to create instance of AlexaCRM\CRMToolkit\Client class
 */
class Settings extends AbstractSettings {

    /**
     * Username to login to Dynamics CRM
     *
     * @var string
     */
    public $username;

    /**
     * Password to login to Dynamics CRM
     *
     * @var string
     */
    public $password;


	public function __construct( $settings ) {
		$this->username   = $settings['username'];
		$this->password   = $settings['password'];

		parent::__construct( $settings );
	}

    /**
     * Check if all required settings are filled
     *
     * @return bool
     */
    public function isFullSettings() {
        return ( $this->discoveryUrl && $this->username && $this->password && $this->organizationUrl
                 && $this->loginUrl && ( ( $this->authMode === 'OnlineFederation' ) ? $this->crmRegion : true ) );
    }

	/**
	 * Validates settings input.
	 *
	 * @param array $settings
	 */
	protected function validateInput( $settings ) {
		if ( ! isset( $settings["serverUrl"] ) ||
		     ! isset( $settings["username"] ) ||
		     ! isset( $settings["password"] )
		) {
			throw new \InvalidArgumentException( 'Username, password or serverUrl is incorrect' );
		}

        if ( !filter_var( $settings["serverUrl"], FILTER_VALIDATE_URL )
             || strpos( $settings["serverUrl"], "." ) === false
        ) {
            throw new \InvalidArgumentException( 'Invalid serverUrl has been provided' );
        }

        if ( !in_array( $settings['authMode'], [ 'OnlineFederation', 'Federation' ] ) ) {
            throw new \InvalidArgumentException( 'Provided authentication mode <' . $this->authMode . '> is not supported' );
        }
    }

    /**
     * Validates URL parsing results.
     *
     * @param array $urlParts
     */
    protected function validateUrl( $urlParts ) {
        if ( !is_array( $urlParts ) ) {
            throw new \InvalidArgumentException( 'Invalid serverUrl has been provided' );
        }

        if ( !isset( $urlParts["scheme"] ) ) {
            throw new \InvalidArgumentException( 'serverUrl has been provided without a valid scheme (http:// or https://)' );
        }

        if ( !isset( $urlParts["host"] ) ) {
            throw new \InvalidArgumentException( 'Invalid serverUrl has been provided' );
        }
    }

}
