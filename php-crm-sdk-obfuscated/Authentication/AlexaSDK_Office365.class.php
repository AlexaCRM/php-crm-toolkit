<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSWxJMUlsbCddPSdkYXRlJzskR0xPQkFMU1snSUlJSUlJbGxsMWxsJ109J3N1YnN0cic7JEdMT0JBTFNbJ0lJSUlJSWxsbDFJbCddPSdwcmVnX21hdGNoJzskR0xPQkFMU1snSUlJSUlJSTExMUlJJ109J3RpbWUnOw==')); ?><?php 
class AlexaSDK_Office365 extends AlexaSDK{
public $IIIIIIIIIIIl;
private $IIIIIIIIIl1I;
private $IIIIIIllI1Il;
private $IIIIIIlIll11;
function __construct($IIIIIIIIlIII,$IIIIIIllI1I1){
$this->IIIIIIIIIIIl = $IIIIIIIIlIII;
$this->IIIIIIlIll11 = $IIIIIIllI1I1;
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
if ($this->IIIIIIllI1Il != NULL) {
if ($this->IIIIIIllI1Il['expiryTime'] >$GLOBALS['IIIIIII111II']()) {
return $this->IIIIIIllI1Il;
}
}else{
$IIIIIIlIIIll = $this->IIIIIIlIll11->getCachedSecurityToken("discovery",$this->IIIIIIllI1Il);
if ($IIIIIIlIIIll &&$this->IIIIIIllI1Il['expiryTime'] >$GLOBALS['IIIIIII111II']()){
return $this->IIIIIIllI1Il;
}
}
$this->IIIIIIllI1Il = $this->requestSecurityToken($this->IIIIIIIIIIIl->IIIIIIllIlll,$this->IIIIIIIIIIIl->IIIIIIllIll1,$this->IIIIIIIIIIIl->IIIIIIllIIl1,$this->IIIIIIIIIIIl->IIIIIIllII1I);
$this->IIIIIIlIll11->setCachedSecurityToken('discovery',$this->IIIIIIllI1Il);
return $this->IIIIIIllI1Il;
}
protected function getSecurityHeaderNode(Array $securityToken) {
$IIIIIIllI11I = new DOMDocument();
$IIIIIIllI11l = $IIIIIIllI11I->appendChild($IIIIIIllI11I->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd','o:Security'));
$IIIIIIllI11l->setAttribute('s:mustUnderstand','1');
$IIIIIIllI111 = $IIIIIIllI11l->appendChild($IIIIIIllI11I->createElement('u:Timestamp'));
$IIIIIIllI111->setAttribute('u:Id','_0');
$IIIIIIllI111->appendChild($IIIIIIllI11I->createElement('u:Created',self::getCurrentTime().'Z'));
$IIIIIIllI111->appendChild($IIIIIIllI11I->createElement('u:Expires',self::getExpiryTime().'Z'));
$IIIIIIlllIII = $IIIIIIllI11I->createDocumentFragment();
$IIIIIIlllIII->appendXML($securityToken['securityToken']);
$IIIIIIllI11l->appendChild($IIIIIIlllIII);
return $IIIIIIllI11l;
}
protected function requestSecurityToken($IIIIIIlllI11,$IIIIIIllllII,$IIIIIIllllIl,$IIIIIIllllI1) {
$IIIIIIlllllI = self::getLoginXML($IIIIIIlllI11,$IIIIIIllllII,$IIIIIIllllIl,$IIIIIIllllI1);
$IIIIIIllllll = self::getSoapResponse($IIIIIIlllI11,$IIIIIIlllllI);
$IIIIIIllI11I = new DOMDocument();
$IIIIIIllI11I->loadXML($IIIIIIllllll);
$IIIIIIlllll1 = $IIIIIIllI11I->getElementsbyTagName("CipherValue");
$securityToken0 =  $IIIIIIlllll1->item(0)->textContent;
$securityToken1 =  $IIIIIIlllll1->item(1)->textContent;
$keyIdentifier = $IIIIIIllI11I->getElementsbyTagName("KeyIdentifier")->item(0)->textContent;
$binarySecret = $IIIIIIllI11I->getElementsbyTagName("BinarySecret")->item(0)->textContent;
$IIIIIIllI11I->getElementsByTagName('SecurityTokenReference')->item(0)->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:wsse','http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
$IIIIIIlllIII = $IIIIIIllI11I->saveXML($IIIIIIllI11I->getElementsByTagName("RequestedSecurityToken")->item(0));
$GLOBALS['IIIIIIlll1Il']('/<wst:RequestedSecurityToken>(.*)<\/wst:RequestedSecurityToken>/',$IIIIIIlllIII,$IIIIIIlll1I1);
$IIIIIIlllIII = $IIIIIIlll1I1[1];
$expiryTime = $IIIIIIllI11I->getElementsByTagName("RequestSecurityTokenResponse")->item(0)->getElementsByTagName('Expires')->item(0)->textContent;
$expiryTime = self::parseTime($GLOBALS['IIIIIIlll1ll']($expiryTime,0,-5),'%Y-%m-%dT%H:%M:%S');
$securityToken = Array(
'securityToken'=>$IIIIIIlllIII,
'securityToken0'=>$securityToken0,
'securityToken1'=>$securityToken1,
'binarySecret'=>$binarySecret,
'keyIdentifier'=>$keyIdentifier,
'expiryTime'=>$expiryTime
);
if (self::$IIIIIIIIlI1l) {
echo 'Got Security Token - Expires at: '.$GLOBALS['IIIIIIlI1Ill']('r',$securityToken['expiryTime']).PHP_EOL;
echo "\tKey Identifier\t: ".$securityToken['keyIdentifier'].PHP_EOL;
echo "\tSecurity Token 0\t: ".$GLOBALS['IIIIIIlll1ll']($securityToken['securityToken0'],0,25).'...'.$GLOBALS['IIIIIIlll1ll']($securityToken['securityToken0'],-25).' ('.strlen($securityToken['securityToken0']).')'.PHP_EOL;
echo "\tSecurity Token 1\t: ".$GLOBALS['IIIIIIlll1ll']($securityToken['securityToken1'],0,25).'...'.$GLOBALS['IIIIIIlll1ll']($securityToken['securityToken1'],-25).' ('.strlen($securityToken['securityToken1']).')'.PHP_EOL;
echo "\tBinary Secret\t: ".$securityToken['binarySecret'].PHP_EOL.PHP_EOL;
}
return $securityToken;
}
protected static function getLoginXML($IIIIIIlllI11,$IIIIIIllllII,$IIIIIIllllIl,$IIIIIIllllI1) {
$IIIIIIlllllI = new DOMDocument();
$IIIIIIlll11I = $IIIIIIlllllI->appendChild($IIIIIIlllllI->createElementNS('http://www.w3.org/2003/05/soap-envelope','s:Envelope'));
$IIIIIIlll11I->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:a','http://www.w3.org/2005/08/addressing');
$IIIIIIlll11I->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:u','http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
$IIIIIIlll11l = $IIIIIIlll11I->appendChild($IIIIIIlllllI->createElement('s:Header'));
$IIIIIIlll11l->appendChild($IIIIIIlllllI->createElement('a:Action','http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue'))->setAttribute('s:mustUnderstand',"1");
$IIIIIIlll11l->appendChild($IIIIIIlllllI->createElement('a:MessageId','urn:uuid:'.parent::getUuid()));
$IIIIIIlll11l->appendChild($IIIIIIlllllI->createElement('a:ReplyTo'))->appendChild($IIIIIIlllllI->createElement('a:Address','http://www.w3.org/2005/08/addressing/anonymous'));
$IIIIIIlll11l->appendChild($IIIIIIlllllI->createElement('a:To',$IIIIIIlllI11))->setAttribute('s:mustUnderstand',"1");
$IIIIIIlll111 = $IIIIIIlll11l->appendChild($IIIIIIlllllI->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd','o:Security'));
$IIIIIIlll111->setAttribute('s:mustUnderstand',"1");
$IIIIIIll1III = $IIIIIIlll111->appendChild($IIIIIIlllllI->createElement('u:Timestamp'));
$IIIIIIll1III->setAttribute('u:Id','_0');
$IIIIIIll1III->appendChild($IIIIIIlllllI->createElement('u:Created',self::getCurrentTime().'Z'));
$IIIIIIll1III->appendChild($IIIIIIlllllI->createElement('u:Expires',self::getExpiryTime().'Z'));
$IIIIIIll1IIl = $IIIIIIlll111->appendChild($IIIIIIlllllI->createElement('o:UsernameToken'));
$IIIIIIll1IIl->setAttribute('u:Id','uuid-14bed392-2320-44ae-859d-fa4ec83df57a-1');
$IIIIIIll1IIl->appendChild($IIIIIIlllllI->createElement('o:Username',$IIIIIIllllIl));
$IIIIIIll1IIl->appendChild($IIIIIIlllllI->createElement('o:Password',$IIIIIIllllI1))->setAttribute('Type','http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText');
$IIIIIIll1II1 = $IIIIIIlll11I->appendChild($IIIIIIlllllI->createElementNS('http://www.w3.org/2003/05/soap-envelope','s:Body'));
$IIIIIIll1IlI = $IIIIIIll1II1->appendChild($IIIIIIlllllI->createElementNS('http://schemas.xmlsoap.org/ws/2005/02/trust','t:RequestSecurityToken'));
$IIIIIIll1Ill = $IIIIIIll1IlI->appendChild($IIIIIIlllllI->createElementNS('http://schemas.xmlsoap.org/ws/2004/09/policy','wsp:AppliesTo'));
$IIIIIIll1Il1 = $IIIIIIll1Ill->appendChild($IIIIIIlllllI->createElement('a:EndpointReference'));
$IIIIIIll1Il1->appendChild($IIIIIIlllllI->createElement('a:Address',"urn:".$IIIIIIllllII));
$IIIIIIll1IlI->appendChild($IIIIIIlllllI->createElement('t:RequestType','http://schemas.xmlsoap.org/ws/2005/02/trust/Issue'));
return $IIIIIIlllllI->saveXML($IIIIIIlll11I);
}
}?>
