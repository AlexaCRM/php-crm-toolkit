<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSUlJbElsbCddPSdkaXJuYW1lJzs=')); ?><?php 
class AlexaSDK_Cache{
private $IIIIIIllIl1l = NULL;
public function __construct($options = NULL) {
$this->includes();
if (function_exists('wp_using_ext_object_cache') &&wp_using_ext_object_cache()){
$this->IIIIIIllIl1l = new AlexaSDK_WPCache($options);
}else{
AlexaSDK_PhpFastCache::$server = array(array($options["server"],$options["port"]));
$this->IIIIIIllIl1l = new AlexaSDK_PhpFastCache($options);
}
}
private function includes(){
include_once ( $GLOBALS['IIIIIIIIlIll'](__FILE__) ."/AlexaSDK_WPCache.class.php");
include_once ( $GLOBALS['IIIIIIIIlIll'](__FILE__) ."/AlexaSDK_PhpFastCache.class.php");
}
public function set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false){
if ($value){
return $this->IIIIIIllIl1l->set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
}else{
return NULL;
}
}
public function get($name){
return $this->IIIIIIllIl1l->get($name);
}
public function cleanup(){
return $this->IIIIIIllIl1l->cleanup($IIIIIIll1ll1 = "");
}
}?>
