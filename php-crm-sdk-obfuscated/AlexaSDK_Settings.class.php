<?php 
class AlexaSDK_Settings{
public $IIIIIIllIIll;
public $IIIIIIllIIl1;
public $IIIIIIllII1I;
public $IIIIIIllII1l;
public $IIIIIIllII11;
public $port;
public $IIIIIIllIlIl;
public $IIIIIIllIlI1;
public $IIIIIIIIIIl1;
public $IIIIIIIIIIll;
public $IIIIIIllIllI;
public $IIIIIIllIlll;
public $IIIIIIllIll1;
public $IIIIIIIIIII1;
public $IIIIIIllIl1I;
function __construct($IIIIIIIIIIl1 = null,$IIIIIIllIIl1 = null,$IIIIIIllII1I = null,$IIIIIIIIIIll = null,$IIIIIIllIlll = null,$IIIIIIllII1l = null,$IIIIIIllIIll = null,$IIIIIIllIl1l = null ){
if ($IIIIIIIIIIl1 != null &&$IIIIIIIIIIl1 != ''){
$this->IIIIIIIIIIl1 = $IIIIIIIIIIl1;
}
if ($IIIIIIllIIl1 != null &&$IIIIIIllIIl1 != ''){
$this->IIIIIIllIIl1 = $IIIIIIllIIl1;
}
if ($IIIIIIllII1I != null &&$IIIIIIllII1I != ''){
$this->IIIIIIllII1I = $IIIIIIllII1I;
}
if ($IIIIIIIIIIll != null &&$IIIIIIIIIIll != ''){
$this->IIIIIIIIIIll = $IIIIIIIIIIll;
}
if ($IIIIIIllIlll != null &&$IIIIIIllIlll != ''){
$this->IIIIIIllIlll = $IIIIIIllIlll;
}
if ($IIIIIIllII1l != null &&$IIIIIIllII1l != ''){
$this->IIIIIIllII1l = $IIIIIIllII1l;
}
if ($IIIIIIllIIll != null &&$IIIIIIllIIll != ''){
$this->IIIIIIllIIll = $IIIIIIllIIll;
}
if ($IIIIIIllIl1l != null &&$IIIIIIllIl1l != ''){
$this->IIIIIIllIll1 = $IIIIIIllIl1l;
}
}
public function isFullSettings(){
return ($this->IIIIIIIIIIl1 &&$this->IIIIIIllIIl1 &&$this->IIIIIIllII1I &&$this->IIIIIIIIIIll &&$this->IIIIIIllIlll &&$this->IIIIIIIIIIlI &&(($this->IIIIIIllIIll == "OnlineFederation") ?$this->IIIIIIllIll1 : true));
}
}
?>
