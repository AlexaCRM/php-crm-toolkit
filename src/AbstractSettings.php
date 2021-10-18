<?php


namespace AlexaCRM\CRMToolkit;

use Psr\Log\LoggerInterface;

/**
 * Abstract settings class.
 * Implements common methods and attributes
 * @package AlexaCRM\CRMToolkit
 */
abstract class AbstractSettings {
	/**
	 * Type of online authentication. Can be 'defaultAuth' or 'secretShared'
	 * Used only when $authMode === 'OnlineFederation'
	 *
	 * @var string
	 */
	public $authMethod;

	/**
	 * Deployment type Internet-facing deployment(On-premises) or CRM Online(Office 365).
	 * Can be 'OnlineFederation' for CRM Online, or 'Federation' for Internet-Facing Deployment
	 *
	 * @var string
	 */
	public $authMode;

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
	 * Determines whether to use the Discovery Service.
	 *
	 * @var bool
	 */
	public $useDiscovery;

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
	 * Path to the CA bundle.
	 *
	 * @var string
	 */
	public $caPath;

	/**
	 * @var bool
	 */
	public $ignoreSslErrors = false;

	/**
	 * Whether to use the literal value of STSAuthURL in GetUserRealm response.
	 *
	 * The toolkit only supports one ADFS endpoint, /adfs/services/trust/13/usernamemixed.
	 * In non-standard environments, STSAuthURL may point to a different endpoint.
	 * The default behavior is to always point to the UsernameMixed endpoint.
	 * This setting allows to override such behavior and instead use the exact presented value.
	 *
	 * @var bool
	 */
	public $strictFederatedSTS = false;

	/**
	 * @var CacheInterface
	 */
	public $cacher;

	/**
	 * @var LoggerInterface
	 */
	public $logger;

	/**
	 * @var mixed
	 */
	public $cache = array( "server" => "localhost", "port" => 11211 );

	/**
	 * Proxy with port.
	 *
	 * @var string
	 */
	public $proxy;
	/**
	 * List of CRM regions
	 *
	 * @var array
	 */
	protected static $crmRegionMapping = [
		'crm'   => 'crmna:dynamics.com',
		'crm2'  => 'crmsam:dynamics.com',
		'crm3'  => 'crmcan:dynamics.com',
		'crm4'  => 'crmemea:dynamics.com',
		'crm5'  => 'crmapac:dynamics.com',
		'crm6'  => 'crmoce:dynamics.com',
		'crm7'  => 'crmjpn:dynamics.com',
		'crm8'  => 'crmind:dynamics.com',
		'crm9'  => 'crmgcc:dynamics.com',
		'crm11' => 'crmgbr:dynamics.com',
		'crm12' => 'crmfra:dynamics.com',
	];

	public function __construct( $settings ) {
		try {
			$this->validateInput( $settings );
		} catch ( \InvalidArgumentException $e ) {
			throw $e;
		}

		$this->serverUrl  = $settings['serverUrl'];
		$this->authMode   = $settings['authMode'];
		if ( isset( $settings['authMethod'] ) ) {
			$this->authMethod = $settings['authMethod'];
		}

		$serverUrlParts = parse_url( $this->serverUrl );

		try {
			$this->validateUrl( $serverUrlParts );
		} catch ( \InvalidArgumentException $e ) {
			throw $e;
		}

		$this->useSsl = ( $serverUrlParts['scheme'] === 'https' );
		$this->port   = isset ( $serverUrlParts['port'] ) ? $serverUrlParts['port'] : '';

		if ( $this->useSsl && isset( $settings['ignoreSslErrors'] ) ) {
			$this->ignoreSslErrors = (bool) $settings['ignoreSslErrors'];
		}

		$serverHostParts = explode( '.', $serverUrlParts['host'] );

		$organizationName = $serverHostParts[0];

		$this->organizationName       = ( isset( $settings["organizationName"] ) ) ? $settings["organizationName"] : null;
		$this->organizationUniqueName = ( isset( $settings["organizationUniqueName"] ) ) ? $settings["organizationUniqueName"] : null;
		$this->organizationId         = ( isset( $settings["organizationId"] ) ) ? $settings["organizationId"] : null;
		$this->organizationVersion    = ( isset( $settings["organizationVersion"] ) ) ? $settings["organizationVersion"] : null;
		$this->proxy                  = ( isset( $settings["proxy"] ) ) ? $settings["proxy"] : null;

		if ( $this->authMode === 'OnlineFederation' ) {
			$crmRegionId     = $serverHostParts[1];
			$this->crmRegion = $this->getCrmRegion( $crmRegionId );

			$this->discoveryUrl    = sprintf( '%s://disco.%s.dynamics.com/XRMServices/2011/Discovery.svc', $serverUrlParts['scheme'], $crmRegionId );
			$this->organizationUrl = sprintf( '%s://%s.api.%s.dynamics.com/XRMServices/2011/Organization.svc', $serverUrlParts['scheme'], $organizationName, $crmRegionId );

			$this->loginUrl = 'https://login.microsoftonline.com/RST2.srf';

			if ( isset( $settings['strictFederatedSTS'] ) ) {
				$this->strictFederatedSTS = $settings['strictFederatedSTS'];
			}

			$this->useDiscovery = false;
		} elseif ( $this->authMode === 'Federation' ) {
			$this->crmRegion = null; // not applicable

			$urlPort = ( $this->port !== '' ) ? ':' . $this->port : '';

			$this->discoveryUrl    = sprintf( '%s://%s%s/XRMServices/2011/Discovery.svc', $serverUrlParts['scheme'], $serverUrlParts['host'], $urlPort );
			$this->organizationUrl = sprintf( '%s://%s%s/XRMServices/2011/Organization.svc', $serverUrlParts['scheme'], $serverUrlParts['host'], $urlPort );
			// loginUrl is set upon Client instantiation

			$this->useDiscovery = isset( $settings['useDiscovery'] ) ? $settings['useDiscovery'] : true;
		}

		// Set the custom CA bundle path.
		if ( isset( $settings['caPath'] ) && is_readable( $settings['caPath'] ) ) {
			$this->caPath = $settings['caPath'];
		}
	}

	/**
	 * Check if all required settings are filled
	 *
	 * @return bool
	 */
	abstract public function isFullSettings();

	/**
	 * Checks whether organization data (name, unique name, ID, version) is stored in Settings
	 *
	 * @return bool
	 */
	public function hasOrganizationData() {
		return ( ! is_null( $this->organizationName ) && ! is_null( $this->organizationUniqueName )
		         && ! is_null( $this->organizationId ) && ! is_null( $this->organizationVersion ) );
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
				return $service;
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
	protected function getCrmRegion( $crmRegionId ) {
		if ( ! array_key_exists( $crmRegionId, static::$crmRegionMapping ) ) {
			throw new \InvalidArgumentException( 'Cannot resolve CRM region: check CRM server url' );
		}

		return static::$crmRegionMapping[ $crmRegionId ];
	}
}
