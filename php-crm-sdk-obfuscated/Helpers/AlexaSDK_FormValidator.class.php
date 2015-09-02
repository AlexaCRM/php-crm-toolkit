<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSWxsbDFsbCddPSdzdWJzdHInOyRHTE9CQUxTWydJSUlJSUlJMUlJSWwnXT0nYXJyYXlfa2V5X2V4aXN0cyc7JEdMT0JBTFNbJ0lJSUlJSWxJSTFsbCddPSdpbXBsb2RlJzskR0xPQkFMU1snSUlJSUlJbGwxMTExJ109J2FycmF5X3NlYXJjaCc7')); ?><?php 
class AlexaSDK_FormValidator {
public static $IIIIIIll1l1I = Array(
'date'=>"[0-9]{1,2}\/[0-9]{1,2}\/[0-9][0-9]",
'amount'=>"^[-]?[0-9]+\$",
'number'=>"^[-]?[0-9,]+\$",
'alfanum'=>"^[0-9a-zA-Z ,.-_\\s\?\!]+\$",
'not_empty'=>"[a-z0-9A-Z]+",
'words'=>"^[A-Za-z]+[A-Za-z \\s]*\$",
'phone'=>"^[0-9]{10,11}\$",
'zipcode'=>"^[1-9][0-9]{3}[a-zA-Z]{2}\$",
'plate'=>"^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}\$",
'price'=>"^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?\$",
'float'=>"/^-?(?:\d+|\d*\.\d+)$/",
'timezone'=>"/^(Z|[+-](?:2[0-3]|[01]?[0-9])(?::?(?:[0-5]?[0-9]))?)$/",
'2digitopt'=>"^\d+(\,\d{2})?\$",
'2digitforce'=>"^\d+\,\d\d\$",
'anything'=>"^[\d\D]{1,}\$"
);
private $IIIIIIll1l1l,$IIIIIIll1l11,$IIIIIIll11II,$errors,$IIIIIIll11I1,$IIIIIIll11lI;
public function __construct($IIIIIIll1l1l = array(),$IIIIIIll11II = array(),$IIIIIIll1l11 = array()) {
$this->IIIIIIll1l1l = $IIIIIIll1l1l;
$this->IIIIIIll1l11 = $IIIIIIll1l11;
$this->IIIIIIll11II = $IIIIIIll11II;
$this->errors = array();
$this->IIIIIIll11I1 = array();
}
public function validate($IIIIIIll11ll) {
$this->IIIIIIll11lI = $IIIIIIll11ll;
$IIIIIIll111I = false;
foreach ($IIIIIIll11ll as $key =>$IIIIIIll111l) {
if ((strlen($IIIIIIll111l) == 0 ||$GLOBALS['IIIIIIll1111']($key,$this->IIIIIIll1l1l) === false) &&$GLOBALS['IIIIIIll1111']($key,$this->IIIIIIll11II) === false) {
$this->IIIIIIll11I1[] = $key;
continue;
}
$IIIIIIIllllI = self::validateItem($IIIIIIll111l,$this->IIIIIIll1l1l[$key]);
if ($IIIIIIIllllI === false) {
$IIIIIIll111I = true;
$this->addError($key,$this->IIIIIIll1l1l[$key]);
}else {
$this->IIIIIIll11I1[] = $key;
}
}
return(!$IIIIIIll111I);
}
public function getScript() {
if (!empty($this->errors)) {
$errors = array();
foreach ($this->errors as $key =>$IIIIIIll111l) {
$errors[] = "'INPUT[name={$key}]'";
}
$IIIIIIl1IIIl = '$$('.$GLOBALS['IIIIIIlII1ll'](',',$errors) .').addClass("unvalidated");';
$IIIIIIl1IIIl .= "alert('there are errors in the form');";
}
if (!empty($this->IIIIIIll11I1)) {
$IIIIIIll11I1 = array();
foreach ($this->IIIIIIll11I1 as $key) {
$IIIIIIll11I1[] = "'INPUT[name={$key}]'";
}
$IIIIIIl1IIIl .= '$$('.$GLOBALS['IIIIIIlII1ll'](',',$IIIIIIll11I1) .').removeClass("unvalidated");';
}
$IIIIIIl1IIIl = "<script type='text/javascript'>{$IIIIIIl1IIIl} </script>";
return($IIIIIIl1IIIl);
}
public function sanatize($IIIIIIll11ll) {
foreach ($IIIIIIll11ll as $key =>$IIIIIIll111l) {
if ($GLOBALS['IIIIIIll1111']($key,$this->IIIIIIll1l11) === false &&!$GLOBALS['IIIIIII1IIIl']($key,$this->IIIIIIll1l11))
continue;
$IIIIIIll11ll[$key] = self::sanatizeItem($IIIIIIll111l,$this->IIIIIIll1l1l[$key]);
}
return($IIIIIIll11ll);
}
private function addError($IIIIIIl1IIll,$type = 'string') {
$this->errors[$IIIIIIl1IIll] = $type;
}
public static function sanatizeItem($IIIIIIl1II1l,$type) {
$IIIIIIl1II11 = NULL;
switch ($type) {
case 'url':
$IIIIIIl1IlII = FILTER_SANITIZE_URL;
break;
case 'int':
$IIIIIIl1IlII = FILTER_SANITIZE_NUMBER_INT;
break;
case 'float':
$IIIIIIl1IlII = FILTER_SANITIZE_NUMBER_FLOAT;
$IIIIIIl1II11 = FILTER_FLAG_ALLOW_FRACTION |FILTER_FLAG_ALLOW_THOUSAND;
break;
case 'email':
$IIIIIIl1II1l = $GLOBALS['IIIIIIlll1ll']($IIIIIIl1II1l,0,254);
$IIIIIIl1IlII = FILTER_SANITIZE_EMAIL;
break;
case 'string':
default:
$IIIIIIl1IlII = FILTER_SANITIZE_STRING;
$IIIIIIl1II11 = FILTER_FLAG_NO_ENCODE_QUOTES;
break;
}
$IIIIIIl1IIIl = filter_var($IIIIIIl1II1l,$IIIIIIl1IlII,$IIIIIIl1II11);
return($IIIIIIl1IIIl);
}
public static function validateItem($IIIIIIl1II1l,$type) {
if ($GLOBALS['IIIIIII1IIIl']($type,self::$IIIIIIll1l1I)) {
$IIIIIIl1IlI1 = filter_var($IIIIIIl1II1l,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>'!'.self::$IIIIIIll1l1I[$type] .'!i'))) !== false;
return($IIIIIIl1IlI1);
}
$IIIIIIl1IlII = false;
switch ($type) {
case 'email':
$IIIIIIl1II1l = $GLOBALS['IIIIIIlll1ll']($IIIIIIl1II1l,0,254);
$IIIIIIl1IlII = FILTER_VALIDATE_EMAIL;
break;
case 'int':
$IIIIIIl1IlII = FILTER_VALIDATE_INT;
break;
case 'boolean':
$IIIIIIl1IlII = FILTER_VALIDATE_BOOLEAN;
break;
case 'ip':
$IIIIIIl1IlII = FILTER_VALIDATE_IP;
break;
case 'url':
$IIIIIIl1IlII = FILTER_VALIDATE_URL;
break;
}
return ($IIIIIIl1IlII === false) ?false : filter_var($IIIIIIl1II1l,$IIIIIIl1IlII) !== false ?true : false;
}
}
?>
