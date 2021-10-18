<?php


namespace AlexaCRM\CRMToolkit;

/**
 * SDK configuration class, used to create instance of AlexaCRM\CRMToolkit\Client class
 */
class OnlineS2SSecretAuthenticationSettings extends AbstractSettings {

	/**
	 * Const definition for settings type
	 */
	const SETTINGS_TYPE = 'sharedSecretAuth';

	/**
	 * Web API version.
	 */
	public $apiVersion = '9.0';

	/**
	 * Identifier of the Azure Active Directory application registration
	 *
	 * @var string
	 */
	public $applicationId;

	/**
	 * A key associated with the Azure Active Directory $applicationId
	 *
	 * @var string
	 */
	public $clientSecret;

	/**
	 * Azure AD tenant ID.
	 *
	 * Optional, allows skipping tenant detection.
	 */
	public $tenantID = null;

	/**
	 * Cache object for the settings
	 * @var mixed|null
	 */
	public $cache = null;

	/**
	 * OnlineS2SSecretAuthenticationSettings constructor.
	 *
	 * @param $settings
	 */
	public function __construct( $settings ) {
		$this->applicationId = $settings['applicationId'];
		$this->clientSecret  = $settings['clientSecret'];
		if ( ! empty( $settings['cache'] ) ) {
			$this->cache = $settings['cache'];
		}

		parent::__construct( $settings );
	}

	/**
	 * Check if all required settings are filled
	 *
	 * @return bool
	 */
	public function isFullSettings() {
		return ( $this->discoveryUrl && $this->applicationId && $this->clientSecret && $this->organizationUrl
		         && $this->loginUrl && ( ( $this->authMode === 'OnlineFederation' ) ? $this->crmRegion : true ) );
	}

	/**
	 * Returns Web API endpoint URI.
	 */
	public function getEndpointURI() {
		return trim( $this->serverUrl, '/' ) . '/api/data/v' . $this->apiVersion . '/';
	}


	/**
	 * Validates settings input.
	 *
	 * @param array $settings
	 */
	protected function validateInput( $settings ) {
		if ( ! isset( $settings["serverUrl"] ) ||
		     ! isset( $settings["applicationId"] ) ||
		     ! isset( $settings["clientSecret"] )
		) {
			throw new \InvalidArgumentException( 'applicationId, clientSecret or serverUrl is incorrect' );
		}

		if ( ! filter_var( $settings["serverUrl"], FILTER_VALIDATE_URL )
		     || strpos( $settings["serverUrl"], "." ) === false
		) {
			throw new \InvalidArgumentException( 'Invalid serverUrl has been provided' );
		}

		if ( $settings['authMode'] !== 'OnlineFederation' ) {
			throw new \InvalidArgumentException( 'Provided authentication mode <' . $this->authMode . '> is not supported' );
		}
	}

	/**
	 * Validates URL parsing results.
	 *
	 * @param array $urlParts
	 */
	protected function validateUrl( $urlParts ) {
		if ( ! is_array( $urlParts ) ) {
			throw new \InvalidArgumentException( 'Invalid serverUrl has been provided' );
		}

		if ( ! isset( $urlParts["scheme"] ) ) {
			throw new \InvalidArgumentException( 'serverUrl has been provided without a valid scheme (http:// or https://)' );
		}

		if ( ! isset( $urlParts["host"] ) ) {
			throw new \InvalidArgumentException( 'Invalid serverUrl has been provided' );
		}
	}
}
