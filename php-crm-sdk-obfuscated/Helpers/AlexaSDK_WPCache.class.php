<?php 
class AlexaSDK_WPCache{
public function __construct() {
}
public function set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false){
if (false === ($IIIIIIllIl1l = get_transient($name))){
return set_transient( $name,$value,$IIIIIIll1lIl );
}elseif($IIIIIIll1lI1 == false){
return set_transient( $name,$value,$IIIIIIll1lIl );
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
public function cleanup($IIIIIIll1ll1 = ""){
if ($IIIIIIll1ll1 != ""){
return delete_transient($IIIIIIll1ll1);
}else{
return wp_cache_flush();
}
}
}
?>
