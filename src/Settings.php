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
class Settings {

    /**
     * Deployment type Internet-facing deployment(On-premises) or CRM Online(Office 365).
     * Can be 'OnlineFederation' for CRM Online, or 'Federation' for Internet-Facing Deployment
     *
     * @var string
     */
    public $authMode;

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

    /**
     * Url where Dynamics CRM is located
     *
     * @var string
     */
    public $serverUrl;

    /**
     * Use SSL flag
     *
     * @var bool
     */
    public $useSsl;

    /**
     * Defines what port will be used for Dynamics CRM Server (Example: 2222)
     *
     * @var int
     */
    public $port;

    /**
     * Unique Name of Dynamics CRM organization
     *
     * @var string
     */
    public $organizationName;

    /**
     * Unique ID of Dynamics CRM organization
     *
     * @var string
     */
    public $organizationId;

    /**
     * Discovery Service Url
     *
     * @var string
     */
    public $discoveryUrl;

    /**
     * Organization Service Url
     *
     * @var string
     */
    public $organizationUrl;

    /**
     * OrganizationData Service Url
     *
     * @var string
     */
    public $organizationDataUrl;

    /**
     * Authorization endpoint that used to accuire token for SOAP requests
     *
     * @var string
     */
    public $loginUrl;

    /**
     * Select the right region for your Dynamics CRM
     * Only for Dynamics CRM Online
     * crmna:dynamics.com - North America
     * crmemea:dynamics.com - Europe, the Middle East and Africa
     * crmapac:dynamics.com - Asia Pacific
     * etc.
     *
     * @var string
     */
    public $crmRegion;

    /**
     * Unique name of organization, can be retrieved by RetrieveOrganizationsRequest
     * Or In Dynamics CRM Settings -> Customizations -> Developer Resources
     *
     * @var string
     */
    public $organizationUniqueName;

    /**
     * Version of Dynamics CRM used for this Organization
     *
     * @var string
     */
    public $organizationVersion;

    /**
     * @var bool
     */
    public $ignoreSslErrors = false;

    /**
     * @var mixed
     */
    public $cache = array( "server" => "localhost", "port" => 11211 );

    /**
     * List of CRM regions
     *
     * @var array
     */
    private static $crmRegionMapping = [
        'crm'  => 'crmna:dynamics.com',
        'crm2' => 'crmsam:dynamics.com',
        'crm3' => 'crmcan:dynamics.com',
        'crm4' => 'crmemea:dynamics.com',
        'crm5' => 'crmapac:dynamics.com',
        'crm6' => 'crmoce:dynamics.com',
        'crm7' => 'crmjpn:dynamics.com',
        'crm8' => 'crmind:dynamics.com',
        'crm9' => 'crmgcc:dynamics.com',
        'crm11' => 'crmgbr:dynamics.com',
    ];

    /**
     * Set up settings using constructor
     *
     * @param array $settings
     *
     * @throws \InvalidArgumentException
     */
    public function __construct( $settings ) {
        try {
            $this->validateInput( $settings );
        } catch ( \InvalidArgumentException $e ) {
            throw $e;
        }

        $this->serverUrl = $settings['serverUrl'];
        $this->username  = $settings['username'];
        $this->password  = $settings['password'];
        $this->authMode  = $settings['authMode'];

        $serverUrlParts = parse_url( $this->serverUrl );

        try {
            $this->validateUrl( $serverUrlParts );
        } catch ( \InvalidArgumentException $e ) {
            throw $e;
        }

        $this->useSsl = ( $serverUrlParts['scheme'] === 'https' );
        $this->port   = isset ( $serverUrlParts['port'] ) ? $serverUrlParts['port'] : '';

        if ( $this->useSsl && isset( $settings['ignoreSslErrors'] ) ) {
            $this->ignoreSslErrors = (bool)$settings['ignoreSslErrors'];
        }

        $serverHostParts = explode( '.', $serverUrlParts['host'] );

        $organizationName = $serverHostParts[0];

        $this->organizationName       = ( isset( $settings["organizationName"] ) ) ? $settings["organizationName"] : null;
        $this->organizationUniqueName = ( isset( $settings["organizationUniqueName"] ) ) ? $settings["organizationUniqueName"] : null;
        $this->organizationId         = ( isset( $settings["organizationId"] ) ) ? $settings["organizationId"] : null;
        $this->organizationVersion    = ( isset( $settings["organizationVersion"] ) ) ? $settings["organizationVersion"] : null;

        if ( $this->authMode === 'OnlineFederation' ) {
            $crmRegionId     = $serverHostParts[1];
            $this->crmRegion = $this->getCrmRegion( $crmRegionId );

            $this->discoveryUrl        = sprintf( '%s://disco.%s.dynamics.com/XRMServices/2011/Discovery.svc', $serverUrlParts['scheme'], $crmRegionId );
            $this->organizationUrl     = sprintf( '%s://%s.api.%s.dynamics.com/XRMServices/2011/Organization.svc', $serverUrlParts['scheme'], $organizationName, $crmRegionId );

            $this->loginUrl = 'https://login.microsoftonline.com/RST2.srf';
        } elseif ( $this->authMode === 'Federation' ) {
            $this->crmRegion = null; // not applicable

            $urlPort = ( $this->port !== '' ) ? ':' . $this->port : '';

            $this->discoveryUrl        = sprintf( '%s://%s%s/XRMServices/2011/Discovery.svc', $serverUrlParts['scheme'], $serverUrlParts['host'], $urlPort );
            $this->organizationUrl     = sprintf( '%s://%s%s/XRMServices/2011/Organization.svc', $serverUrlParts['scheme'], $serverUrlParts['host'], $urlPort );
            // loginUrl is set upon Client instantiation
        }
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
     * Checks whether organization data (name, unique name, ID, version) is stored in Settings
     *
     * @return bool
     */
    public function hasOrganizationData() {
        return ( !is_null( $this->organizationName ) && !is_null( $this->organizationUniqueName )
                 && !is_null( $this->organizationId ) && !is_null( $this->organizationVersion ) );
    }

    /**
     * Retrieves endpoint URI for the given service.
     *
     * @param string $service
     *
     * @return string
     */
    public function getServiceEndpoint( $service ) {
        switch ( $service ) {
            case 'organization':
                return $this->organizationUrl;
            case 'discovery':
                return $this->discoveryUrl;
            case 'sts':
            case 'login':
                return $this->loginUrl;
            default:
                throw new \InvalidArgumentException( 'Service <' . $service . '> is not supported' );
        }
    }

    /**
     * Retrieves authentication endpoint for the STS.
     *
     * @return string
     */
    public function getAuthenticationEndpoint() {
        switch ( $this->authMode ) {
            case 'OnlineFederation':
                return $this->crmRegion;
            case 'Federation':
                return $this->organizationUrl;
            default:
                throw new \InvalidArgumentException( 'Unsupported authentication mode: ' . $this->authMode );
        }
    }

    /**
     * @param $crmRegionId
     *
     * @return string
     */
    private function getCrmRegion( $crmRegionId ) {
        if ( !array_key_exists( $crmRegionId, static::$crmRegionMapping ) ) {
            throw new \InvalidArgumentException( 'Cannot resolve CRM region: check CRM server url' );
        }

        return static::$crmRegionMapping[ $crmRegionId ];
    }

    /**
     * Validates settings input.
     *
     * @param array $settings
     */
    private function validateInput( $settings ) {
        if ( !isset( $settings["serverUrl"] ) ||
             !isset( $settings["username"] ) ||
             !isset( $settings["password"] )
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
    private function validateUrl( $urlParts ) {
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
