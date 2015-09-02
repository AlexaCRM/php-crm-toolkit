<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSTFsSWxsMSddPSdmaWxlbXRpbWUnOyRHTE9CQUxTWydJSUlJSUkxSTExbDEnXT0nZXhlYyc7JEdMT0JBTFNbJ0lJSUlJSUkxMTFJSSddPSd0aW1lJzskR0xPQkFMU1snSUlJSUlJMUlsMTFJJ109J2ZsdXNoJzskR0xPQkFMU1snSUlJSUlJMUlJbDExJ109J2ZpbGVzaXplJzskR0xPQkFMU1snSUlJSUlJMUlJbDFJJ109J2lzX2Rpcic7JEdMT0JBTFNbJ0lJSUlJSTFJSWxsMSddPSdyZWFkZGlyJzskR0xPQkFMU1snSUlJSUlJMUlJbGxsJ109J29wZW5kaXInOyRHTE9CQUxTWydJSUlJSUkxSUlsSUknXT0ndW5saW5rJzskR0xPQkFMU1snSUlJSUlJSWwxMTFJJ109J2V4cGxvZGUnOyRHTE9CQUxTWydJSUlJSUlsMTFsbGwnXT0naXNfbnVtZXJpYyc7JEdMT0JBTFNbJ0lJSUlJSUkxbElJbCddPSd1bnNlcmlhbGl6ZSc7JEdMT0JBTFNbJ0lJSUlJSUkxMWwxbCddPSdzZXJpYWxpemUnOyRHTE9CQUxTWydJSUlJSUlsMTFJSWwnXT0ncHJlZ19yZXBsYWNlJzskR0xPQkFMU1snSUlJSUlJbEkxMWwxJ109J2FycmF5X2tleXMnOyRHTE9CQUxTWydJSUlJSUlJSWxJSTEnXT0naXNfYXJyYXknOyRHTE9CQUxTWydJSUlJSUlsSTFJbGwnXT0nZGF0ZSc7JEdMT0JBTFNbJ0lJSUlJSUlsMUlsMSddPSdzdHJ0b2xvd2VyJzskR0xPQkFMU1snSUlJSUlJbElJMWxJJ109J2luX2FycmF5JzskR0xPQkFMU1snSUlJSUlJbDFsMWxJJ109J2NobW9kJzskR0xPQkFMU1snSUlJSUlJSUlsSWxsJ109J2Rpcm5hbWUnOyRHTE9CQUxTWydJSUlJSUlsMWxsMTEnXT0nZmNsb3NlJzskR0xPQkFMU1snSUlJSUlJbDFsbDFsJ109J2Z3cml0ZSc7JEdMT0JBTFNbJ0lJSUlJSWwxbGwxSSddPSdmb3Blbic7JEdMT0JBTFNbJ0lJSUlJSWxJMUkxMSddPSdzdHJwb3MnOyRHTE9CQUxTWydJSUlJSUlsMWxsSWwnXT0naXNfd3JpdGFibGUnOyRHTE9CQUxTWydJSUlJSUlsMWxJMWwnXT0naW5pX2dldCc7JEdMT0JBTFNbJ0lJSUlJSUlsMUlJMSddPSdjb3VudCc7')); ?><?php 
class AlexaSDK_PhpFastCache {
public static $storage = "auto";
public static $IIIIIIl1Ill1 = 1;
public static $IIIIIIl1Il1I = 40;
public static $path = "";
public static $IIIIIIl1Il11 = "cache.storage";
public static $IIIIIIl1I1II = true;
public static $IIIIIIll1ll1 = array();
public static $server = array(array("localhost",11211));
public static $IIIIIIl1I1Il = false;
public static $IIIIIIl1I1I1 = false;
private static $IIIIIIl1I1lI = 0;
private static $IIIIIIl1I1ll = array();
private static $IIIIIIl1I1l1 = array("pdo","mpdo","files","memcache","memcached","apc","xcache","wincache");
private static $IIIIIIl1I11I = "pdo.caching";
private static $IIIIIIl1I11l = "objects";
private static $IIIIIIl1I111 = "";
private static $IIIIIIl1lIII = array();
public static $IIIIIIl1lIIl = array();
private static $IIIIIIl1lII1 = array(
"path"=>false,
"servers"=>array(),
"config_file"=>"",
);
private static $objects = array(
"memcache"=>"",
"memcached"=>"",
"pdo"=>"",
);
private static function getOS() {
$os = array(
"os"=>PHP_OS,
"php"=>PHP_SAPI,
"system"=>php_uname(),
"unique"=>md5(php_uname() .PHP_OS .PHP_SAPI)
);
return $os;
}
public static function systemInfo() {
if ($GLOBALS['IIIIIIIl1II1'](self::$IIIIIIl1lIIl) == 0) {
self::$IIIIIIl1lIIl['os'] = self::getOS();
self::$IIIIIIl1lIIl['errors'] = array();
self::$IIIIIIl1lIIl['storage'] = "";
self::$IIIIIIl1lIIl['method'] = "pdo";
self::$IIIIIIl1lIIl['drivers'] = array(
"apc"=>false,
"xcache"=>false,
"memcache"=>false,
"memcached"=>false,
"wincache"=>false,
"pdo"=>false,
"mpdo"=>false,
"files"=>false,
);
if (extension_loaded('apc') &&$GLOBALS['IIIIIIl1lI1l']('apc.enabled')) {
self::$IIIIIIl1lIIl['drivers']['apc'] = true;
self::$IIIIIIl1lIIl['storage'] = "memory";
self::$IIIIIIl1lIIl['method'] = "apc";
}
if (extension_loaded('xcache') &&function_exists("xcache_get")) {
self::$IIIIIIl1lIIl['drivers']['xcache'] = true;
self::$IIIIIIl1lIIl['storage'] = "memory";
self::$IIIIIIl1lIIl['method'] = "xcache";
}
if (extension_loaded('wincache') &&function_exists("wincache_ucache_set")) {
self::$IIIIIIl1lIIl['drivers']['wincache'] = true;
self::$IIIIIIl1lIIl['storage'] = "memory";
self::$IIIIIIl1lIIl['method'] = "wincache";
}
if (function_exists("memcache_connect")) {
self::$IIIIIIl1lIIl['drivers']['memcache'] = true;
try {
memcache_connect("127.0.0.1");
self::$IIIIIIl1lIIl['storage'] = "memory";
self::$IIIIIIl1lIIl['method'] = "memcache";
}catch (Exception $IIIIIIl1lI11) {
}
}
if (class_exists("memcached")) {
self::$IIIIIIl1lIIl['drivers']['memcached'] = true;
try {
$memcached = new memcached();
$memcached->addServer("127.0.0.1","11211");
self::$IIIIIIl1lIIl['storage'] = "memory";
self::$IIIIIIl1lIIl['method'] = "memcached";
}catch (Exception $IIIIIIl1lI11) {
}
}
if (extension_loaded('pdo_sqlite')) {
self::$IIIIIIl1lIIl['drivers']['pdo'] = true;
self::$IIIIIIl1lIIl['drivers']['mpdo'] = true;
}
if ($GLOBALS['IIIIIIl1llIl'](self::getPath(true))) {
self::$IIIIIIl1lIIl['drivers']['files'] = true;
}
if (self::$IIIIIIl1lIIl['storage'] == "") {
if (extension_loaded('pdo_sqlite')) {
self::$IIIIIIl1lIIl['storage'] = "disk";
self::$IIIIIIl1lIIl['method'] = "pdo";
}else {
self::$IIIIIIl1lIIl['storage'] = "disk";
self::$IIIIIIl1lIIl['method'] = "files";
}
}
if (self::$IIIIIIl1lIIl['storage'] == "disk"&&!$GLOBALS['IIIIIIl1llIl'](self::getPath())) {
self::$IIIIIIl1lIIl['errors'][] = "Please Create & CHMOD 0777 or any Writeable Mode for ".self::getPath();
}
}
return self::$IIIIIIl1lIIl;
}
private static function isPHPModule() {
if (PHP_SAPI == "apache2handler") {
return true;
}else {
if ($GLOBALS['IIIIIIlI1I11'](PHP_SAPI,"handler") !== false) {
return true;
}
}
return false;
}
static function htaccessGen($path = "") {
if (self::$IIIIIIl1I1II == true) {
if (!file_exists($path ."/.htaccess")) {
$IIIIIIl1llll = "order deny, allow \r\n
deny from all \r\n
allow from 127.0.0.1";
$IIIIIIl1lll1 = @$GLOBALS['IIIIIIl1ll1I']($path ."/.htaccess","w+");
@$GLOBALS['IIIIIIl1ll1l']($IIIIIIl1lll1,$IIIIIIl1llll);
@$GLOBALS['IIIIIIl1ll11']($IIIIIIl1lll1);
}else {
}
}
}
private static function getPath($IIIIIIl1l1Il = false) {
if (self::$path == '') {
if (self::isPHPModule()) {
$IIIIIIl1l1I1 = $GLOBALS['IIIIIIl1lI1l']('upload_tmp_dir') ?$GLOBALS['IIIIIIl1lI1l']('upload_tmp_dir') : sys_get_temp_dir();
self::$path = $IIIIIIl1l1I1;
}else {
self::$path = $GLOBALS['IIIIIIIIlIll'](__FILE__);
}
}
if ($IIIIIIl1l1Il == false &&self::$IIIIIIl1lII1['path'] == false) {
if (!file_exists(self::$path ."/".self::$IIIIIIl1Il11 ."/") ||!$GLOBALS['IIIIIIl1llIl'](self::$path ."/".self::$IIIIIIl1Il11 ."/")) {
if (!file_exists(self::$path ."/".self::$IIIIIIl1Il11 ."/")) {
@mkdir(self::$path ."/".self::$IIIIIIl1Il11 ."/",0777);
}
if (!$GLOBALS['IIIIIIl1llIl'](self::$path ."/".self::$IIIIIIl1Il11 ."/")) {
@$GLOBALS['IIIIIIl1l1lI'](self::$path ."/".self::$IIIIIIl1Il11 ."/",0777);
}
if (!file_exists(self::$path ."/".self::$IIIIIIl1Il11 ."/") ||!$GLOBALS['IIIIIIl1llIl'](self::$path ."/".self::$IIIIIIl1Il11 ."/")) {
die("Sorry, Please create ".self::$path ."/".self::$IIIIIIl1Il11 ."/ and SET Mode 0777 or any Writable Permission!");
}
}
self::$IIIIIIl1lII1['path'] = true;
self::htaccessGen(self::$path ."/".self::$IIIIIIl1Il11 ."/");
}
return self::$path ."/".self::$IIIIIIl1Il11 ."/";
}
public static function autoconfig($name = "") {
$IIIIIIllIl1l = self::cacheMethod($name);
if ($IIIIIIllIl1l != ""&&$IIIIIIllIl1l != self::$storage &&$IIIIIIllIl1l != "auto") {
return $IIIIIIllIl1l;
}
$os = self::getOS();
if (self::$storage == ""||self::$storage == "auto") {
if (extension_loaded('apc') &&$GLOBALS['IIIIIIl1lI1l']('apc.enabled') &&$GLOBALS['IIIIIIlI1I11'](PHP_SAPI,"CGI") === false) {
self::$IIIIIIl1lIIl['drivers']['apc'] = true;
self::$IIIIIIl1lIIl['storage'] = "memory";
self::$IIIIIIl1lIIl['method'] = "apc";
}elseif (extension_loaded('xcache')) {
self::$IIIIIIl1lIIl['drivers']['xcache'] = true;
self::$IIIIIIl1lIIl['storage'] = "memory";
self::$IIIIIIl1lIIl['method'] = "xcache";
}else {
$IIIIIIl1l1l1 = false;
if (file_exists(self::getPath() ."/config.".$os['unique'] .".cache.ini")) {
$IIIIIIl1l11I = self::decode(file_get_contents(self::getPath() ."/config.".$os['unique'] .".cache.ini"));
if (!isset($IIIIIIl1l11I['value'])) {
$IIIIIIl1l1l1 = true;
}else {
$IIIIIIl1l11I = $IIIIIIl1l11I['value'];
self::$IIIIIIl1lIIl = $IIIIIIl1l11I;
}
}else {
$IIIIIIl1l11I = self::systemInfo();
}
if (isset($IIIIIIl1l11I['os']['unique'])) {
if ($IIIIIIl1l11I['os']['unique'] != $os['unique']) {
$IIIIIIl1l1l1 = true;
}
}else {
$IIIIIIl1l1l1 = true;
}
if (!file_exists(self::getPath() ."/config.".$os['unique'] .".cache.ini") ||$IIIIIIl1l1l1 == true) {
$IIIIIIl1l11I = self::systemInfo();
self::$IIIIIIl1lIIl = $IIIIIIl1l11I;
try {
$IIIIIIl1lll1 = $GLOBALS['IIIIIIl1ll1I'](self::getPath() ."/config.".$os['unique'] .".cache.ini","w+");
$GLOBALS['IIIIIIl1ll1l']($IIIIIIl1lll1,self::encode($IIIIIIl1l11I));
$GLOBALS['IIIIIIl1ll11']($IIIIIIl1lll1);
}catch (Exception $IIIIIIl1lI11) {
die("Please chmod 0777 ".self::getPath() ."/config.".$os['unique'] .".cache.ini");
}
}else {
}
}
self::$storage = self::$IIIIIIl1lIIl['method'];
}else {
if ($GLOBALS['IIIIIIlII1lI'](self::$storage,array("files","pdo","mpdo"))) {
self::$IIIIIIl1lIIl['storage'] = "disk";
}elseif ($GLOBALS['IIIIIIlII1lI'](self::$storage,array("apc","memcache","memcached","wincache","xcache"))) {
self::$IIIIIIl1lIIl['storage'] = "memory";
}else {
self::$IIIIIIl1lIIl['storage'] = "";
}
if (self::$IIIIIIl1lIIl['storage'] == ""||!$GLOBALS['IIIIIIlII1lI'](self::$storage,self::$IIIIIIl1I1l1)) {
die("Don't have this Cache ".self::$storage ." In your System! Please double check!");
}
self::$IIIIIIl1lIIl['method'] = $GLOBALS['IIIIIIIl1Il1'](self::$storage);
}
if (self::$IIIIIIl1lIIl['method'] == "files") {
$IIIIIIl1l11l = self::files_get("last_cleanup_cache");
if ($IIIIIIl1l11l == null) {
self::files_cleanup();
self::files_set("last_cleanup_cache",@$GLOBALS['IIIIIIlI1Ill']("U"),3600 * self::$IIIIIIl1Ill1);
}
}
return self::$IIIIIIl1lIIl['method'];
}
private static function cacheMethod($name = "") {
$IIIIIIllIl1l = self::$storage;
if ($GLOBALS['IIIIIIIIlII1']($name)) {
$key = $GLOBALS['IIIIIIlI11l1']($name);
$key = $key[0];
if ($GLOBALS['IIIIIIlII1lI']($key,self::$IIIIIIl1I1l1)) {
$IIIIIIllIl1l = $key;
}
}
return $IIIIIIllIl1l;
}
public static function safename($name) {
return $GLOBALS['IIIIIIIl1Il1']($GLOBALS['IIIIIIl11IIl']("/[^a-zA-Z0-9_\s\.]+/","",$name));
}
private static function encode($value,$IIIIIIll1lIl = "") {
$value = $GLOBALS['IIIIIII11l1l'](array(
"time"=>@$GLOBALS['IIIIIIlI1Ill']("U"),
"value"=>$value,
"endin"=>$IIIIIIll1lIl
));
return $value;
}
private static function decode($value) {
$IIIIIIl11Ill = @$GLOBALS['IIIIIII1lIIl']($value);
if ($IIIIIIl11Ill == false) {
return $value;
}else {
return $IIIIIIl11Ill;
}
}
public static function cleanup($IIIIIIll1ll1 = "") {
$IIIIIIl11Il1 = self::autoconfig();
self::$IIIIIIl1I1ll = array();
switch ($IIIIIIl11Il1) {
case "pdo":
return self::pdo_cleanup($IIIIIIll1ll1);
break;
case "mpdo":
return self::pdo_cleanup($IIIIIIll1ll1);
break;
case "files":
return self::files_cleanup($IIIIIIll1ll1);
break;
case "memcache":
return self::memcache_cleanup($IIIIIIll1ll1);
break;
case "memcached":
return self::memcached_cleanup($IIIIIIll1ll1);
break;
case "wincache":
return self::wincache_cleanup($IIIIIIll1ll1);
break;
case "apc":
return self::apc_cleanup($IIIIIIll1ll1);
break;
case "xcache":
return self::xcache_cleanup($IIIIIIll1ll1);
break;
default:
return self::pdo_cleanup($IIIIIIll1ll1);
break;
}
}
public static function delete($name = "string|array(db->item)") {
$IIIIIIl11Il1 = self::autoconfig($name);
if (self::$IIIIIIl1I1Il == true) {
$IIIIIIl11I1l = md5($GLOBALS['IIIIIII11l1l']($IIIIIIl11Il1 .$name));
if (isset(self::$IIIIIIl1I1ll[$IIIIIIl11I1l])) {
unset(self::$IIIIIIl1I1ll[$IIIIIIl11I1l]);
}
}
switch ($IIIIIIl11Il1) {
case "pdo":
return self::pdo_delete($name);
break;
case "mpdo":
return self::pdo_delete($name);
break;
case "files":
return self::files_delete($name);
break;
case "memcache":
return self::memcache_delete($name);
break;
case "memcached":
return self::memcached_delete($name);
break;
case "wincache":
return self::wincache_delete($name);
break;
case "apc":
return self::apc_delete($name);
break;
case "xcache":
return self::xcache_delete($name);
break;
default:
return self::pdo_delete($name);
break;
}
}
public static function exists($name = "string|array(db->item)") {
$IIIIIIl11Il1 = self::autoconfig($name);
switch ($IIIIIIl11Il1) {
case "pdo":
return self::pdo_exist($name);
break;
case "mpdo":
return self::pdo_exist($name);
break;
case "files":
return self::files_exist($name);
break;
case "memcache":
return self::memcache_exist($name);
break;
case "memcached":
return self::memcached_exist($name);
break;
case "wincache":
return self::wincache_exist($name);
break;
case "apc":
return self::apc_exist($name);
break;
case "xcache":
return self::xcache_exist($name);
break;
default:
return self::pdo_exist($name);
break;
}
}
public static function deleteMulti($IIIIIIl11lIl = array()) {
$IIIIIIl11lI1 = array();
foreach ($IIIIIIl11lIl as $IIIIIIl11llI =>$name) {
if (!$GLOBALS['IIIIIIl11lll']($IIIIIIl11llI)) {
$IIIIIIl11ll1 = $IIIIIIl11llI ."_".$name;
$name = array($IIIIIIl11llI =>$name);
}else {
$IIIIIIl11ll1 = $name;
}
$IIIIIIl11lI1[$IIIIIIl11ll1] = self::delete($name);
}
return $IIIIIIl11lI1;
}
public static function setMulti($IIIIIIl11l1l = array(),$IIIIIIl11l11 = 600,$IIIIIIl111II = false) {
$IIIIIIl11lI1 = array();
foreach ($IIIIIIl11l1l as $IIIIIIl11lIl) {
$IIIIIIIl11Il = $GLOBALS['IIIIIIlI11l1']($IIIIIIl11lIl);
if ($IIIIIIIl11Il[0] != "0") {
$IIIIIII1IIll = $IIIIIIIl11Il[0];
$name = isset($IIIIIIl11lIl[$IIIIIII1IIll]) ?array($IIIIIII1IIll =>$IIIIIIl11lIl[$IIIIIII1IIll]) : "";
$IIIIIIl11ll1 = $IIIIIII1IIll ."_".$IIIIIIl11lIl[$IIIIIII1IIll];
$IIIIIIl11Ill = 0;
}else {
$name = isset($IIIIIIl11lIl[0]) ?$IIIIIIl11lIl[0] : "";
$IIIIIIl11Ill = 1;
$IIIIIIl11ll1 = $name;
}
$value = isset($IIIIIIl11lIl[$IIIIIIl11Ill]) ?$IIIIIIl11lIl[$IIIIIIl11Ill] : "";
$IIIIIIl11Ill++;
$time = isset($IIIIIIl11lIl[$IIIIIIl11Ill]) ?$IIIIIIl11lIl[$IIIIIIl11Ill] : $IIIIIIl11l11;
$IIIIIIl11Ill++;
$IIIIIIl111I1 = isset($IIIIIIl11lIl[$IIIIIIl11Ill]) ?$IIIIIIl11lIl[$IIIIIIl11Ill] : $IIIIIIl111II;
$IIIIIIl11Ill++;
if ($name != ""&&$value != "") {
$IIIIIIl11lI1[$IIIIIIl11ll1] = self::set($name,$value,$time,$IIIIIIl111I1);
}
}
return $IIIIIIl11lI1;
}
public static function set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false) {
$IIIIIIl11Il1 = self::autoconfig($name);
if (self::$IIIIIIl1I1Il == true) {
$IIIIIIl11I1l = md5($GLOBALS['IIIIIII11l1l']($IIIIIIl11Il1 .$name));
self::$IIIIIIl1I1ll[$IIIIIIl11I1l] = $value;
}
switch ($IIIIIIl11Il1) {
case "pdo":
return self::pdo_set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
break;
case "mpdo":
return self::pdo_set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
break;
case "files":
return self::files_set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
break;
case "memcache":
return self::memcache_set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
break;
case "memcached":
return self::memcached_set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
break;
case "wincache":
return self::wincache_set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
break;
case "apc":
return self::apc_set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
break;
case "xcache":
return self::xcache_set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
break;
default:
return self::pdo_set($name,$value,$IIIIIIll1lIl,$IIIIIIll1lI1);
break;
}
}
public static function decrement($name,$IIIIIIl111ll = 1) {
$IIIIIIl11Il1 = self::autoconfig($name);
if (self::$IIIIIIl1I1Il == true) {
$IIIIIIl11I1l = md5($GLOBALS['IIIIIII11l1l']($IIIIIIl11Il1 .$name));
if (isset(self::$IIIIIIl1I1ll[$IIIIIIl11I1l])) {
self::$IIIIIIl1I1ll[$IIIIIIl11I1l] = (Int) self::$IIIIIIl1I1ll[$IIIIIIl11I1l] -$IIIIIIl111ll;
}else {
self::$IIIIIIl1I1ll[$IIIIIIl11I1l] = $IIIIIIl111ll;
}
}
switch ($IIIIIIl11Il1) {
case "pdo":
return self::pdo_decrement($name,$IIIIIIl111ll);
break;
case "mpdo":
return self::pdo_decrement($name,$IIIIIIl111ll);
break;
case "files":
return self::files_decrement($name,$IIIIIIl111ll);
break;
case "memcache":
return self::memcache_decrement($name,$IIIIIIl111ll);
break;
case "memcached":
return self::memcached_decrement($name,$IIIIIIl111ll);
break;
case "wincache":
return self::wincache_decrement($name,$IIIIIIl111ll);
break;
case "apc":
return self::apc_decrement($name,$IIIIIIl111ll);
break;
case "xcache":
return self::xcache_decrement($name,$IIIIIIl111ll);
break;
default:
return self::pdo_decrement($name,$IIIIIIl111ll);
break;
}
}
public static function get($name) {
$IIIIIIl11Il1 = self::autoconfig($name);
if (self::$IIIIIIl1I1Il == true) {
$IIIIIIl11I1l = md5($GLOBALS['IIIIIII11l1l']($IIIIIIl11Il1 .$name));
if (isset(self::$IIIIIIl1I1ll[$IIIIIIl11I1l])) {
return self::$IIIIIIl1I1ll[$IIIIIIl11I1l];
}
}
switch ($IIIIIIl11Il1) {
case "pdo":
return self::pdo_get($name);
break;
case "mpdo":
return self::pdo_get($name);
break;
case "files":
return self::files_get($name);
break;
case "memcache":
return self::memcache_get($name);
break;
case "memcached":
return self::memcached_get($name);
break;
case "wincache":
return self::wincache_get($name);
break;
case "apc":
return self::apc_get($name);
break;
case "xcache":
return self::xcache_get($name);
break;
default:
return self::pdo_get($name);
break;
}
}
public static function getMulti($IIIIIIl11lIl = array()) {
$IIIIIIl11lI1 = array();
foreach ($IIIIIIl11lIl as $IIIIIIl11llI =>$name) {
if (!$GLOBALS['IIIIIIl11lll']($IIIIIIl11llI)) {
$IIIIIIl11ll1 = $IIIIIIl11llI ."_".$name;
$name = array($IIIIIIl11llI =>$name);
}else {
$IIIIIIl11ll1 = $name;
}
$IIIIIIl11lI1[$IIIIIIl11ll1] = self::get($name);
}
return $IIIIIIl11lI1;
}
public static function stats() {
$IIIIIIl11Il1 = self::autoconfig();
switch ($IIIIIIl11Il1) {
case "pdo":
return self::pdo_stats();
break;
case "mpdo":
return self::pdo_stats();
break;
case "files":
return self::files_stats();
break;
case "memcache":
return self::memcache_stats();
break;
case "memcached":
return self::memcached_stats();
break;
case "wincache":
return self::wincache_stats();
break;
case "apc":
return self::apc_stats();
break;
case "xcache":
return self::xcache_stats();
break;
default:
return self::pdo_stats();
break;
}
}
public static function increment($name,$IIIIIIl111ll = 1) {
$IIIIIIl11Il1 = self::autoconfig($name);
if (self::$IIIIIIl1I1Il == true) {
$IIIIIIl11I1l = md5($GLOBALS['IIIIIII11l1l']($IIIIIIl11Il1 .$name));
if (isset(self::$IIIIIIl1I1ll[$IIIIIIl11I1l])) {
self::$IIIIIIl1I1ll[$IIIIIIl11I1l] = (Int) self::$IIIIIIl1I1ll[$IIIIIIl11I1l] +$IIIIIIl111ll;
}else {
self::$IIIIIIl1I1ll[$IIIIIIl11I1l] = $IIIIIIl111ll;
}
}
switch ($IIIIIIl11Il1) {
case "pdo":
return self::pdo_increment($name,$IIIIIIl111ll);
break;
case "mpdo":
return self::pdo_increment($name,$IIIIIIl111ll);
break;
case "files":
return self::files_increment($name,$IIIIIIl111ll);
break;
case "memcache":
return self::memcache_increment($name,$IIIIIIl111ll);
break;
case "memcached":
return self::memcached_increment($name,$IIIIIIl111ll);
break;
case "wincache":
return self::wincache_increment($name,$IIIIIIl111ll);
break;
case "apc":
return self::apc_increment($name,$IIIIIIl111ll);
break;
case "xcache":
return self::xcache_increment($name,$IIIIIIl111ll);
break;
default:
return self::pdo_increment($name,$IIIIIIl111ll);
break;
}
}
private static function files_exist($name) {
$data = self::files_get($name);
if ($data == null) {
return false;
}else {
return true;
}
}
private static function files_set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false) {
$db = self::selectDB($name);
$name = $db['item'];
$IIIIII1IIIlI = $db['db'];
$path = self::getPath();
$IIIIII1IIIll = $GLOBALS['IIIIIIIl111I']("/",$IIIIII1IIIlI);
foreach ($IIIIII1IIIll as $IIIIII1IIIl1) {
if ($IIIIII1IIIl1 != ""&&$IIIIII1IIIl1 != "."&&$IIIIII1IIIl1 != "..") {
$path.="/".$IIIIII1IIIl1;
if (!file_exists($path)) {
mkdir($path,0777);
}
}
}
$file = $path ."/".$name .".c.html";
$IIIIII1III1l = true;
if (file_exists($file)) {
$data = self::decode(file_get_contents($file));
if ($IIIIIIll1lI1 == true &&((Int) $data['time'] +(Int) $data['endin'] >@$GLOBALS['IIIIIIlI1Ill']("U"))) {
$IIIIII1III1l = false;
}
}
if ($IIIIII1III1l == true) {
try {
$IIIIIIl1lll1 = $GLOBALS['IIIIIIl1ll1I']($file,"w+");
$GLOBALS['IIIIIIl1ll1l']($IIIIIIl1lll1,self::encode($value,$IIIIIIll1lIl));
$GLOBALS['IIIIIIl1ll11']($IIIIIIl1lll1);
}catch (Exception $IIIIIIl1lI11) {
die("Sorry, can't write cache to file :".$file);
}
}
return $value;
}
private static function files_get($name) {
$db = self::selectDB($name);
$name = $db['item'];
$IIIIII1IIIlI = $db['db'];
$path = self::getPath();
$IIIIII1IIIll = $GLOBALS['IIIIIIIl111I']("/",$IIIIII1IIIlI);
foreach ($IIIIII1IIIll as $IIIIII1IIIl1) {
if ($IIIIII1IIIl1 != ""&&$IIIIII1IIIl1 != "."&&$IIIIII1IIIl1 != "..") {
$path.="/".$IIIIII1IIIl1;
}
}
$file = $path ."/".$name .".c.html";
if (!file_exists($file)) {
return null;
}
$data = self::decode(file_get_contents($file));
if (!isset($data['time']) ||!isset($data['endin']) ||!isset($data['value'])) {
return null;
}
if ($data['time'] +$data['endin'] <@$GLOBALS['IIIIIIlI1Ill']("U")) {
$GLOBALS['IIIIII1IIlII']($file);
return null;
}
return isset($data['value']) ?$data['value'] : null;
}
private static function files_stats($IIIIII1IIIl1 = "") {
$total = array(
"expired"=>0,
"size"=>0,
"files"=>0
);
if ($IIIIII1IIIl1 == "") {
$IIIIII1IIIl1 = self::getPath();
}
$IIIIII1IIllI = $GLOBALS['IIIIII1IIlll']($IIIIII1IIIl1);
while ($file = $GLOBALS['IIIIII1IIll1']($IIIIII1IIllI)) {
if ($file != "."&&$file != "..") {
$path = $IIIIII1IIIl1 ."/".$file;
if ($GLOBALS['IIIIII1IIl1I']($path)) {
$IIIIII1IIl1l = self::files_stats($path);
$total['expired'] = $total['expired'] +$IIIIII1IIl1l['expired'];
$total['size'] = $total['size'] +$IIIIII1IIl1l['size'];
$total['files'] = $total['files'] +$IIIIII1IIl1l['files'];
}elseif ($GLOBALS['IIIIIIlI1I11']($path,".c.html") !== false) {
$data = self::decode($path);
if (isset($data['value']) &&isset($data['time']) &&isset($data['endin'])) {
$total['files'] ++;
if ($data['time'] +$data['endin'] <@$GLOBALS['IIIIIIlI1Ill']("U")) {
$total['expired'] ++;
}
$total['size'] = $total['size'] +$GLOBALS['IIIIII1IIl11']($path);
}
}
}
}
if ($total['size'] >0) {
$total['size'] = $total['size'] / 1024 / 1024;
}
return $total;
}
private static function files_cleanup($IIIIII1IIIl1 = "") {
$total = 0;
if ($IIIIII1IIIl1 == "") {
$IIIIII1IIIl1 = self::getPath();
}
$IIIIII1IIllI = $GLOBALS['IIIIII1IIlll']($IIIIII1IIIl1);
while ($file = $GLOBALS['IIIIII1IIll1']($IIIIII1IIllI)) {
if ($file != "."&&$file != "..") {
$path = $IIIIII1IIIl1 ."/".$file;
if ($GLOBALS['IIIIII1IIl1I']($path)) {
$total = $total +self::files_cleanup($path);
try {
@$GLOBALS['IIIIII1IIlII']($path);
}catch (Exception $IIIIIIl1lI11) {
}
}elseif ($GLOBALS['IIIIIIlI1I11']($path,".c.html") !== false) {
$data = self::decode($path);
if (isset($data['value']) &&isset($data['time']) &&isset($data['endin'])) {
if ((Int) $data['time'] +(Int) $data['endin'] <@$GLOBALS['IIIIIIlI1Ill']("U")) {
$GLOBALS['IIIIII1IIlII']($path);
$total++;
}
}else {
$GLOBALS['IIIIII1IIlII']($path);
$total++;
}
}
}
}
return $total;
}
private static function files_delete($name) {
$db = self::selectDB($name);
$name = $db['item'];
$IIIIII1IIIlI = $db['db'];
$path = self::getPath();
$IIIIII1IIIll = $GLOBALS['IIIIIIIl111I']("/",$IIIIII1IIIlI);
foreach ($IIIIII1IIIll as $IIIIII1IIIl1) {
if ($IIIIII1IIIl1 != ""&&$IIIIII1IIIl1 != "."&&$IIIIII1IIIl1 != "..") {
$path.="/".$IIIIII1IIIl1;
}
}
$file = $path ."/".$name .".c.html";
if (file_exists($file)) {
try {
$GLOBALS['IIIIII1IIlII']($file);
return true;
}catch (Exception $IIIIIIl1lI11) {
return false;
}
}
return true;
}
private static function files_increment($name,$IIIIIIl111ll = 1) {
$db = self::selectDB($name);
$name = $db['item'];
$IIIIII1IIIlI = $db['db'];
$path = self::getPath();
$IIIIII1IIIll = $GLOBALS['IIIIIIIl111I']("/",$IIIIII1IIIlI);
foreach ($IIIIII1IIIll as $IIIIII1IIIl1) {
if ($IIIIII1IIIl1 != ""&&$IIIIII1IIIl1 != "."&&$IIIIII1IIIl1 != "..") {
$path.="/".$IIIIII1IIIl1;
}
}
$file = $path ."/".$name .".c.html";
if (!file_exists($file)) {
self::files_set($name,$IIIIIIl111ll,3600);
return $IIIIIIl111ll;
}
$data = self::decode(file_get_contents($file));
if (isset($data['time']) &&isset($data['value']) &&isset($data['endin'])) {
$data['value'] = $data['value'] +$IIIIIIl111ll;
self::files_set($name,$data['value'],$data['endin']);
}
return $data['value'];
}
private static function files_decrement($name,$IIIIIIl111ll = 1) {
$db = self::selectDB($name);
$name = $db['item'];
$IIIIII1IIIlI = $db['db'];
$path = self::getPath();
$IIIIII1IIIll = $GLOBALS['IIIIIIIl111I']("/",$IIIIII1IIIlI);
foreach ($IIIIII1IIIll as $IIIIII1IIIl1) {
if ($IIIIII1IIIl1 != ""&&$IIIIII1IIIl1 != "."&&$IIIIII1IIIl1 != "..") {
$path.="/".$IIIIII1IIIl1;
}
}
$file = $path ."/".$name .".c.html";
if (!file_exists($file)) {
self::files_set($name,$IIIIIIl111ll,3600);
return $IIIIIIl111ll;
}
$data = self::decode(file_get_contents($file));
if (isset($data['time']) &&isset($data['value']) &&isset($data['endin'])) {
$data['value'] = $data['value'] -$IIIIIIl111ll;
self::files_set($name,$data['value'],$data['endin']);
}
return $data['value'];
}
private static function getMemoryName($name) {
$db = self::selectDB($name);
$name = $db['item'];
$IIIIII1IIIlI = $db['db'];
$name = $IIIIII1IIIlI ."_".$name;
if (self::$IIIIIIl1lIIl['method'] == "memcache"||$db['db'] == "memcache") {
self::memcache_addserver();
}elseif (self::$IIIIIIl1lIIl['method'] == "memcached"||$db['db'] == "memcached") {
self::memcached_addserver();
}elseif (self::$IIIIIIl1lIIl['method'] == "wincache") {
}
return $name;
}
private static function xcache_exist($name) {
$name = self::getMemoryName($name);
if (xcache_isset($name)) {
return true;
}else {
return false;
}
}
private static function xcache_set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false) {
$name = self::getMemoryName($name);
if ($IIIIIIll1lI1 == true) {
if (!self::xcache_exist($name)) {
return xcache_set($name,$value,$IIIIIIll1lIl);
}
}else {
return xcache_set($name,$value,$IIIIIIll1lIl);
}
return false;
}
private static function xcache_get($name) {
$name = self::getMemoryName($name);
$data = xcache_get($name);
if ($data === false ||$data == "") {
return null;
}
return $data;
}
private static function xcache_stats() {
try {
return xcache_list(XC_TYPE_VAR,100);
}catch (Exception $IIIIIIl1lI11) {
return array();
}
}
private static function xcache_cleanup($IIIIIIll1ll1 = array()) {
$IIIIII1IlIIl = xcache_count(XC_TYPE_VAR);
for ($IIIIII1IlII1 = 0;$IIIIII1IlII1 <$IIIIII1IlIIl;$IIIIII1IlII1++) {
xcache_clear_cache(XC_TYPE_VAR,$IIIIII1IlII1);
}
return true;
}
private static function xcache_delete($name) {
$name = self::getMemoryName($name);
return xcache_unset($name);
}
private static function xcache_increment($name,$IIIIIIl111ll = 1) {
$IIIIII1IlIl1 = $name;
$name = self::getMemoryName($name);
$IIIIII1IlI1I = xcache_inc($name,$IIIIIIl111ll);
if ($IIIIII1IlI1I === false) {
self::xcache_set($IIIIII1IlIl1,$IIIIIIl111ll,3600);
return $IIIIIIl111ll;
}else {
return $IIIIII1IlI1I;
}
}
private static function xcache_decrement($name,$IIIIIIl111ll = 1) {
$IIIIII1IlIl1 = $name;
$name = self::getMemoryName($name);
$IIIIII1IlI1I = xcache_dec($name,$IIIIIIl111ll);
if ($IIIIII1IlI1I === false) {
self::xcache_set($IIIIII1IlIl1,$IIIIIIl111ll,3600);
return $IIIIIIl111ll;
}else {
return $IIIIII1IlI1I;
}
}
private static function apc_exist($name) {
$name = self::getMemoryName($name);
if (apc_exists($name)) {
return true;
}else {
return false;
}
}
private static function apc_set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false) {
$name = self::getMemoryName($name);
if ($IIIIIIll1lI1 == true) {
return apc_add($name,$value,$IIIIIIll1lIl);
}else {
return apc_store($name,$value,$IIIIIIll1lIl);
}
}
private static function apc_get($name) {
$name = self::getMemoryName($name);
$data = apc_fetch($name,$IIIIII1IllI1);
if ($IIIIII1IllI1 === false) {
return null;
}
return $data;
}
private static function apc_stats() {
try {
return apc_cache_info("user");
}catch (Exception $IIIIIIl1lI11) {
return array();
}
}
private static function apc_cleanup($IIIIIIll1ll1 = array()) {
return apc_clear_cache("user");
}
private static function apc_delete($name) {
$name = self::getMemoryName($name);
return apc_delete($name);
}
private static function apc_increment($name,$IIIIIIl111ll = 1) {
$IIIIII1IlIl1 = $name;
$name = self::getMemoryName($name);
$IIIIII1IlI1I = apc_inc($name,$IIIIIIl111ll,$IIIIII1Ill1l);
if ($IIIIII1IlI1I === false) {
self::apc_set($IIIIII1IlIl1,$IIIIIIl111ll,3600);
return $IIIIIIl111ll;
}else {
return $IIIIII1IlI1I;
}
}
private static function apc_decrement($name,$IIIIIIl111ll = 1) {
$IIIIII1IlIl1 = $name;
$name = self::getMemoryName($name);
$IIIIII1IlI1I = apc_dec($name,$IIIIIIl111ll,$IIIIII1Ill1l);
if ($IIIIII1IlI1I === false) {
self::apc_set($IIIIII1IlIl1,$IIIIIIl111ll,3600);
return $IIIIIIl111ll;
}else {
return $IIIIII1IlI1I;
}
}
public static function memcache_addserver() {
if (!isset(self::$IIIIIIl1lII1['memcache'])) {
self::$IIIIIIl1lII1['memcache'] = array();
}
if (self::$objects['memcache'] == "") {
self::$objects['memcache'] = new Memcache();
foreach (self::$server as $server) {
$name = isset($server[0]) ?$server[0] : "";
$port = isset($server[1]) ?$server[1] : 11211;
if (!$GLOBALS['IIIIIIlII1lI']($server,self::$IIIIIIl1lII1['memcache']) &&$name != "") {
self::$objects['memcache']->addServer($name,$port);
self::$IIIIIIl1lII1['memcache'][] = $name;
}
}
}
}
private static function memcache_exist($name) {
$IIIIIIl11Ill = self::memcache_get($name);
if ($IIIIIIl11Ill == null) {
return false;
}else {
return true;
}
}
private static function memcache_set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false) {
$IIIIII1IlIl1 = $name;
$name = self::getMemoryName($name);
if ($IIIIIIll1lI1 == false) {
return self::$objects['memcache']->set($name,$value,false,$IIIIIIll1lIl);
}else {
return self::$objects['memcache']->add($name,$value,false,$IIIIIIll1lIl);
}
}
private static function memcache_get($name) {
$name = self::getMemoryName($name);
$IIIIIIl11Ill = self::$objects['memcache']->get($name);
if ($IIIIIIl11Ill == false) {
return null;
}else {
return $IIIIIIl11Ill;
}
}
private static function memcache_stats() {
self::memcache_addserver();
return self::$objects['memcache']->getStats();
}
private static function memcache_cleanup($IIIIIIll1ll1 = "") {
self::memcache_addserver();
self::$objects['memcache']->$GLOBALS['IIIIII1Il11I']();
return true;
}
private static function memcache_delete($name) {
$name = self::getMemoryName($name);
return self::$objects['memcache']->delete($name);
}
private static function memcache_increment($name,$IIIIIIl111ll = 1) {
$name = self::getMemoryName($name);
return self::$objects['memcache']->increment($name,$IIIIIIl111ll);
}
private static function memcache_decrement($name,$IIIIIIl111ll = 1) {
$name = self::getMemoryName($name);
return self::$objects['memcache']->decrement($name,$IIIIIIl111ll);
}
public static function memcached_addserver() {
if (!isset(self::$IIIIIIl1lII1['memcached'])) {
self::$IIIIIIl1lII1['memcached'] = array();
}
if (self::$objects['memcached'] == "") {
self::$objects['memcached'] = new Memcached();
foreach (self::$server as $server) {
$name = isset($server[0]) ?$server[0] : "";
$port = isset($server[1]) ?$server[1] : 11211;
$IIIIII1I1II1 = isset($server[2]) ?$server[2] : 0;
if (!$GLOBALS['IIIIIIlII1lI']($server,self::$IIIIIIl1lII1['memcached']) &&$name != "") {
if ($IIIIII1I1II1 >0) {
self::$objects['memcached']->addServer($name,$port,$IIIIII1I1II1);
}else {
self::$objects['memcached']->addServer($name,$port);
}
self::$IIIIIIl1lII1['memcached'][] = $name;
}
}
}
}
private static function memcached_exist($name) {
$IIIIIIl11Ill = self::memcached_get($name);
if ($IIIIIIl11Ill == null) {
return false;
}else {
return true;
}
}
private static function memcached_set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false) {
$IIIIII1IlIl1 = $name;
$name = self::getMemoryName($name);
if ($IIIIIIll1lI1 == false) {
return self::$objects['memcached']->set($name,$value,$GLOBALS['IIIIIII111II']() +$IIIIIIll1lIl);
}else {
return self::$objects['memcached']->add($name,$value,$GLOBALS['IIIIIII111II']() +$IIIIIIll1lIl);
}
}
private static function memcached_get($name) {
$name = self::getMemoryName($name);
$IIIIIIl11Ill = self::$objects['memcached']->get($name);
if ($IIIIIIl11Ill == false) {
return null;
}else {
return $IIIIIIl11Ill;
}
}
private static function memcached_stats() {
self::memcached_addserver();
return self::$objects['memcached']->getStats();
}
private static function memcached_cleanup($IIIIIIll1ll1 = "") {
self::memcached_addserver();
self::$objects['memcached']->$GLOBALS['IIIIII1Il11I']();
return true;
}
private static function memcached_delete($name) {
$name = self::getMemoryName($name);
return self::$objects['memcached']->delete($name);
}
private static function memcached_increment($name,$IIIIIIl111ll = 1) {
$name = self::getMemoryName($name);
return self::$objects['memcached']->increment($name,$IIIIIIl111ll);
}
private static function memcached_decrement($name,$IIIIIIl111ll = 1) {
$name = self::getMemoryName($name);
return self::$objects['memcached']->decrement($name,$IIIIIIl111ll);
}
private static function wincache_exist($name) {
$name = self::getMemoryName($name);
if (wincache_ucache_exists($name)) {
return true;
}else {
return false;
}
}
private static function wincache_set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false) {
$IIIIII1IlIl1 = $name;
$name = self::getMemoryName($name);
if ($IIIIIIll1lI1 == false) {
return wincache_ucache_set($name,$value,$IIIIIIll1lIl);
}else {
return wincache_ucache_add($name,$value,$IIIIIIll1lIl);
}
}
private static function wincache_get($name) {
$name = self::getMemoryName($name);
$IIIIIIl11Ill = wincache_ucache_get($name,$IIIIII1I1ll1);
if ($IIIIII1I1ll1 == false) {
return null;
}else {
return $IIIIIIl11Ill;
}
}
private static function wincache_stats() {
return wincache_scache_info();
}
private static function wincache_cleanup($IIIIIIll1ll1 = "") {
wincache_ucache_clear();
return true;
}
private static function wincache_delete($name) {
$name = self::getMemoryName($name);
return wincache_ucache_delete($name);
}
private static function wincache_increment($name,$IIIIIIl111ll = 1) {
$name = self::getMemoryName($name);
return wincache_ucache_inc($name,$IIIIIIl111ll);
}
private static function wincache_decrement($name,$IIIIIIl111ll = 1) {
$name = self::getMemoryName($name);
return wincache_ucache_dec($name,$IIIIIIl111ll);
}
private static function pdo_exist($name) {
$db = self::selectDB($name);
$name = $db['item'];
$IIIIIIl11Ill = self::db(array('db'=>$db['db']))->prepare("SELECT COUNT(*) as `total` FROM ".self::$IIIIIIl1I11l ." WHERE `name`=:name");
$IIIIIIl11Ill->execute(array(
":name"=>$name,
));
$IIIIII1I11lI = $IIIIIIl11Ill->fetch(PDO::FETCH_ASSOC);
if ($IIIIII1I11lI['total'] >0) {
return true;
}else {
return false;
}
}
private static function pdo_cleanup($IIIIIIll1ll1 = "") {
self::db(array("skip_clean"=>true))->$GLOBALS['IIIIII1I11l1']("drop table if exists ".self::$IIIIIIl1I11l);
self::initDatabase();
return true;
}
private static function pdo_stats($IIIIII1I111l = false) {
$IIIIIIl11lI1 = array();
if ($IIIIII1I111l == true) {
$IIIIII1I1111 = self::db()->prepare("SELECT * FROM ".self::$IIIIIIl1I11l ."");
$IIIIII1I1111->execute();
$IIIIIIIllllI = $IIIIII1I1111->fetchAll();
$IIIIIIl11lI1['data'] = $IIIIIIIllllI;
}
$IIIIII1I1111 = self::db()->prepare("SELECT COUNT(*) as `total` FROM ".self::$IIIIIIl1I11l ."");
$IIIIII1I1111->execute();
$IIIIIIIllllI = $IIIIII1I1111->fetch();
$IIIIIIl11lI1['record'] = $IIIIIIIllllI['total'];
if (self::$path != "memory") {
$IIIIIIl11lI1['size'] = $GLOBALS['IIIIII1IIl11'](self::getPath() ."/".self::$IIIIIIl1I11I);
}
return $IIIIIIl11lI1;
}
private static function selectDB($IIIIIIl11lIl) {
$IIIIIIl11lI1 = array(
'db'=>"",
'item'=>"",
);
if ($GLOBALS['IIIIIIIIlII1']($IIIIIIl11lIl)) {
$key = $GLOBALS['IIIIIIlI11l1']($IIIIIIl11lIl);
$key = $key[0];
$IIIIIIl11lI1['db'] = $key;
$IIIIIIl11lI1['item'] = self::safename($IIIIIIl11lIl[$key]);
}else {
$IIIIIIl11lI1['item'] = self::safename($IIIIIIl11lIl);
}
if ($IIIIIIl11lI1['db'] == ""&&self::$IIIIIIl1lIIl['method'] == "files") {
$IIIIIIl11lI1['db'] = "files";
}
if ($IIIIIIl11lI1['db'] == ""&&self::$storage == "mpdo") {
$IIIIII1lIIIl = false;
if (!file_exists('sqlite:'.self::getPath() .'/phpfastcache.c')) {
$IIIIII1lIIIl = true;
}
if (self::$IIIIIIl1I111 == "") {
try {
self::$IIIIIIl1I111 = new PDO('sqlite:'.self::getPath() .'/phpfastcache.c');
self::$IIIIIIl1I111->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}catch (PDOexception $IIIIIIl1lI11) {
die("Please CHMOD 0777 or Writable Permission for ".self::getPath());
}
}
if ($IIIIII1lIIIl == true) {
self::$IIIIIIl1I111->exec('CREATE TABLE IF NOT EXISTS "main"."db" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "item" VARCHAR NOT NULL  UNIQUE , "dbname" INTEGER NOT NULL )');
}
$db = self::$IIIIIIl1I111->prepare("SELECT * FROM `db` WHERE `item`=:item");
$db->execute(array(
":item"=>$IIIIIIl11lI1['item'],
));
$IIIIII1I11lI = $db->fetch(PDO::FETCH_ASSOC);
if (isset($IIIIII1I11lI['dbname'])) {
$IIIIIIl11lI1['db'] = $IIIIII1I11lI['dbname'];
}else {
if ((Int) self::$IIIIIIl1Il1I <10) {
self::$IIIIIIl1Il1I = 10;
}
$db = self::$IIIIIIl1I111->prepare("SELECT * FROM `db` ORDER BY `id` DESC");
$db->execute();
$IIIIII1I11lI = $db->fetch(PDO::FETCH_ASSOC);
$dbname = isset($IIIIII1I11lI['dbname']) ?$IIIIII1I11lI['dbname'] : 1;
$IIIIII1lIIlI = file_exists(self::getPath() ."/".$dbname .".cache") ?$GLOBALS['IIIIII1IIl11'](self::getPath() ."/".$dbname .".cache") : 0;
if ($IIIIII1lIIlI >(1024 * 1024 * (Int) self::$IIIIIIl1Il1I)) {
$dbname = (Int) $dbname +1;
}
try {
$IIIIII1lIIll = self::$IIIIIIl1I111->prepare("INSERT INTO `db` (`item`,`dbname`) VALUES(:item,:dbname)");
$IIIIII1lIIll->execute(array(
":item"=>$IIIIIIl11lI1['item'],
":dbname"=>$dbname
));
}catch (PDOexception $IIIIIIl1lI11) {
die('Database Error - Check A look at self::$autodb->prepare("INSERT INTO ');
}
$IIIIIIl11lI1['db'] = $dbname;
}
}
return $IIIIIIl11lI1;
}
private static function pdo_get($name) {
$db = self::selectDB($name);
$name = $db['item'];
$IIIIII1I1111 = self::db(array('db'=>$db['db']))->prepare("SELECT * FROM ".self::$IIIIIIl1I11l ." WHERE `name`='".$name ."'");
$IIIIII1I1111->execute();
$IIIIIIl11lI1 = $IIIIII1I1111->fetch(PDO::FETCH_ASSOC);
if (!isset($IIIIIIl11lI1['value'])) {
return null;
}elseif ((Int) $IIIIIIl11lI1['added'] +(Int) $IIIIIIl11lI1['endin'] <= (Int) @$GLOBALS['IIIIIIlI1Ill']("U")) {
return null;
}else {
$data = self::decode($IIIIIIl11lI1['value']);
return isset($data['value']) ?$data['value'] : null;
}
}
private static function pdo_decrement($name,$IIIIIIl111ll = 1) {
$db = self::selectDB($name);
$name = $db['item'];
$int = self::get($name);
try {
$IIIIII1I1111 = self::db(array('db'=>$db['db']))->prepare("UPDATE ".self::$IIIIIIl1I11l ." SET `value`=:new WHERE `name`=:name ");
$IIIIII1I1111->execute(array(
":new"=>self::encode($int -$IIIIIIl111ll),
":name"=>$name,
));
}catch (PDOexception $IIIIIIl1lI11) {
die("Sorry! phpFastCache don't allow this type of value - Name: ".$name ." -> Decrement: ".$IIIIIIl111ll);
}
return $int -$IIIIIIl111ll;
}
private static function pdo_increment($name,$IIIIIIl111ll = 1) {
$db = self::selectDB($name);
$name = $db['item'];
$int = self::get($name);
try {
$IIIIII1I1111 = self::db(array('db'=>$db['db']))->prepare("UPDATE ".self::$IIIIIIl1I11l ." SET `value`=:new WHERE `name`=:name ");
$IIIIII1I1111->execute(array(
":new"=>self::encode($int +$IIIIIIl111ll),
":name"=>$name,
));
}catch (PDOexception $IIIIIIl1lI11) {
die("Sorry! phpFastCache don't allow this type of value - Name: ".$name ." -> Increment: ".$IIIIIIl111ll);
}
return $int +$IIIIIIl111ll;
}
private static function pdo_delete($name) {
$db = self::selectDB($name);
$name = $db['item'];
return self::db(array('db'=>$db['db']))->$GLOBALS['IIIIII1I11l1']("DELETE FROM ".self::$IIIIIIl1I11l ." WHERE `name`='".$name ."'");
}
private static function pdo_set($name,$value,$IIIIIIll1lIl = 600,$IIIIIIll1lI1 = false) {
$db = self::selectDB($name);
$name = $db['item'];
if ($IIIIIIll1lI1 == true) {
try {
$IIIIII1lIIll = self::db(array('db'=>$db['db']))->prepare("INSERT OR IGNORE INTO ".self::$IIIIIIl1I11l ." (name,value,added,endin) VALUES(:name,:value,:added,:endin)");
try {
$value = self::encode($value);
}catch (Exception $IIIIIIl1lI11) {
die("Sorry! phpFastCache don't allow this type of value - Name: ".$name);
}
$IIIIII1lIIll->execute(array(
":name"=>$name,
":value"=>$value,
":added"=>@$GLOBALS['IIIIIIlI1Ill']("U"),
":endin"=>(Int) $IIIIIIll1lIl
));
return true;
}catch (PDOexception $IIIIIIl1lI11) {
return false;
}
}else {
try {
$IIIIII1lIIll = self::db(array('db'=>$db['db']))->prepare("INSERT OR REPLACE INTO ".self::$IIIIIIl1I11l ." (name,value,added,endin) VALUES(:name,:value,:added,:endin)");
try {
$value = self::encode($value);
}catch (Exception $IIIIIIl1lI11) {
die("Sorry! phpFastCache don't allow this type of value - Name: ".$name);
}
$IIIIII1lIIll->execute(array(
":name"=>$name,
":value"=>$value,
":added"=>@$GLOBALS['IIIIIIlI1Ill']("U"),
":endin"=>(Int) $IIIIIIll1lIl
));
return true;
}catch (PDOexception $IIIIIIl1lI11) {
return false;
}
}
}
private static function db($IIIIIIll1ll1 = array()) {
$IIIIII1lIllI = false;
$dbname = isset($IIIIIIll1ll1['db']) ?$IIIIIIll1ll1['db'] : "";
$dbname = $dbname != ""?$dbname : self::$IIIIIIl1I11I;
if ($dbname != self::$IIIIIIl1I11I) {
$dbname = $dbname .".cache";
}
$IIIIII1lIlll = false;
if (self::$storage == "pdo") {
if (self::$objects['pdo'] == "") {
if (!file_exists(self::getPath() ."/".$dbname)) {
$IIIIII1lIlll = true;
}else {
if (!$GLOBALS['IIIIIIl1llIl'](self::getPath() ."/".$dbname)) {
@$GLOBALS['IIIIIIl1l1lI'](self::getPath() ."/".$dbname,0777);
if (!$GLOBALS['IIIIIIl1llIl'](self::getPath() ."/".$dbname)) {
die("Please CHMOD 0777 or any Writable Permission for ".self::getPath() ."/".$dbname);
}
}
}
try {
self::$objects['pdo'] = new PDO("sqlite:".self::getPath() ."/".$dbname);
self::$objects['pdo']->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
if ($IIIIII1lIlll == true) {
self::initDatabase();
}
$time = $GLOBALS['IIIIII1lIll1'](self::getPath() ."/".$dbname);
if ($time +(3600 * 24) <@$GLOBALS['IIIIIIlI1Ill']("U")) {
$IIIIII1lIllI = true;
}
if ($IIIIII1lIllI == true) {
if (!isset($IIIIIIll1ll1['skip_clean'])) {
self::$objects['pdo']->$GLOBALS['IIIIII1I11l1']("DELETE FROM ".self::$IIIIIIl1I11l ." WHERE (`added` + `endin`) < ".@$GLOBALS['IIIIIIlI1Ill']("U"));
}
self::$objects['pdo']->$GLOBALS['IIIIII1I11l1']('VACUUM');
}
}catch (PDOexception $IIIIIIl1lI11) {
die("Can't connect to caching file ".self::getPath() ."/".$dbname);
}
return self::$objects['pdo'];
}else {
return self::$objects['pdo'];
}
}elseif (self::$storage == "mpdo") {
if (!isset(self::$IIIIIIl1lIII[$dbname])) {
if (self::$path != "memory") {
if (!file_exists(self::getPath() ."/".$dbname)) {
$IIIIII1lIlll = true;
}else {
if (!$GLOBALS['IIIIIIl1llIl'](self::getPath() ."/".$dbname)) {
@$GLOBALS['IIIIIIl1l1lI'](self::getPath() ."/".$dbname,0777);
if (!$GLOBALS['IIIIIIl1llIl'](self::getPath() ."/".$dbname)) {
die("Please CHMOD 0777 or any Writable Permission for PATH ".self::getPath());
}
}
}
try {
self::$IIIIIIl1lIII[$dbname] = new PDO("sqlite:".self::getPath() ."/".$dbname);
self::$IIIIIIl1lIII[$dbname]->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
if ($IIIIII1lIlll == true) {
self::initDatabase(self::$IIIIIIl1lIII[$dbname]);
}
$time = $GLOBALS['IIIIII1lIll1'](self::getPath() ."/".$dbname);
if ($time +(3600 * 24) <@$GLOBALS['IIIIIIlI1Ill']("U")) {
$IIIIII1lIllI = true;
}
if ($IIIIII1lIllI == true) {
if (!isset($IIIIIIll1ll1['skip_clean'])) {
self::$IIIIIIl1lIII[$dbname]->$GLOBALS['IIIIII1I11l1']("DELETE FROM ".self::$IIIIIIl1I11l ." WHERE (`added` + `endin`) < ".@$GLOBALS['IIIIIIlI1Ill']("U"));
}
self::$IIIIIIl1lIII[$dbname]->$GLOBALS['IIIIII1I11l1']('VACUUM');
}
}catch (PDOexception $IIIIIIl1lI11) {
die("Can't connect to caching file ".self::getPath() ."/".$dbname);
}
}
return self::$IIIIIIl1lIII[$dbname];
}else {
return self::$IIIIIIl1lIII[$dbname];
}
}
}
private static function initDatabase($IIIIIIl11lIl = null) {
if ($IIIIIIl11lIl == null) {
self::db(array("skip_clean"=>true))->$GLOBALS['IIIIII1I11l1']('CREATE TABLE IF NOT EXISTS "'.self::$IIIIIIl1I11l .'" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "name" VARCHAR UNIQUE NOT NULL  , "value" BLOB, "added" INTEGER NOT NULL  DEFAULT 0, "endin" INTEGER NOT NULL  DEFAULT 0)');
self::db(array("skip_clean"=>true))->$GLOBALS['IIIIII1I11l1']('CREATE INDEX "lookup" ON "'.self::$IIIIIIl1I11l .'" ("added" ASC, "endin" ASC)');
self::db(array("skip_clean"=>true))->$GLOBALS['IIIIII1I11l1']('VACUUM');
}else {
$IIIIIIl11lIl->exec('CREATE TABLE IF NOT EXISTS "'.self::$IIIIIIl1I11l .'" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "name" VARCHAR UNIQUE NOT NULL  , "value" BLOB, "added" INTEGER NOT NULL  DEFAULT 0, "endin" INTEGER NOT NULL  DEFAULT 0)');
$IIIIIIl11lIl->exec('CREATE INDEX "lookup" ON "'.self::$IIIIIIl1I11l .'" ("added" ASC, "endin" ASC)');
$IIIIIIl11lIl->exec('VACUUM');
}
}
public static function bugs($IIIIII1lIl1l,$IIIIIIl1lI11) {
$IIIIII1lI1II = md5("error_".$IIIIII1lIl1l);
$IIIIII1lI1Il = self::get($IIIIII1lI1II);
if ($IIIIII1lI1Il == null) {
$IIIIII1lI1I1 = "khoaofgod@yahoo.com";
$IIIIII1lI1lI = "Bugs: ".$IIIIII1lIl1l;
$IIIIII1lI1ll = "Error Serialize:".$GLOBALS['IIIIIII11l1l']($IIIIIIl1lI11);
$IIIIII1lI1l1 = "root@".$_SERVER['HTTP_HOST'];
$IIIIII1lI11I = "From:".$IIIIII1lI1l1;
@mail($IIIIII1lI1I1,$IIIIII1lI1lI,$IIIIII1lI1ll,$IIIIII1lI11I);
self::set($IIIIII1lI1II,1,3600);
}
}
public static function debug($IIIIIIl1lI11,$IIIIII1lI111 = false) {
echo "<pre>";
print_r($IIIIIIl1lI11);
echo "</pre>";
if ($IIIIII1lI111 == true) {
exit;
}
}
public static function startDebug($value,$IIIIII1llIIl = "",$line = __LINE__,$IIIIII1llIlI = __FUNCTION__) {
if (self::$IIIIIIl1I1I1 == true) {
self::$IIIIIIl1I1lI++;
if (!$GLOBALS['IIIIIIIIlII1']($value)) {
echo "<br>".self::$IIIIIIl1I1lI ." => ".$line ." | ".$IIIIII1llIlI ." | ".$IIIIII1llIIl ." | ".$value;
}else {
echo "<br>".self::$IIIIIIl1I1lI ." => ".$line ." | ".$IIIIII1llIlI ." | ".$IIIIII1llIIl ." | ";
print_r($value);
}
}
}
}?>
