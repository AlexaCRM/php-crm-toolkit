<?php

if (!class_exists("AlexaSDK_Settings")) :
    
    
    class AlexaSDK_Settings{
    
        public $username;
        public $password;
        public $domain;
        public $authMode;
        public $IdentityProvider;
        public $organizationUrl;
        public $discoveryUrl;
        public $loginUrl = "https://adfs.crm2011.net.au/adfs/services/trust/13/usernamemixed";
        public $organizationName;
        
        /*
          Select the right region for your CRM
          crmna:dynamics.com - North America
          crmemea:dynamics.com - Europe, the Middle East and Africa
          crmapac:dynamics.com - Asia Pacific
        */
        public $crmRegion;
        
        /*
         * Set up settings using constructor
         */
        function AlexaSDK_Settings($discoveryUrl = null, $username = null, $password = null, $organizationUrl = null, $loginUrl = null, $domain = null, $authMode = null, $region = null ){
            
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
            
            if ($domain != null && $domain != ''){
                $this->domain = $domain;
            }
            
            if ($authMode != null && $authMode != ''){
                $this->authMode = $authMode;
            }
            
            if ($region != null && $region != ''){
                $this->crmRegion = $region;
            }
        }
        
        /* Check if all settings are filled
         * @return bool
         */
        public function isFullSettings(){
            return ($this->discoveryUrl && $this->username && $this->password && $this->organizationUrl && $this->loginUrl && $this->domain && (($this->authMode == "OnlineFederation") ? $this->crmRegion : true));
        }
    
    }
    
endif;