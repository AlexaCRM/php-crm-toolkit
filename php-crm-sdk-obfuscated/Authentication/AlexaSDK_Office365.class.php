<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSWxJMUlsbCddPSdkYXRlJzskR0xPQkFMU1snSUlJSUlJbGxsMWxJJ109J3N1YnN0cic7JEdMT0JBTFNbJ0lJSUlJSWxsbDFJSSddPSdwcmVnX21hdGNoJzskR0xPQkFMU1snSUlJSUlJSTExMUlJJ109J3RpbWUnOw==')); ?><?php 
class AlexaSDK_Office365 extends AlexaSDK{
public $IIIIIIIIIIIl;
private $IIIIIIIIIl1I;
private $IIIIIIllI1II;
private $IIIIIIlIll11;
function __construct($IIIIIIIIlIII,$IIIIIIllI1Il){
$this->IIIIIIIIIIIl = $IIIIIIIIlIII;
$this->IIIIIIlIll11 = $IIIIIIllI1Il;
}
public function getOrganizationSecurityToken() {
if ($this->IIIIIIIIIl1I != NULL) {
if ($this->IIIIIIIIIl1I['expiryTime'] >$GLOBALS['IIIIIII111II']()) {
return $this->IIIIIIIIIl1I;
}
}else{
$IIIIIIlIIIll = $this->IIIIIIlIll11->getCachedSecurityToken("organization",$this->IIIIIIIIIl1I);
if ($IIIIIIlIIIll &&$this->IIIIIIIIIl1I['expiryTime'] >$GLOBALS['IIIIIII111II']()){
return $this->IIIIIIIIIl1I;
}
}
$this->IIIIIIIIIl1I = $this->requestSecurityToken($this->IIIIIIIIIIIl->IIIIIIllIlll,$this->IIIIIIIIIIIl->IIIIIIllIll1,$this->IIIIIIIIIIIl->IIIIIIllIIl1,$this->IIIIIIIIIIIl->IIIIIIllII1I);
$this->IIIIIIlIll11->setCachedSecurityToken('organization',$this->IIIIIIIIIl1I);
return $this->IIIIIIIIIl1I;
}
public function getDiscoverySecurityToken(){
if ($this->IIIIIIllI1II != NULL) {
if ($this->IIIIIIllI1II['expiryTime'] >$GLOBALS['IIIIIII111II']()) {
return $this->IIIIIIllI1II;
}
}else{
$IIIIIIlIIIll = $this->IIIIIIlIll11->getCachedSecurityToken("discovery",$this->IIIIIIllI1II);
if ($IIIIIIlIIIll &&$this->IIIIIIllI1II['expiryTime'] >$GLOBALS['IIIIIII111II']()){
return $this->IIIIIIllI1II;
}
}
$this->IIIIIIllI1II = $this->requestSecurityToken($this->IIIIIIIIIIIl->IIIIIIllIlll,$this->IIIIIIIIIIIl->IIIIIIllIll1,$this->IIIIIIIIIIIl->IIIIIIllIIl1,$this->IIIIIIIIIIIl->IIIIIIllII1I);
$this->IIIIIIlIll11->setCachedSecurityToken('discovery',$this->IIIIIIllI1II);
return $this->IIIIIIllI1II;
}
protected function getSecurityHeaderNode(Array $securityToken) {
$IIIIIIllI1l1 = new DOMDocument();
$IIIIIIllI11I = $IIIIIIllI1l1->appendChild($IIIIIIllI1l1->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd','o:Security'));
$IIIIIIllI11I->setAttribute('s:mustUnderstand','1');
$IIIIIIllI11l = $IIIIIIllI11I->appendChild($IIIIIIllI1l1->createElement('u:Timestamp'));
$IIIIIIllI11l->setAttribute('u:Id','_0');
$IIIIIIllI11l->appendChild($IIIIIIllI1l1->createElement('u:Created',self::getCurrentTime().'Z'));
$IIIIIIllI11l->appendChild($IIIIIIllI1l1->createElement('u:Expires',self::getExpiryTime().'Z'));
$IIIIIIllI111 = $IIIIIIllI1l1->createDocumentFragment();
$IIIIIIllI111->appendXML($securityToken['securityToken']);
$IIIIIIllI11I->appendChild($IIIIIIllI111);
return $IIIIIIllI11I;
}
protected function requestSecurityToken($IIIIIIlllI1l,$IIIIIIlllI11,$IIIIIIllllII,$IIIIIIllllIl) {
$IIIIIIllllI1 = self::getLoginXML($IIIIIIlllI1l,$IIIIIIlllI11,$IIIIIIllllII,$IIIIIIllllIl);
$IIIIIIlllllI = self::getSoapResponse($IIIIIIlllI1l,$IIIIIIllllI1);
$IIIIIIllI1l1 = new DOMDocument();
$IIIIIIllI1l1->loadXML($IIIIIIlllllI);
$IIIIIIllllll = $IIIIIIllI1l1->getElementsbyTagName("CipherValue");
$securityToken0 =  $IIIIIIllllll->item(0)->textContent;
$securityToken1 =  $IIIIIIllllll->item(1)->textContent;
$keyIdentifier = $IIIIIIllI1l1->getElementsbyTagName("KeyIdentifier")->item(0)->textContent;
$binarySecret = $IIIIIIllI1l1->getElementsbyTagName("BinarySecret")->item(0)->textContent;
$IIIIIIllI1l1->getElementsByTagName('SecurityTokenReference')->item(0)->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:wsse','http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
$IIIIIIllI111 = $IIIIIIllI1l1->saveXML($IIIIIIllI1l1->getElementsByTagName("RequestedSecurityToken")->item(0));
$GLOBALS['IIIIIIlll1II']('/<wst:RequestedSecurityToken>(.*)<\/wst:RequestedSecurityToken>/',$IIIIIIllI111,$IIIIIIlll1Il);
$IIIIIIllI111 = $IIIIIIlll1Il[1];
$expiryTime = $IIIIIIllI1l1->getElementsByTagName("RequestSecurityTokenResponse")->item(0)->getElementsByTagName('Expires')->item(0)->textContent;
$expiryTime = self::parseTime($GLOBALS['IIIIIIlll1lI']($expiryTime,0,-5),'%Y-%m-%dT%H:%M:%S');
$securityToken = Array(
'securityToken'=>$IIIIIIllI111,
'securityToken0'=>$securityToken0,
'securityToken1'=>$securityToken1,
'binarySecret'=>$binarySecret,
'keyIdentifier'=>$keyIdentifier,
'expiryTime'=>$expiryTime
);
if (self::$IIIIIIIIlI1l) {
echo 'Got Security Token - Expires at: '.$GLOBALS['IIIIIIlI1Ill']('r',$securityToken['expiryTime']).PHP_EOL;
echo "\tKey Identifier\t: ".$securityToken['keyIdentifier'].PHP_EOL;
echo "\tSecurity Token 0\t: ".$GLOBALS['IIIIIIlll1lI']($securityToken['securityToken0'],0,25).'...'.$GLOBALS['IIIIIIlll1lI']($securityToken['securityToken0'],-25).' ('.strlen($securityToken['securityToken0']).')'.PHP_EOL;
echo "\tSecurity Token 1\t: ".$GLOBALS['IIIIIIlll1lI']($securityToken['securityToken1'],0,25).'...'.$GLOBALS['IIIIIIlll1lI']($securityToken['securityToken1'],-25).' ('.strlen($securityToken['securityToken1']).')'.PHP_EOL;
echo "\tBinary Secret\t: ".$securityToken['binarySecret'].PHP_EOL.PHP_EOL;
}
return $securityToken;
}
protected static function getLoginXML($IIIIIIlllI1l,$IIIIIIlllI11,$IIIIIIllllII,$IIIIIIllllIl) {
$IIIIIIllllI1 = new DOMDocument();
$IIIIIIlll1l1 = $IIIIIIllllI1->appendChild($IIIIIIllllI1->createElementNS('http://www.w3.org/2003/05/soap-envelope','s:Envelope'));
$IIIIIIlll1l1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:a','http://www.w3.org/2005/08/addressing');
$IIIIIIlll1l1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:u','http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
$IIIIIIlll11I = $IIIIIIlll1l1->appendChild($IIIIIIllllI1->createElement('s:Header'));
$IIIIIIlll11I->appendChild($IIIIIIllllI1->createElement('a:Action','http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue'))->setAttribute('s:mustUnderstand',"1");
$IIIIIIlll11I->appendChild($IIIIIIllllI1->createElement('a:MessageId','urn:uuid:'.parent::getUuid()));
$IIIIIIlll11I->appendChild($IIIIIIllllI1->createElement('a:ReplyTo'))->appendChild($IIIIIIllllI1->createElement('a:Address','http://www.w3.org/2005/08/addressing/anonymous'));
$IIIIIIlll11I->appendChild($IIIIIIllllI1->createElement('a:To',$IIIIIIlllI1l))->setAttribute('s:mustUnderstand',"1");
$IIIIIIlll11l = $IIIIIIlll11I->appendChild($IIIIIIllllI1->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd','o:Security'));
$IIIIIIlll11l->setAttribute('s:mustUnderstand',"1");
$IIIIIIlll111 = $IIIIIIlll11l->appendChild($IIIIIIllllI1->createElement('u:Timestamp'));
$IIIIIIlll111->setAttribute('u:Id','_0');
$IIIIIIlll111->appendChild($IIIIIIllllI1->createElement('u:Created',self::getCurrentTime().'Z'));
$IIIIIIlll111->appendChild($IIIIIIllllI1->createElement('u:Expires',self::getExpiryTime().'Z'));
$IIIIIIll1III = $IIIIIIlll11l->appendChild($IIIIIIllllI1->createElement('o:UsernameToken'));
$IIIIIIll1III->setAttribute('u:Id','uuid-14bed392-2320-44ae-859d-fa4ec83df57a-1');
$IIIIIIll1III->appendChild($IIIIIIllllI1->createElement('o:Username',$IIIIIIllllII));
$IIIIIIll1III->appendChild($IIIIIIllllI1->createElement('o:Password',$IIIIIIllllIl))->setAttribute('Type','http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText');
$IIIIIIll1IIl = $IIIIIIlll1l1->appendChild($IIIIIIllllI1->createElementNS('http://www.w3.org/2003/05/soap-envelope','s:Body'));
$IIIIIIll1II1 = $IIIIIIll1IIl->appendChild($IIIIIIllllI1->createElementNS('http://schemas.xmlsoap.org/ws/2005/02/trust','t:RequestSecurityToken'));
$IIIIIIll1IlI = $IIIIIIll1II1->appendChild($IIIIIIllllI1->createElementNS('http://schemas.xmlsoap.org/ws/2004/09/policy','wsp:AppliesTo'));
$IIIIIIll1Ill = $IIIIIIll1IlI->appendChild($IIIIIIllllI1->createElement('a:EndpointReference'));
$IIIIIIll1Ill->appendChild($IIIIIIllllI1->createElement('a:Address',"urn:".$IIIIIIlllI11));
$IIIIIIll1II1->appendChild($IIIIIIllllI1->createElement('t:RequestType','http://schemas.xmlsoap.org/ws/2005/02/trust/Issue'));
return $IIIIIIllllI1->saveXML($IIIIIIlll1l1);
}
}?>