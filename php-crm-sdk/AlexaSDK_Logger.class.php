<?php

include_once ( dirname(__FILE__) . "/Helpers/Logger.php" );

class AlexaSDK_Logger extends AlexaSDK_Abstract{

	
	public static function debug($message) {
		/*if (self::$debugMode) {
			print_r("\t".$message.";\n\t");
		}*/
	}
	
	public static function log($message, $exception = NULL){
			self::debug($message);
			
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


class AlexaSDK_Log{
	
	protected static $_instance;
	
	public static function instance() {
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