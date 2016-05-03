<?php

/**
 * AlexaSDK_Logger.php
 * 
 * This file defines AlexaSDK_AlexaSDK_Logger and AlexaSDK_Log class 
 * that used to log the errors and debug infromation into log file
 * 
 * @author alexacrm.com.au
 * @version 1.0
 * @package AlexaSDK
 */


/**
 * Logs the messages into the log file with the KLogger
 */
class AlexaSDK_Logger extends AlexaSDK_Abstract{

	/**
	 * Log the message or full exception stack into log file
	 * 
	 * @param string $message The message to log if AlexaSDK_Abstract::$debugMode is enabled
	 * @param Exception $exception the object of Exception class to write trace into log file
	 * 
	 */
	public static function log($message, $exception = NULL){
			if (self::$enableLogs){

				$l = AlexaSDK_Log::instance();

				if ($exception){
					$l->LogError((string)$exception);
				}else if (self::$debugMode){
					$l->LogDebug($message);
				}
			}
	}
}

/**
 * Singletone instance of the KLogger class
 */
class AlexaSDK_Log{
	
	/**
	 * @var Klogger $_instance;
	 */
	protected static $_instance;
	
	/**
	 * Main KLogger Instance
	 *
	 * Ensures only one instance of KLogger is loaded or can be loaded.
	 * 
	 * @static
	 * @return KLogger class object
	 */
	public static function instance() {
		
		include_once ( dirname(__FILE__) . "/Helpers/Logger.php" );
		
		if (is_null(self::$_instance)) {
			
			$dir = trailingslashit( dirname(__FILE__)).trailingslashit("logs");
			$log = $dir."log.txt";
			
			if (!file_exists($log)){
				if (!file_exists($dir)){
					wp_mkdir_p( $dir );
				}
				$htaccess_file = $dir . '.htaccess';

				if ( !file_exists( $htaccess_file ) ) {
					if ( $handle = @fopen( $htaccess_file, 'w' ) ) {
						fwrite( $handle, "Deny from all\n" );
						fclose( $handle );
					}
				}
				if ( !file_exists( $log ) ) {
					if ( $handle = @fopen( $log, 'w' ) ) {
						fclose( $handle );
					}
				}
			}
			
			self::$_instance = new KLogger($log, KLogger::DEBUG);
		}
		return self::$_instance;
	}

}