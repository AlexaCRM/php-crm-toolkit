<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AlexaSDK_Cache{
    
        private $cache = NULL;
        
        public function __construct($options = NULL) {
            
                $this->includes();
                
                if (function_exists('wp_using_ext_object_cache') && wp_using_ext_object_cache()){
                    
                        $this->cache = new AlexaSDK_WPCache();
                }else{
                    
                        $this->cache = new AlexaSDK_PhpFastCache();
                }
        }
        
        private function includes(){
                include_once ( dirname(__FILE__) . "/AlexaSDK_WPCache.class.php" );
                include_once ( dirname(__FILE__) . "/AlexaSDK_PhpFastCache.class.php" );
        }
        
        
        public function set($name, $value, $time_in_second = 600, $skip_if_existing = false){
                if ($value){
                    return $this->cache->set($name, $value, $time_in_second, $skip_if_existing);
                }else{
                    return NULL;
                }
        }
        
        public function get($name){
            
                return $this->cache->get($name);
            
        }
        
        public function cleanup(){
            
                return $this->cache->cleanup($option = "");
            
        }
}