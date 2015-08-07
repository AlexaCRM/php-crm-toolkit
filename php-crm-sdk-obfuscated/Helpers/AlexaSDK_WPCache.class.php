<?php 
class AlexaSDK_WPCache{
public function __construct() {
}
public function set($name,$value,$IIIIIIll1lII = 600,$IIIIIIll1lIl = false){
if (false === ($IIIIIIll1Il1 = get_transient($name))){
return set_transient( $name,$value,$IIIIIIll1lII );
}elseif($IIIIIIll1lIl == false){
return set_transient( $name,$value,$IIIIIIll1lII );
}
}
public function get($name){
$value = get_transient($name);
if (false === $value){
return NULL;
}else{
return $value;
}
}
public function cleanup($IIIIIIll1lll = ""){
if ($IIIIIIll1lll != ""){
return delete_transient($IIIIIIll1lll);
}else{
return wp_cache_flush();
}
}
}
?>
