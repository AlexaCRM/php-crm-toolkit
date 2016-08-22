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

/**
 * AlexaCRM\CRMToolkit\Auth\AlexaSDK_Oauth2.class.php
 *
 * @author alexacrm.com
 * @version 1.0
 * @package AlexaCRM\CRMToolkit\AlexaSDK\Authentication
 * @subpackage Authentication
 */
namespace AlexaCRM\CRMToolkit\Auth;

use AlexaCRM\CRMToolkit\Settings;
use AlexaCRM\CRMToolkit\Rest;

/**
 * This class used to authenticate to Microsoft Dynamics CRM Online
 */
class OAuth2 extends Rest {

    /**
     * Global SDK settings
     *
     * @var Settings Instance of AlexaCRM\CRMToolkit\Settings class
     */
    public $settings;

    protected $clientId; /* also called or $tenantId */

    private $clientSecret;

    private $authorizationEndpoint;

    private $tokenEndpoint;

    private $securityToken;

    private $grantType = "authorization_code";

    private $responseType = "code";

    public $redirectUrl = "";

    private $multiTenant = false;

    private $resource;

    public function __construct( Settings $_settings, $resouce ) {
        $this->settings     = $_settings;
        $this->clientId     = $this->settings->oauthClientId;
        $this->clientSecret = $this->settings->oauthClientSecret;
        $this->multiTenant  = $this->settings->oauthMultiTenant;

        if ( $this->multiTenant ) {
            $this->authorizationEndpoint = "https://login.microsoftonline.com/common/oauth2/authorize";
            $this->tokenEndpoint         = "https://login.microsoftonline.com/common/oauth2/token";
        } else {
            $this->authorizationEndpoint = $this->settings->oauthAuthorizationEndpoint;
            $this->tokenEndpoint         = $this->settings->oauthTokenEndpoint;
        }

        if ( !$this->redirectUrl ) {
            $this->redirectUrl = strtok( "http" . ( ( $_SERVER['SERVER_PORT'] == 443 ) ? "s://" : "://" ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], "?" );
        }

        $this->resource = $resouce;
        //$this->securityToken = $this->getTokenCookie();
    }

//		private function getTokenCookie(){
//			return (isset($_COOKIE["AADOAUTH2"])) ? json_decode($_COOKIE["AADOAUTH2"]) : null;
//		}
//
//		private function setTokenCookie($token){
//			return setcookie('AADOAUTH2', json_encode($token), (time() + 2 * 86400), '/');
//		}

    public function getSecurityToken() {
        /* Check if there is an existing token */
        if ( $this->securityToken != null ) {
            /* Check if the Security Token is still valid */
            if ( $this->securityToken->expires_on > time() ) {
                /* Use the existing token */
                return $this->securityToken;
            } else {
                $this->securityToken = $this->requestRefreshToken();
                //$this->setTokenCookie($this->securityToken);
                /* Use refreshed token */

                return $this->securityToken;
            }
        } else {
            /* Check if Security Token cached  */
            //$isDefined = $this->auth->getCachedSecurityToken("organization", $this->organizationSecurityToken);
            /* Check if the Security Token is still valid */
            //if ($isDefined && $this->organizationSecurityToken['expiryTime'] > time()) {
            /* Use cached token */
            //	return $this->organizationSecurityToken;
            //}
        }

        if ( isset( $_GET["code"] ) ) {

            $_GET["state"];
            $_GET["session_state"];

            $this->securityToken = $this->requestAccessToken( $_GET["code"] );

            //$this->setTokenCookie($this->securityToken);
            return $this->securityToken;
        } else if ( isset( $_GET["error"] ) ) {
        } else if ( empty( $_GET ) ) {

            $this->redirectToAuthorization();
        }
    }

    public function redirectToAuthorization() {

        $args = array(
            'response_type' => $this->responseType,
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'resource'      => $this->resource,
            'client_secret' => $this->clientSecret,
            'state'         => self::getUuid(),
        );

        $content = http_build_query( $args );

        $url = $this->authorizationEndpoint . "?" . $content;

        ?>
        <script>
            window.location.href = "<?php echo $url; ?>";
        </script>
        <?php
    }

    public function requestAccessToken( $code, $sessionState = null ) {
        $args = array(
            'grant_type'    => $this->grantType,
            'code'          => $code,
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'resource'      => $this->resource,
            'client_secret' => $this->clientSecret,
        );

        $content = http_build_query( $args );

        $url = str_replace( "?api-version=1.0", "", $this->tokenEndpoint );

        return self::getRestResponse( $url, $content );
    }

    public function requestRefreshToken() {
        $args    = array(
            'grant_type'    => 'refresh_token',
            'refresh_token' => $this->securityToken["refresh_token"],
            'redirect_uri'  => $this->redirectUrl,
            'resource'      => $this->resource,
            'client_secret' => $this->clientSecret,
            'client_id'     => $this->clientId,
        );
        $content = http_build_query( $args );

        $url = str_replace( "?api-version=1.0", "", $this->tokenEndpoint );

        return self::getRestResponse( $url, $content );
    }

}

