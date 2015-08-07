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
        
        
        public $use_ssl;
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
         * 
         * @var String $crmRegion
         */
        public $crmRegion;
        
        public $organizationUniqueName;
        
        public $organizationVersion;
        
        
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
        function __construct($discoveryUrl = null, $username = null, $password = null, $organizationUrl = null, $loginUrl = null, $serverUrl = null, $authMode = null, $region = null ){
            
            if ($discoveryUrl != null && $discoveryUrl != ''){
                $this->discoveryUrl = $discoveryUrl;
            }
            
            if ($username != null && $username != ''){
                $this->username = $username;
            }
            
            if ($password != null && $password != ''){
                $this->password = $password;
            }
            
            if ($organizationUrl != null && $organizationUrl != ''){
                $this->organizationUrl = $organizationUrl;
            }
            
            if ($loginUrl != null && $loginUrl != ''){
                $this->loginUrl = $loginUrl;
            }
            
            if ($serverUrl != null && $serverUrl != ''){
                $this->serverUrl = $serverUrl;
            }
            
            if ($authMode != null && $authMode != ''){
                $this->authMode = $authMode;
            }
            
            if ($region != null && $region != ''){
                $this->crmRegion = $region;
            }
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
