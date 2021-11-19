<?php


namespace AlexaCRM\CRMToolkit\Auth;

use DOMDocument;
use AlexaCRM\CRMToolkit\AbstractSettings;
use AlexaCRM\CRMToolkit\Client;

class OnlineS2SAuth {

	/**
	 * Service settings.
	 */
	protected $settings;

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * Bearer token.
	 */
	public $token = null;

	protected $httpClient = null;

	/**
	 * Create a new instance of the AlexaCRM\CRMToolkit\AlexaSDK
	 *
	 * @param AbstractSettings $settings
	 * @param Client $client
	 *
	 * @throws \Exception Thrown if TLS 1.2 is not supported by the environment.
	 */
	public function __construct( $settings, $client ) {
		$this->settings = $settings;
		$this->client = $client;
	}

	/**
	 * Acquires the Bearer token via client credentials OAuth2 flow.
	 */
	public function acquireToken() {
		$settings = $this->settings;

		$cacheKey = $this->getTokenCacheKey();
		$cachedToken = $this->settings->cache->get( $cacheKey );
		if ( ! empty( $cachedToken ) ) {
			$cachedToken = unserialize( $cachedToken );
            $isExpired = $cachedToken->expiresOn < time();

			if ( ! empty( $cachedToken ) && isset( $cachedToken->token ) && ! empty( $cachedToken->token ) && !$isExpired ) {
				$this->token = $cachedToken;
				return $cachedToken;
			}
		}


		$tenantId = $this->detectTenantID( $settings->getEndpointURI() );
		$tokenEndpoint = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token';

		try {
			$requestFields = [
				'grant_type'    => 'client_credentials',
				'client_id'     => $settings->applicationId,
				'client_secret' => $settings->clientSecret,
				'resource'      => $settings->serverUrl,
			];

			$cURLHandle = curl_init();
			curl_setopt( $cURLHandle, CURLOPT_URL, $tokenEndpoint );
			curl_setopt( $cURLHandle, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $cURLHandle, CURLOPT_TIMEOUT, 300 );


			curl_setopt( $cURLHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
			curl_setopt( $cURLHandle, CURLOPT_POST, 1 );
			curl_setopt( $cURLHandle, CURLOPT_POSTFIELDS, $requestFields );
			curl_setopt( $cURLHandle, CURLOPT_FOLLOWLOCATION, true ); // follow redirects
			curl_setopt( $cURLHandle, CURLOPT_POSTREDIR, 7 ); // 1 | 2 | 4 (301, 302, 303 redirects mask)
			curl_setopt( $cURLHandle, CURLOPT_HEADER, false );

			/* Execute the cURL request, get the XML response */
			$responseBody = curl_exec( $cURLHandle );

			$this->client->logger->debug( 'Retrieved a new access token via ' . $tokenEndpoint );


			/* Check for HTTP errors */
			$curlError = curl_error( $cURLHandle );
			$curlErrNo = curl_errno( $cURLHandle );
			curl_close( $cURLHandle );

		} catch ( RequestException $e ) {
			throw new \Exception( 'Authentication at Azure AD failed. ' . 'cURL Error: ' . $curlErrNo . ', ' . $curlError );
		}

		$this->token = Token::createFromJson( $responseBody );
		$this->settings->cache->set( $cacheKey, serialize($this->token), $this->token->expiresOn );

		return $this->token;
	}

	/**
	 * Detects the instance tenant ID by probing the API without authorization.
	 *
	 * @param string $endpointUri
	 *
	 * @return string Tenant ID of the queried instance.
	 */
	protected function detectTenantID( $endpointUri ) {
		if ( isset( $this->settings->tenantID ) ) {
			return $this->settings->tenantID;
		}

		$cacheKey = 'msdynwebapi.tenant.' . sha1( $endpointUri );
		$cachedTenantID = $this->settings->cache->get( $cacheKey );

		if(!empty($cachedTenantID)) {
			return $cachedTenantID;
		}

		try {
			$curl = curl_init($endpointUri);
			curl_setopt($curl, CURLOPT_URL, $endpointUri);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_NOBODY, 1);

			$resp = curl_exec($curl);
			curl_close($curl);

			$headers = [];
			$output = rtrim($resp);
			$data = explode("\n",$output);
			$headers['status'] = $data[0];
			array_shift($data);

			foreach($data as $part){
				$middle = explode(":",$part,2);
				if ( !isset($middle[1]) ) { $middle[1] = null; }
				$headers[ strtolower(trim($middle[0])) ] = trim($middle[1]);
			}
		} catch ( HttpClientException $e ) {
			// Always returns 401 Unauthorized, but we only need 'WWW-Authenticate' header to use it below
		}

		preg_match( '~/([a-f0-9]{8}-([a-f0-9]{4}-){3}[a-f0-9]{12})/~', $headers['www-authenticate'], $tenantMatch );
		$tenantID = $tenantMatch[1];

		$this->client->logger->debug( "Probed {$endpointUri} for tenant ID {{$tenantID}}" );

		$expirationDuration = 86400 * 365; // Cache the tenant ID for 1 year.
		$this->settings->cache->set( $cacheKey, $tenantID, $expirationDuration );

		return $tenantID;
	}

	/**
	 * Generates a Security section for SOAP envelope header.
	 *
	 * @return \DOMNode
	 */
	public function generateTokenHeader( ) {
		return null;
	}

	/**
	 * Invalidates the token for a given service and refresh it
	 *
	 * @param string $service
	 *
	 * @return void
	 */
	public function invalidateToken( $service ) {
		unset( $this->token );

		$cacheKey = $this->getTokenCacheKey();
		$this->settings->cache->delete( $cacheKey );

		$this->client->logger->notice( 'Invalidated token for ' . ucfirst( $service ) . 'Service' );
		$this->acquireToken();
	}

	/**
	 * Generates a security token cache key
	 *
	 * @return string
	 */
	protected function getTokenCacheKey() {
		return 'msdynwebapi.token.' . sha1( $this->settings->serverUrl . $this->settings->applicationId . $this->settings->clientSecret );
	}

}
