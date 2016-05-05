<?php

/**
 * AlexaSDK_Authentication.class.php
 * 
 * @author alexacrm.com
 * @version 1.0
 * @package AlexaSDK\Authentication
 * @subpackage Authentication
 */


class AlexaSDK_Authentication extends AlexaSDK{
	
		/**
		 * Global SDK settings
		 * 
		 * @var AlexaSDK_Settings Instance of AlexaSDK_Settings class
		 */
		public $settings;
	
		/**
		 * Object of AlexaSDK class
		 * 
		 * @var AlexaSDK
		 */
		protected $auth;
		
		/**
		 *  Token that used to construct SOAP requests
		 * 
		 * @var Array 
		 */
		protected $organizationSecurityToken = NULL;

		/**
		 *  Token that used to construct SOAP requests
		 * 
		 * @var Array 
		 */
		protected $discoverySecurityToken = NULL;
		
		
		public function setCachedSecurityToken($service, $token) {
			if ($this->auth->cacheClass) {
				$this->auth->cacheClass->set(strtolower($service) . "securitytoken", serialize($token), floor($token['expiryTime'] - time()));
			}
		}

		public function getCachedSecurityToken($service, &$securityToken) {
			if ($this->auth->cacheClass) {
				$token = $this->auth->cacheClass->get(strtolower($service) . "securitytoken");
				
				if ($token != NULL) {
					$securityToken = unserialize($token);
					return TRUE;
				}
			}
			$securityToken = NULL;
			return FALSE;
		}
	
}