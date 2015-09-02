<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSWxJSUlJbCddPSd2YXJfZHVtcCc7JEdMT0JBTFNbJ0lJSUlJSUkxSUlJbCddPSdhcnJheV9rZXlfZXhpc3RzJzskR0xPQkFMU1snSUlJSUlJbGxsMUlsJ109J3ByZWdfbWF0Y2gnOyRHTE9CQUxTWydJSUlJSUlsSUkxbGwnXT0naW1wbG9kZSc7JEdMT0JBTFNbJ0lJSUlJSUlsMUlsMSddPSdzdHJ0b2xvd2VyJzskR0xPQkFMU1snSUlJSUlJSWwxMTFJJ109J2V4cGxvZGUnOyRHTE9CQUxTWydJSUlJSUlsSUkxSUknXT0nc3RydG91cHBlcic7JEdMT0JBTFNbJ0lJSUlJSWxJSTFJMSddPSdzdHJ0b3RpbWUnOyRHTE9CQUxTWydJSUlJSUlsSWxsMUknXT0nZ21kYXRlJzskR0xPQkFMU1snSUlJSUlJbGxsMWxsJ109J3N1YnN0cic7JEdMT0JBTFNbJ0lJSUlJSWwxMUlJbCddPSdwcmVnX3JlcGxhY2UnOyRHTE9CQUxTWydJSUlJSUlJSWxJbGwnXT0nZGlybmFtZSc7JEdMT0JBTFNbJ0lJSUlJSWxJMUkxMSddPSdzdHJwb3MnOw==')); ?><?php 
interface AlexaSDK_Interface {
const EmptyGUID = '00000000-0000-0000-0000-000000000000';
const MAX_CRM_RECORDS = 5000;
}
abstract class AlexaSDK_Abstract implements AlexaSDK_Interface {
protected static $IIIIIIIIlI1l = FALSE;
protected static $IIIIIII111I1 = 240;
protected static $IIIIIII111lI = "AlexaSDK";
public static $IIIIIII111ll = Array(
'http://www.w3.org/2005/08/addressing/soap/fault',
'http://schemas.microsoft.com/net/2005/12/windowscommunicationfoundation/dispatcher/fault',
'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/ExecuteOrganizationServiceFaultFault',
'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/CreateOrganizationServiceFaultFault',
'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/RetrieveOrganizationServiceFaultFault',
'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/UpdateOrganizationServiceFaultFault',
'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/DeleteOrganizationServiceFaultFault',
'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/RetrieveMultipleOrganizationServiceFaultFault',
);
public static function loadClass($IIIIIII111l1){
if ((class_exists($IIIIIII111l1)) ||($GLOBALS['IIIIIIlI1I11']($IIIIIII111l1,self::$IIIIIII111lI) === false)) {
return false;
}
$classFilePath = $GLOBALS['IIIIIIIIlIll'](__FILE__) .DIRECTORY_SEPARATOR .$IIIIIII111l1 .'.class.php';
if ((file_exists($classFilePath) === false) ||(is_readable($classFilePath) === false)) {
return false;
}
require_once $classFilePath;
}
protected static function stripNS($IIIIIII1III1) {
return $GLOBALS['IIIIIIl11IIl']('/[a-zA-Z]+:([a-zA-Z]+)/','$1',$IIIIIII1III1);
}
protected static function getCurrentTime() {
return $GLOBALS['IIIIIIlll1ll']($GLOBALS['IIIIIIlIll1I']('c'),0,-6) .".00";
}
protected static function getExpiryTime() {
return $GLOBALS['IIIIIIlll1ll']($GLOBALS['IIIIIIlIll1I']('c',$GLOBALS['IIIIIIlII1I1']('+5 minutes')),0,-6) .".00";
}
protected static function getUuid($namespace = '') {
static $guid = '';
$uid = uniqid("",true);
$data = $namespace;
$data .= $_SERVER['REQUEST_TIME'];
$data .= $_SERVER['HTTP_USER_AGENT'];
$data .= $_SERVER['REMOTE_ADDR'];
$data .= $_SERVER['REMOTE_PORT'];
$hash = $GLOBALS['IIIIIIlII1II'](hash('ripemd128',$uid .$guid .md5($data)));
$guid = $GLOBALS['IIIIIIlll1ll']($hash,0,8) .
'-'.
$GLOBALS['IIIIIIlll1ll']($hash,8,4) .
'-'.
$GLOBALS['IIIIIIlll1ll']($hash,12,4) .
'-'.
$GLOBALS['IIIIIIlll1ll']($hash,16,4) .
'-'.
$GLOBALS['IIIIIIlll1ll']($hash,20,12);
return $guid;
}
public static function setDebug($_debugMode) {
self::$IIIIIIIIlI1l = $_debugMode;
}
public static function setTimeLimit($_timeLimit) {
self::$IIIIIII111I1 = $_timeLimit;
}
public static function getClassName($IIIIIIIl111l) {
$capitalisedEntityName = self::capitaliseEntityName($IIIIIIIl111l);
$IIIIIII111l1 = 'AlexaSDK_'.$capitalisedEntityName."_class";
return $IIIIIII111l1;
}
private static function capitaliseEntityName($IIIIIIIl111l) {
$words = $GLOBALS['IIIIIIIl111I']('_',$IIIIIIIl111l);
foreach($words as $key =>$word) $words[$key] = ucwords($GLOBALS['IIIIIIIl1Il1']($word));
$capitalisedEntityName = $GLOBALS['IIIIIIlII1ll']('_',$words);
return $capitalisedEntityName;
}
protected static function parseTime($timestamp,$formatString) {
if(function_exists("strptime") == true) {
$time_array = strptime($timestamp,$formatString);
}else {
$masks = Array(
'%d'=>'(?P<d>[0-9]{2})',
'%m'=>'(?P<m>[0-9]{2})',
'%Y'=>'(?P<Y>[0-9]{4})',
'%H'=>'(?P<H>[0-9]{2})',
'%M'=>'(?P<M>[0-9]{2})',
'%S'=>'(?P<S>[0-9]{2})',
);
$rexep = "#".strtr(preg_quote($formatString),$masks)."#";
if(!$GLOBALS['IIIIIIlll1Il']($rexep,$timestamp,$out)) return false;
$time_array = Array(
"tm_sec"=>(int) $out['S'],
"tm_min"=>(int) $out['M'],
"tm_hour"=>(int) $out['H'],
"tm_mday"=>(int) $out['d'],
"tm_mon"=>$out['m']?$out['m']-1:0,
"tm_year"=>$out['Y'] >1900 ?$out['Y'] -1900 : 0,
);
}
$phpTimestamp = gmmktime($time_array['tm_hour'],$time_array['tm_min'],$time_array['tm_sec'],
$time_array['tm_mon']+1,$time_array['tm_mday'],1900+$time_array['tm_year']);
return $phpTimestamp;
}
protected static function addFormattedValues(Array &$IIIIIIIl11II,DOMNodeList $IIIIIIIl1l1I,Array $IIIIIIIl11Il = NULL,$IIIIIIIl11I1 = NULL) {
foreach ($IIIIIIIl1l1I as $IIIIIIIl11lI) {
$IIIIIIIl11ll = $IIIIIIIl11lI->getElementsByTagName('key')->item(0)->textContent;
$IIIIIII1III1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->textContent;
if ($IIIIIIIl11Il == NULL) {
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll,$IIIIIIIl11II)) {
$IIIIIIIl11II[$IIIIIIIl11ll] = (Object)Array(
'Value'=>$IIIIIIIl11II[$IIIIIIIl11ll],
'FormattedValue'=>$IIIIIII1III1
);
}else {
$IIIIIIIl11II[$IIIIIIIl11ll] = $IIIIIII1III1;
}
}else {
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll,$IIIIIIIl11II)) {
if (isset($IIIIIIIl11II[$IIIIIIIl11ll]->$IIIIIIIl11I1)) {
$IIIIIIIl11II[$IIIIIIIl11ll]->$IIIIIIIl11I1 = (Object)Array(
'Value'=>$IIIIIIIl11II[$IIIIIIIl11ll]->$IIIIIIIl11I1,
'FormattedValue'=>$IIIIIII1III1);
}else {
$IIIIIIIl11II[$IIIIIIIl11ll]->$IIIIIIIl11I1 = $IIIIIII1III1;
}
}else {
$IIIIIII1IIlI = (Object)Array();
foreach ($IIIIIIIl11Il as $IIIIIII1IIll) {
$IIIIIII1IIlI->$IIIIIII1IIll = NULL;
}
$IIIIIII1IIlI->$IIIIIIIl11I1 = $IIIIIII1III1;
$IIIIIIIl11II[$IIIIIIIl11ll] = $IIIIIII1IIlI;
}
}
}
}
protected static function vardump($IIIIIII11111){
echo "<pre>";
$GLOBALS['IIIIIIlIIIIl']($IIIIIII11111);
echo "</pre>";
}
}
spl_autoload_register(Array('AlexaSDK_Abstract','loadClass'));
?>
