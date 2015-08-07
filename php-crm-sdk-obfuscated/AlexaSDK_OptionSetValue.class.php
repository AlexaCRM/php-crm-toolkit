<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSUlsMUlsMSddPSdzdHJ0b2xvd2VyJzs=')); ?><?php 
class AlexaSDK_OptionSetValue extends AlexaSDK_Abstract {
protected $value = NULL;
protected $label = NULL;
public function __construct($IIIIIIllIII1,$IIIIIIllIIlI) {
$this->value = $IIIIIIllIII1;
$this->label = $IIIIIIllIIlI;
}
public function __get($IIIIIII1lIll) {
switch ($GLOBALS['IIIIIIIl1Il1']($IIIIIII1lIll)) {
case 'value':
return $this->value;
break;
case 'label':
return $this->label;
}
$IIIIIIlII1Il = debug_backtrace();
trigger_error('Undefined property via __get(): '.$IIIIIII1lIll
.' in '.$IIIIIIlII1Il[0]['file'] .' on line '.$IIIIIIlII1Il[0]['line'],
E_USER_NOTICE);
return NULL;
}
public function __toString() {
return (string)$this->label;
}
}?>
