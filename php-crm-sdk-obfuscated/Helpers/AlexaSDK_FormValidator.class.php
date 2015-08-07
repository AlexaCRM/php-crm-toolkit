<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSWxsbDFsSSddPSdzdWJzdHInOyRHTE9CQUxTWydJSUlJSUlJMUlJSWwnXT0nYXJyYXlfa2V5X2V4aXN0cyc7JEdMT0JBTFNbJ0lJSUlJSWxJSTFsbCddPSdpbXBsb2RlJzskR0xPQkFMU1snSUlJSUlJbGwxMTFsJ109J2FycmF5X3NlYXJjaCc7')); ?><?php 
class AlexaSDK_FormValidator {
public static $IIIIIIll1ll1 = Array(
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
private $IIIIIIll1l1I,$IIIIIIll1l1l,$IIIIIIll1l11,$errors,$IIIIIIll11Il,$IIIIIIll11I1;
public function __construct($IIIIIIll1l1I = array(),$IIIIIIll1l11 = array(),$IIIIIIll1l1l = array()) {
$this->IIIIIIll1l1I = $IIIIIIll1l1I;
$this->IIIIIIll1l1l = $IIIIIIll1l1l;
$this->IIIIIIll1l11 = $IIIIIIll1l11;
$this->errors = array();
$this->IIIIIIll11Il = array();
}
public function validate($IIIIIIll11lI) {
$this->IIIIIIll11I1 = $IIIIIIll11lI;
$IIIIIIll11l1 = false;
foreach ($IIIIIIll11lI as $key =>$IIIIIIll111I) {
if ((strlen($IIIIIIll111I) == 0 ||$GLOBALS['IIIIIIll111l']($key,$this->IIIIIIll1l1I) === false) &&$GLOBALS['IIIIIIll111l']($key,$this->IIIIIIll1l11) === false) {
$this->IIIIIIll11Il[] = $key;
continue;
}
$IIIIIIIllllI = self::validateItem($IIIIIIll111I,$this->IIIIIIll1l1I[$key]);
if ($IIIIIIIllllI === false) {
$IIIIIIll11l1 = true;
$this->addError($key,$this->IIIIIIll1l1I[$key]);
}else {
$this->IIIIIIll11Il[] = $key;
}
}
return(!$IIIIIIll11l1);
}
public function getScript() {
if (!empty($this->errors)) {
$errors = array();
foreach ($this->errors as $key =>$IIIIIIll111I) {
$errors[] = "'INPUT[name={$key}]'";
}
$IIIIIIl1IIII = '$$('.$GLOBALS['IIIIIIlII1ll'](',',$errors) .').addClass("unvalidated");';
$IIIIIIl1IIII .= "alert('there are errors in the form');";
}
if (!empty($this->IIIIIIll11Il)) {
$IIIIIIll11Il = array();
foreach ($this->IIIIIIll11Il as $key) {
$IIIIIIll11Il[] = "'INPUT[name={$key}]'";
}
$IIIIIIl1IIII .= '$$('.$GLOBALS['IIIIIIlII1ll'](',',$IIIIIIll11Il) .').removeClass("unvalidated");';
}
$IIIIIIl1IIII = "<script type='text/javascript'>{$IIIIIIl1IIII} </script>";
return($IIIIIIl1IIII);
}
public function sanatize($IIIIIIll11lI) {
foreach ($IIIIIIll11lI as $key =>$IIIIIIll111I) {
if ($GLOBALS['IIIIIIll111l']($key,$this->IIIIIIll1l1l) === false &&!$GLOBALS['IIIIIII1IIIl']($key,$this->IIIIIIll1l1l))
continue;
$IIIIIIll11lI[$key] = self::sanatizeItem($IIIIIIll111I,$this->IIIIIIll1l1I[$key]);
}
return($IIIIIIll11lI);
}
private function addError($IIIIIIl1IIlI,$type = 'string') {
$this->errors[$IIIIIIl1IIlI] = $type;
}
public static function sanatizeItem($IIIIIIl1II1I,$type) {
$IIIIIIl1II1l = NULL;
switch ($type) {
case 'url':
$IIIIIIl1II11 = FILTER_SANITIZE_URL;
break;
case 'int':
$IIIIIIl1II11 = FILTER_SANITIZE_NUMBER_INT;
break;
case 'float':
$IIIIIIl1II11 = FILTER_SANITIZE_NUMBER_FLOAT;
$IIIIIIl1II1l = FILTER_FLAG_ALLOW_FRACTION |FILTER_FLAG_ALLOW_THOUSAND;
break;
case 'email':
$IIIIIIl1II1I = $GLOBALS['IIIIIIlll1lI']($IIIIIIl1II1I,0,254);
$IIIIIIl1II11 = FILTER_SANITIZE_EMAIL;
break;
case 'string':
default:
$IIIIIIl1II11 = FILTER_SANITIZE_STRING;
$IIIIIIl1II1l = FILTER_FLAG_NO_ENCODE_QUOTES;
break;
}
$IIIIIIl1IIII = filter_var($IIIIIIl1II1I,$IIIIIIl1II11,$IIIIIIl1II1l);
return($IIIIIIl1IIII);
}
public static function validateItem($IIIIIIl1II1I,$type) {
if ($GLOBALS['IIIIIII1IIIl']($type,self::$IIIIIIll1ll1)) {
$IIIIIIl1IlIl = filter_var($IIIIIIl1II1I,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>'!'.self::$IIIIIIll1ll1[$type] .'!i'))) !== false;
return($IIIIIIl1IlIl);
}
$IIIIIIl1II11 = false;
switch ($type) {
case 'email':
$IIIIIIl1II1I = $GLOBALS['IIIIIIlll1lI']($IIIIIIl1II1I,0,254);
$IIIIIIl1II11 = FILTER_VALIDATE_EMAIL;
break;
case 'int':
$IIIIIIl1II11 = FILTER_VALIDATE_INT;
break;
case 'boolean':
$IIIIIIl1II11 = FILTER_VALIDATE_BOOLEAN;
break;
case 'ip':
$IIIIIIl1II11 = FILTER_VALIDATE_IP;
break;
case 'url':
$IIIIIIl1II11 = FILTER_VALIDATE_URL;
break;
}
return ($IIIIIIl1II11 === false) ?false : filter_var($IIIIIIl1II1I,$IIIIIIl1II11) !== false ?true : false;
}
}
?>
