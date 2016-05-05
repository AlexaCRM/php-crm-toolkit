<?php

/**
 * init.php
 * 
 * Toolkit init file
 * Need to include it into project to activate include the toolkit files
 * 
 * @author alexacrm.com
 * @version 1.0
 * @package AlexaSDK
 */

abstract class PhpCrmToolkit_Init{
	
		/**
		 * Classes prefix for autoload
		 * 
		 * @var String
		 */
		protected static $classPrefix = "AlexaSDK";

		/**
		 * Implementation of Class Autoloader
		 * See http://www.php.net/manual/en/function.spl-autoload-register.php
		 *
		 * @param String $className the name of the Class to load
		 */
		public static function loadClass($className) {
			/* Only load classes that don't exist, and are part of DynamicsCRM2011 */
			if ((class_exists($className)) || (strpos($className, self::$classPrefix) === false)) {
				return false;
			}

			/* Work out the filename of the Class to be loaded. */
			$classFilePath = trailingslashit (dirname(__FILE__)) . $className . '.class.php';

			/* Only try to load files that actually exist and can be read */
			if ((file_exists($classFilePath) === false) || (is_readable($classFilePath) === false)) {
				return false;
			}

			/* Don't load it if it's already been loaded */
			include_once $classFilePath;
		}
		
		public static function includes(){
			
			include_once ( dirname(__FILE__) . "/authentication/AlexaSDK_Authentication.class.php" );
			include_once ( dirname(__FILE__) . "/authentication/AlexaSDK_OnlineFederation.class.php" );
			include_once ( dirname(__FILE__) . "/authentication/AlexaSDK_Federation.class.php" );
			include_once ( dirname(__FILE__) . "/authentication/AlexaSDK_Oauth2.php" );
			include_once ( dirname(__FILE__) . "/helpers/klogger.php" );
			include_once ( dirname(__FILE__) . "/helpers/AlexaSDK_Cache.class.php" );
			include_once ( dirname(__FILE__) . "/helpers/AlexaSDK_FormValidator.class.php" );
		}
}

/* Register the Class Loader */
spl_autoload_register(Array('PhpCrmToolkit_Init', 'loadClass'));

PhpCrmToolkit_Init::includes();