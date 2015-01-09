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
            
            
            
                /*
                 *  Check domain for auth type and discovery url
                 */
                
                //$this->domain = $domain;
                
                //if (strpos($discoveryUrl, "dynamics.com")){
                    
                    /* Connect with Microsoft Office 365 and Microsoft Dynamics CRM Online */
                    
                    /*$this->organizationName = self::parseOrganizationName($domain);
                    
                    if (strpos($username, "onmicrosoft")){
                        
                        $this->authType = "O365";
                        $this->IdentityProvider = "Microsoft Office 365";
                        
                        if (strpos($domain, "crm4")){
                            
                            $this->crmRegion = "crmemea:dynamics.com";
                            $this->discoveryUrl = "https://disco.crm4.dynamics.com/XRMServices/2011/Discovery.svc";
                            $this->organizationUrl = "https://".$this->organizationName.".api.crm4.dynamics.com/XrmServices/2011/Organization.svc";
                            
                        }elseif(strpos($domain, "crm5")){
                            
                            $this->crmRegion = "crmapac:dynamics.com";
                            $this->discoveryUrl = "https://disco.crm5.dynamics.com/XRMServices/2011/Discovery.svc";
                            $this->organizationUrl = "https://".$this->organizationName.".api.crm5.dynamics.com/XrmServices/2011/Organization.svc";
                            
                        }else{
                            
                            $this->crmRegion = "crmna:dynamics.com";
                            $this->discoveryUrl = "https://disco.crm.dynamics.com/XRMServices/2011/Discovery.svc";
                            $this->organizationUrl = "https://".$this->organizationName.".api.crm.dynamics.com/XrmServices/2011/Organization.svc";
                            
                        }*/
                        
                    /*}else{
                        
                        /*$this->IdentityProvider = "Microsoft Account";
                        
                        throw new Exception("Unable to connect with this email. Probably you try to login with Microsoft Account( Example some@live.com ) instead Microsoft Office 365 Account( Example: username@organization.dynamics.com))");
                        */
                    //}
                    
                //}else{
                    /* Active Directory and claims-based authentication */
                    
                    /*$this->authType = "ADFS";
                    $this->discoveryUrl = $discoveryUrl;*/
                    //$this->organizationUrl = $domain."/XRMServices/2011/Organization.svc";
                    
                //}
                
            //}
            
            
        }
        
        
        
        /* Check if all settings are filled
         * @return bool
         */
        public function isFullSettings(){
            return ($this->discoveryUrl && $this->username && $this->password && $this->organizationUrl && $this->loginUrl && $this->domain && (($this->authMode == "OnlineFederation") ? $this->crmRegion : true));
        }
    
    }
    
    
endif;