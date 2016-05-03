<?php
/**
 * AlexaSDK_Settings.php
 * 
 * @author alexacrm.com.au
 * @version 1.0
 * @package AlexaSDK
 */

/**
 * SDK configuration class, used to create instance of AlexaSDK class
 */
class AlexaSDK_Settings{
    
        /**
         * Deployment type Internet-facing deployment(On-premises) or CRM Online(Office 365)
         * 
         * @var String $authMode can be 'OnlineFederation' for CRM Online, or 'Federation' for Internet-Facing Deployment
         */
        public $authMode;
        
        /**
         * Username to login to Dynamics CRM
         * 
         * @var string $username the Username to login with
         */
        public $username;
        
        /**
         * Password to login to Dynamics CRM
         * 
         * @var string $password the Password to login with
         */
        public $password;
        
        /**
         * Url where Dynamics CRM is located
         * 
         * @var string $serverUrl the Url of Dynamics CRM
         */
        public $serverUrl;
        
        /**
		 * Use SSL flag
		 * 
		 * @var Boolean $useSsl
		 */
        public $useSsl;
		
		/**
		 * Defines what port will be used for Dynamics CRM Server (Example: 2222) 
		 * 
		 * @var Integer $port
		 */
        public $port;
        
        /**
         * Unique Name of Dynamics CRM organization
         * 
         * @var String $organizationName
         */
        public $organizationName;
        
        /**
         * Unique ID of Dynamics CRM organization
         * 
         * @var String $organizationId
         */
        public $organizationId;
        
        /**
         * Discovery Service Url
         * 
         * @var string $discoveryUrl
         */
        public $discoveryUrl;
        
        /**
         * Organization Service Url
         * 
         * @var string $organizationUrl
         */
        public $organizationUrl;
        
        /**
         * OrganizationData Service Url
         * 
         * @var string $organizationDataUrl
         */
        public $organizationDataUrl;
        
        /**
         * Authorization endpoint that used to accuire token for SOAP requests
         * 
         * @var String $loginUrl
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
         * @var String $crmRegion
         */
        public $crmRegion;
        
		/**
		 * Unique name of organization, can be retrieved by RetrieveOrganizationsRequest
		 * Or In Dynamics CRM Settings -> Customizations -> Developer Reources
		 * 
		 * @var String $organizationUniqueName
		 */
        public $organizationUniqueName;
        
		/**
		 * Version of Dynamics CRM used for this Organization
		 * 
		 * @var String $organizationVersion
		 */
        public $organizationVersion;
		
		/**
		 * 
		 * 
		 * @var string 
		 */
		public $oauthResource;
		
		/**
		 *
		 * @var type 
		 */
		public $oauthClientId;
		
		/**
		 *
		 * @var type 
		 */
		public $oauthClientSecret;
		
		/**
		 *
		 * @var type 
		 */
		public $oauthGrantType;
		
		/**
		 *
		 * @var type 
		 */
		public $oauthApiVersion;
        
		/**
		 *
		 * @var type 
		 */
		public $oauthAuthorizationEndpoint;
		
		/**
		 *
		 * @var type 
		 */
		public $oauthTokenEndpoint;
		
		/**
		 *
		 * @var type 
		 */
		public $oauthMultiTenant = false;
        
		/**
		 *
		 * @var type 
		 */
        public $cache = array("server" => "localhost", "port" => 11211);
		
        /**
         * Set up settings using constructor
         * 
         * @param string $discoveryUrl Discovery Service Url
         * @param string $username Username to login to Dynamics CRM
         * @param string $password Password to login to Dynamics CRM
         * @param string $organizationUrl Organization Service Url
         * @param string $loginUrl Authorization endpoint that used to authorize and accuire token for SOAP requests
         * @param string $serverUrl Url where Dynamics CRM is located
         * @param string $authMode can be 'OnlineFederation' for Dynamics CRM Online, or 'Federation' for Internet-Facing Deployment
         * @param string $region the region for your Dynamics CRM Online(crmna:dynamics.com, crmemea:dynamics.com, crmapac:dynamics.com)
         * 
         * @return void
         */
        function __construct($_settings){
            
				$this->discoveryUrl = (isset($_settings["discoveryUrl"])) ? $_settings["discoveryUrl"] : NULL;
				$this->username = (isset($_settings["username"])) ? $_settings["username"] : NULL;
				$this->password = (isset($_settings["password"])) ? $_settings["password"] : NULL;
				$this->organizationUrl = (isset($_settings["organizationUrl"])) ? $_settings["organizationUrl"] : NULL;
				$this->loginUrl = (isset($_settings["loginUrl"])) ? $_settings["loginUrl"] : NULL;
				$this->serverUrl = (isset($_settings["serverUrl"])) ? $_settings["serverUrl"] : NULL;
				$this->authMode = (isset($_settings["authMode"])) ? $_settings["authMode"] : NULL;
				$this->crmRegion = (isset($_settings["crmRegion"])) ? $_settings["crmRegion"] : NULL;
				$this->port = (isset($_settings["port"])) ? $_settings["port"] : NULL;
				$this->useSsl = (isset($_settings["useSsl"])) ? $_settings["useSsl"] : false;
				$this->organizationDataUrl = (isset($_settings["organizationDataUrl"])) ? $_settings["organizationDataUrl"] : NULL;
				$this->organizationName = (isset($_settings["organizationName"])) ? $_settings["organizationName"] : NULL;
				$this->organizationUniqueName = (isset($_settings["organizationUniqueName"])) ? $_settings["organizationUniqueName"] : NULL;
				$this->organizationId = (isset($_settings["organizationId"])) ? $_settings["organizationId"] : NULL;
				$this->organizationVersion = (isset($_settings["organizationVersion"])) ? $_settings["organizationVersion"] : NULL;
				
				$this->cache = (isset($_settings["cache"])) ? $_settings["cache"] :  array("server" => "localhost", "port" => 11211);
				
				$this->oauthResource = (isset($_settings["oauthResource"])) ? $_settings["oauthResource"] : NULL;
				$this->oauthClientId = (isset($_settings["oauthClientId"])) ? $_settings["oauthClientId"] : NULL;
				$this->oauthClientSecret = (isset($_settings["oauthClientSecret"])) ? $_settings["oauthClientSecret"] : NULL;
				$this->oauthGrantType = (isset($_settings["oauthGrantType"])) ? $_settings["oauthGrantType"] : NULL;
				$this->oauthApiVersion = (isset($_settings["oauthApiVersion"])) ? $_settings["oauthApiVersion"] : NULL;
				$this->oauthAuthorizationEndpoint = (isset($_settings["oauthAuthorizationEndpoint"])) ? $_settings["oauthAuthorizationEndpoint"] : NULL;
				$this->oauthTokenEndpoint = (isset($_settings["oauthTokenEndpoint"])) ? $_settings["oauthTokenEndpoint"] : NULL;
				$this->oauthMultiTenant = (isset($_settings["oauthMultiTenant"])) ? $_settings["oauthMultiTenant"] : false;
        }
        
        /**
         * Check if all required settings are filled
         * 
         * @return bool
         */
        public function isFullSettings(){
            return ($this->discoveryUrl && $this->username && $this->password && $this->organizationUrl && $this->loginUrl && $this->domain && (($this->authMode == "OnlineFederation") ? $this->crmRegion : true));
        }
    
}
