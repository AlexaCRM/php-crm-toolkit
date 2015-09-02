<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSUkxMTFJSSddPSd0aW1lJzskR0xPQkFMU1snSUlJSUlJSTExbDExJ109J2Zsb29yJzskR0xPQkFMU1snSUlJSUlJSTExbGxsJ109J2Jhc2U2NF9lbmNvZGUnOyRHTE9CQUxTWydJSUlJSUlJMTFsbEknXT0nYmFzZTY0X2RlY29kZSc7JEdMT0JBTFNbJ0lJSUlJSUlJbElJMSddPSdpc19hcnJheSc7JEdMT0JBTFNbJ0lJSUlJSUkxbElJbCddPSd1bnNlcmlhbGl6ZSc7JEdMT0JBTFNbJ0lJSUlJSUkxMWwxbCddPSdzZXJpYWxpemUnOyRHTE9CQUxTWydJSUlJSUlJMUlJSWwnXT0nYXJyYXlfa2V5X2V4aXN0cyc7JEdMT0JBTFNbJ0lJSUlJSUlsMTExSSddPSdleHBsb2RlJzskR0xPQkFMU1snSUlJSUlJSWwxSWwxJ109J3N0cnRvbG93ZXInOyRHTE9CQUxTWydJSUlJSUlJbDFJSTEnXT0nY291bnQnOyRHTE9CQUxTWydJSUlJSUlJbGwxbDEnXT0naHRtbGVudGl0aWVzJzskR0xPQkFMU1snSUlJSUlJSWxsSTExJ109J2FycmF5X21lcmdlJzskR0xPQkFMU1snSUlJSUlJbElJMWxJJ109J2luX2FycmF5JzskR0xPQkFMU1snSUlJSUlJbElsSUlJJ109J2lzX2ludCc7JEdMT0JBTFNbJ0lJSUlJSWxsbDFsbCddPSdzdWJzdHInOyRHTE9CQUxTWydJSUlJSUlsMTFJSWwnXT0ncHJlZ19yZXBsYWNlJzskR0xPQkFMU1snSUlJSUlJSUlsSWxsJ109J2Rpcm5hbWUnOw==')); ?><?php 
class AlexaSDK extends AlexaSDK_Abstract{
private $IIIIIIIIIIII;
private $IIIIIIIIIIIl;
private $IIIIIIIIIII1;
private $IIIIIIIIIIlI;
private $IIIIIIIIIIll;
private $IIIIIIIIIIl1;
private $IIIIIIIIII1I;
private $IIIIIIIIII1l;
private $IIIIIIIIII11;
private $IIIIIIIIIlII;
private $IIIIIIIIIlIl;
private $IIIIIIIIIlI1;
private $IIIIIIIIIllI;
private $IIIIIIIIIlll;
private $IIIIIIIIIll1;
private $IIIIIIIIIl1I;
private $IIIIIIIIIl1l = Array();
private $IIIIIIIIIl11;
private $IIIIIIIII1II = Array();
private $IIIIIIIII1Il;
private $IIIIIIIII1I1;
private $IIIIIIIII1lI;
private $IIIIIIIII1ll;
protected static $IIIIIIIII1l1 = 6000;
protected static $IIIIIIIII11I = 28800;
protected static $IIIIIIIII11l = self::MAX_CRM_RECORDS;
private $IIIIIIIII111 = Array();
function __construct(AlexaSDK_Settings $IIIIIIIIlIII,$IIIIIIIIlIIl = NULL) {
self::$IIIIIIIIlI1l = $IIIIIIIIlIIl;
$this->includes();
if ($IIIIIIIIlIII instanceof AlexaSDK_Settings){
$this->IIIIIIIIIIIl = $IIIIIIIIlIII;
}else{
throw new Exception("Settings must be instacne of AlexaSDK_Settings class");
}
$this->IIIIIIIIIl11 = new AlexaSDK_Cache($this->IIIIIIIIIIIl->IIIIIIllIl1l);
if (!$this->checkConnectionSettings()) {
switch ($this->IIIIIIIIIIIl->IIIIIIllIIll){
case "OnlineFederation":
throw new BadMethodCallException(get_class($this).' constructor requires Username and Password');
case "Federation":
throw new BadMethodCallException(get_class($this).' constructor requires the Discovery URI, Username and Password');
}
}
switch ($this->IIIIIIIIIIIl->IIIIIIllIIll){
case "OnlineFederation":
$this->IIIIIIIIIIII = new AlexaSDK_Office365($this->IIIIIIIIIIIl,$this);
break;
case "Federation":
$this->IIIIIIIIIIII = new AlexaSDK_Federation($this->IIIIIIIIIIIl,$this);
break;
}
$this->loadEntityDefinitionCache();
}
private function includes(){
include_once ( $GLOBALS['IIIIIIIIlIll'](__FILE__) ."/Authentication/AlexaSDK_Office365.class.php");
include_once ( $GLOBALS['IIIIIIIIlIll'](__FILE__) ."/Authentication/AlexaSDK_Federation.class.php");
include_once ( $GLOBALS['IIIIIIIIlIll'](__FILE__) ."/Helpers/AlexaSDK_Cache.class.php");
include_once ( $GLOBALS['IIIIIIIIlIll'](__FILE__) ."/Helpers/AlexaSDK_FormValidator.class.php");
}
public function clearCache(){
return $this->IIIIIIIIIl11->cleanup();
}
protected function getDiscoveryAuthenticationMode() {
if (isset($this->IIIIIIIIIIIl->IIIIIIllIIll)) 
return $this->IIIIIIIIIIIl->IIIIIIllIIll;
$IIIIIIIII1Il = $this->getDiscoveryDOM();
$this->IIIIIIIII1ll = self::findSecurityPolicy($IIIIIIIII1Il,'DiscoveryService');
if ($this->IIIIIIIII1ll->getElementsByTagName('Authentication')->length == 0) {
throw new Exception('Could not find Authentication tag in provided Discovery Security policy XML');
return FALSE;
}
$IIIIIIllIIll = Array();
if ($this->IIIIIIIII1ll->getElementsByTagName('Authentication')->length >1){
foreach($this->IIIIIIIII1ll->getElementsByTagName('Authentication') as $IIIIIIIIIIII){
array_push($IIIIIIllIIll,$IIIIIIIIIIII->textContent);
}
}else{
array_push($IIIIIIllIIll,$this->IIIIIIIII1ll->getElementsByTagName('Authentication')->item(0)->textContent);
}
return $IIIIIIllIIll;
}
protected function getDiscoveryDOM() {
if ($this->IIIIIIIII1Il != NULL) return $this->IIIIIIIII1Il;
if (self::$IIIIIIIIlI1l) echo 'Getting Discovery DOM WSDL data from: '.$this->IIIIIIIIIIIl->IIIIIIIIIIl1.'?wsdl'.PHP_EOL;
$IIIIIIIII1Il = new DOMDocument();
@$IIIIIIIII1Il->load($this->IIIIIIIIIIIl->IIIIIIIIIIl1.'?wsdl');
if (self::$IIIIIIIIlI1l) :
endif;
$this->mergeWSDLImports($IIIIIIIII1Il);
$this->IIIIIIIII1Il = $IIIIIIIII1Il;
return $IIIIIIIII1Il;
}
protected function getDiscoveryAuthenticationAddress() {
if (isset($this->IIIIIIIII1II['discovery_authuri'])) 
return $this->IIIIIIIII1II['discovery_authuri'];
if ($this->IIIIIIIII1ll == NULL) {
$IIIIIIIII1Il = $this->getDiscoveryDOM();
$this->IIIIIIIII1ll = self::findSecurityPolicy($IIIIIIIII1Il,'DiscoveryService');
}
if ($this->IIIIIIIII1II['discovery_authmode'] == "Federation"){
$IIIIIIIIllII = self::getFederatedSecurityAddress($this->IIIIIIIII1ll);
}else if ($this->IIIIIIIII1II['discovery_authmode'] == "OnlineFederation"){
$IIIIIIIIllII = self::getOnlineFederationSecurityAddress($this->IIIIIIIII1ll);
}
return $IIIIIIIIllII;
}
public function getOrganizationAuthenticationAddress() {
if (isset($this->IIIIIIIII1II['organization_authuri'])) 
return $this->IIIIIIIII1II['organization_authuri'];
if ($this->IIIIIIIIIll1 == NULL) {
$IIIIIIIIII1I = $this->getOrganizationDOM();
$this->IIIIIIIIIll1 = self::findSecurityPolicy($IIIIIIIIII1I,'OrganizationService');
}
$IIIIIIIIllII = self::getFederatedSecurityAddress($this->IIIIIIIIIll1);
$this->IIIIIIIII1II['organization_authuri'] = $IIIIIIIIllII;
return $IIIIIIIIllII;
}
public function getSecurityTokenServiceIdentifier($service){
if (isset($this->IIIIIIIII1II[$service.'_sts_identifier'])) 
return $this->IIIIIIIII1II[$service.'_sts_identifier'];
if ($this->IIIIIIIIIll1 == NULL) {
$IIIIIIIIII1I = $this->getOrganizationDOM();
$this->IIIIIIIIIll1 = self::findSecurityPolicy($IIIIIIIIII1I,'OrganizationService');
}
$this->IIIIIIIII1II[$service.'_sts_identifier'] = self::getSTSidentifier($this->IIIIIIIIIll1);
return $this->IIIIIIIII1II[$service.'_sts_identifier'];
}
public function getOrganizationAuthenticationMode() {
if (isset($this->IIIIIIIII1II['organization_authmode'])) 
return $this->IIIIIIIII1II['organization_authmode'];
$IIIIIIIIII1I = $this->getOrganizationDOM();
$this->IIIIIIIIIll1 = self::findSecurityPolicy($IIIIIIIIII1I,'OrganizationService');
$authType = $this->IIIIIIIIIll1->getElementsByTagName('Authentication')->item(0)->textContent;
return $authType;
}
protected static function getFederatedSecurityAddress(DOMNode $IIIIIII11Ill) {
$securityURL = NULL;
if ($IIIIIII11Ill->getElementsByTagName('EndorsingSupportingTokens')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens tag in provided security policy XML');
return FALSE;
}
$estNode = $IIIIIII11Ill->getElementsByTagName('EndorsingSupportingTokens')->item(0);
if ($estNode->getElementsByTagName('Policy')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy tag in provided security policy XML');
return FALSE;
}
$estPolicyNode = $estNode->getElementsByTagName('Policy')->item(0);
if ($estPolicyNode->getElementsByTagName('IssuedToken')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken tag in provided security policy XML');
return FALSE;
}
$issuedTokenNode = $estPolicyNode->getElementsByTagName('IssuedToken')->item(0);
if ($issuedTokenNode->getElementsByTagName('Issuer')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer tag in provided security policy XML');
return FALSE;
}
$issuerNode = $issuedTokenNode->getElementsByTagName('Issuer')->item(0);
if ($issuerNode->getElementsByTagName('Metadata')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata tag in provided security policy XML');
return FALSE;
}
$metadataNode = $issuerNode->getElementsByTagName('Metadata')->item(0);
if ($metadataNode->getElementsByTagName('Address')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata/.../Address tag in provided security policy XML');
return FALSE;
}
$addressNode = $metadataNode->getElementsByTagName('Address')->item(0);
$securityURL = $addressNode->textContent;
if ($securityURL == NULL) {
throw new Exception('Could not find Security URL in provided security policy WSDL');
return FALSE;
}
return $securityURL;
}
protected static function getOnlineFederationSecurityAddress(DOMNode $IIIIIII11Ill) {
$securityURL = NULL;
if ($IIIIIII11Ill->getElementsByTagName('SignedSupportingTokens')->length == 0) {
throw new Exception('Could not find SignedSupportingTokens tag in provided security policy XML');
return FALSE;
}
$estNode = $IIIIIII11Ill->getElementsByTagName('SignedSupportingTokens')->item(0);
if ($estNode->getElementsByTagName('Policy')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy tag in provided security policy XML');
return FALSE;
}
$estPolicyNode = $estNode->getElementsByTagName('Policy')->item(0);
if ($estPolicyNode->getElementsByTagName('IssuedToken')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken tag in provided security policy XML');
return FALSE;
}
$issuedTokenNode = $estPolicyNode->getElementsByTagName('IssuedToken')->item(0);
if ($issuedTokenNode->getElementsByTagName('Issuer')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer tag in provided security policy XML');
return FALSE;
}
$issuerNode = $issuedTokenNode->getElementsByTagName('Issuer')->item(0);
if ($issuerNode->getElementsByTagName('Metadata')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata tag in provided security policy XML');
return FALSE;
}
$metadataNode = $issuerNode->getElementsByTagName('Metadata')->item(0);
if ($metadataNode->getElementsByTagName('Address')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata/.../Address tag in provided security policy XML');
return FALSE;
}
$addressNode = $metadataNode->getElementsByTagName('Address')->item(0);
$securityURL = $addressNode->textContent;
if ($securityURL == NULL) {
throw new Exception('Could not find Security URL in provided security policy WSDL');
return FALSE;
}
return $securityURL;
}
protected static function getTrust13UsernameAddress(DOMDocument $IIIIIIIIl1II) {
return self::getTrustAddress($IIIIIIIIl1II,'UserNameWSTrustBinding_IWSTrust13Async');
}
protected static function getLoginOnmicrosoftAddress1(DOMDocument $IIIIIIIIl1II) {
$securityURL = NULL;
if ($IIIIIII11Ill->getElementsByTagName('SignedSupportingTokens')->length == 0) {
throw new Exception('Could not find SignedSupportingTokens tag in provided security policy XML');
return FALSE;
}
$estNode = $IIIIIII11Ill->getElementsByTagName('SignedSupportingTokens')->item(0);
if ($estNode->getElementsByTagName('Policy')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy tag in provided security policy XML');
return FALSE;
}
$estPolicyNode = $estNode->getElementsByTagName('Policy')->item(0);
if ($estPolicyNode->getElementsByTagName('IssuedToken')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken tag in provided security policy XML');
return FALSE;
}
$issuedTokenNode = $estPolicyNode->getElementsByTagName('IssuedToken')->item(0);
if ($issuedTokenNode->getElementsByTagName('Issuer')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer tag in provided security policy XML');
return FALSE;
}
$issuerNode = $issuedTokenNode->getElementsByTagName('Issuer')->item(0);
if ($issuerNode->getElementsByTagName('Metadata')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Metadata tag in provided security policy XML');
return FALSE;
}
if ($issuerNode->getElementsByTagName('Address')->length == 0) {
throw new Exception('Could not find EndorsingSupportingTokens/Policy/IssuedToken/Issuer/Address tag in provided security policy XML');
return FALSE;
}
$loginAddressNode = $issuerNode->getElementsByTagName('Address')->item(0);
$securityURL = $loginAddressNode->textContent;
if ($securityURL == NULL) {
throw new Exception('Could not find Security URL in provided security policy WSDL');
return FALSE;
}
return $securityURL;
}
protected static function getTrustAddress(DOMDocument $IIIIIIIIl1II,$trustName) {
$trustAuthNode = NULL;
foreach ($IIIIIIIIl1II->getElementsByTagName('port') as $IIIIIIIlIllI) {
if ($IIIIIIIlIllI->hasAttribute('name') &&$IIIIIIIlIllI->getAttribute('name') == $trustName) {
$trustAuthNode = $IIIIIIIlIllI;
break;
}
}
if ($trustAuthNode == NULL) {
throw new Exception('Could not find Port for trust type <'.$trustName.'> in provided WSDL');
return FALSE;
}
$authenticationURI = NULL;
if ($trustAuthNode->getElementsByTagName('address')->length >0) {
$authenticationURI = $trustAuthNode->getElementsByTagName('address')->item(0)->getAttribute('location');
}
if ($authenticationURI == NULL) {
throw new Exception('Could not find Address for trust type <'.$trustName.'> in provided WSDL');
return FALSE;
}
return $authenticationURI;
}
protected function mergeWSDLImports(DOMNode &$wsdlDOM,$continued = false,DOMDocument &$newRootDocument = NULL) {
static $rootNode = NULL;
static $rootDocument = NULL;
if ($continued == false) {
$rootNode = $wsdlDOM->getElementsByTagName('definitions')->item(0);
$rootDocument = $wsdlDOM;
}
if ($newRootDocument == NULL) $newRootDocument = $rootDocument;
$nodesToRemove = Array();
foreach ($wsdlDOM->childNodes as $IIIIIIlIl11l) {
if ($IIIIIIlIl11l->localName == 'import') {
if ($IIIIIIlIl11l->hasAttribute('location')) {
$IIIIIIIIlll1 = $IIIIIIlIl11l->getAttribute('location');
}else if ($IIIIIIlIl11l->hasAttribute('schemaLocation')) {
$IIIIIIIIlll1 = $IIIIIIlIl11l->getAttribute('schemaLocation');
}else {
$IIIIIIIIlll1 = NULL;
}
if ($IIIIIIIIlll1 != NULL) {
if (self::$IIIIIIIIlI1l) echo "\tImporting data from: ".$IIIIIIIIlll1.PHP_EOL;
$IIIIIIIIll1I = new DOMDocument();
@$IIIIIIIIll1I->load($IIIIIIIIlll1);
$IIIIIIIIll1l = $IIIIIIIIll1I->getElementsByTagName('definitions')->item(0);
if ($IIIIIIIIll1l != NULL) {
foreach ($IIIIIIIIll1l->attributes as $IIIIIIIIll11) {
if ($IIIIIIIIll11->name != 'targetNamespace') {
$rootNode->setAttributeNode($IIIIIIIIll11);
}
}
$this->mergeWSDLImports($IIIIIIIIll1l,true,$IIIIIIIIll1I);
foreach ($IIIIIIIIll1l->childNodes as $importNode) {
$importNode = $newRootDocument->importNode($importNode,true);
$wsdlDOM->insertBefore($importNode,$IIIIIIlIl11l);
}
}else {
$importNode = $newRootDocument->importNode($IIIIIIIIll1I->firstChild,true);
$wsdlDOM->insertBefore($importNode,$IIIIIIlIl11l);
}
$nodesToRemove[] = $IIIIIIlIl11l;
}
}else {
if ($IIIIIIlIl11l->hasChildNodes()) {
$this->mergeWSDLImports($IIIIIIlIl11l,true);
}
}
}
foreach ($nodesToRemove as $IIIIIIIl1lIl) {
$wsdlDOM->removeChild($IIIIIIIl1lIl);
}
return $wsdlDOM;
}
protected static function parseRetrieveEntityResponse($IIIIIIII1l11) {
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$IIIIIII1ll11 = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('ExecuteResult') as $IIIIIIIl1lIl) {
if ($IIIIIIIl1lIl->hasAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type') &&self::stripNS($IIIIIIIl1lIl->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type')) == 'RetrieveEntityResponse') {
$IIIIIII1ll11 = $IIIIIIIl1lIl;
break;
}
}
unset($IIIIIIIl1lIl);
if ($IIIIIII1ll11 == NULL) {
throw new Exception('Could not find ExecuteResult for RetrieveEntityResponse in XML provided');
return FALSE;
}
$entityMetadataNode = NULL;
foreach ($IIIIIII1ll11->getElementsByTagName('value') as $IIIIIIIl1lIl) {
if ($IIIIIIIl1lIl->hasAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type') &&self::stripNS($IIIIIIIl1lIl->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type')) == 'EntityMetadata') {
$entityMetadataNode = $IIIIIIIl1lIl;
break;
}
}
unset($IIIIIIIl1lIl);
if ($entityMetadataNode == NULL) {
throw new Exception('Could not find returned EntityMetadata in XML provided');
return FALSE;
}
$returnValue = $GLOBALS['IIIIIIl11IIl']('/(<)([a-z]:)/','<',$GLOBALS['IIIIIIl11IIl']('/(<\/)([a-z]:)/','</',$IIIIIIII1l11));
$simpleXML = simplexml_load_string($returnValue);
if (!$simpleXML){
throw new Exception('Unable to load metadata simple_xml_class');
return FALSE;
}
$IIIIIIIl1l1l = $simpleXML->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value;
if (!$IIIIIIIl1l1l){
throw new Exception('Unable to load metadata simple_xml_class KeyValuePairOfstringanyType value');
return FALSE;
}
return $IIIIIIIl1l1l;
}
public static function parseRetrieveAllEntitiesResponse1($IIIIIIII1l11) {
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$IIIIIII1ll11 = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('ExecuteResult') as $IIIIIIIl1lIl) {
if ($IIIIIIIl1lIl->hasAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type') &&
(self::stripNS($IIIIIIIl1lIl->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type')) == 'RetrieveEntityResponse'
) ||(self::stripNS($IIIIIIIl1lIl->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type')) == 'RetrieveAllEntitiesResponse')) {
$IIIIIII1ll11 = $IIIIIIIl1lIl;
break;
}
}
unset($IIIIIIIl1lIl);
if ($IIIIIII1ll11 == NULL) {
throw new Exception('Could not find ExecuteResult for RetrieveEntityResponse in XML provided');
return FALSE;
}
$entityMetadataNode = NULL;
foreach ($IIIIIII1ll11->getElementsByTagName('value') as $IIIIIIIl1lIl) {
$entityMetadataNode = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($entityMetadataNode == NULL) {
throw new Exception('Could not find returned EntityMetadata in XML provided');
return FALSE;
}
$returnValue = $GLOBALS['IIIIIIl11IIl']('/(<)([a-z]:)/','<',$GLOBALS['IIIIIIl11IIl']('/(<\/)([a-z]:)/','</',$IIIIIIII1l11));
$simpleXML = simplexml_load_string($returnValue);
if (!$simpleXML){
throw new Exception('Unable to load metadata simple_xml_class');
return FALSE;
}
$IIIIIIIl1l1l = $simpleXML->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value;
if (!$IIIIIIIl1l1l){
throw new Exception('Unable to load metadata simple_xml_class KeyValuePairOfstringanyType value');
return FALSE;
}
return $IIIIIIIl1l1l;
}
protected static function parseRetrieveAllEntitiesResponse(AlexaSDK $IIIIIIIl1I1I,$IIIIIIII1l11) {
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$IIIIIIIl1lII = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('ExecuteResponse') as $IIIIIIIl1lIl) {
$IIIIIIIl1lII = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($IIIIIIIl1lII == NULL) {
throw new Exception('Could not find ExecuteResponse node in XML provided');
return FALSE;
}
$IIIIIIIl1lI1 = NULL;
foreach ($IIIIIIIl1lII->getElementsByTagName('Results') as $IIIIIIIl1lIl) {
$IIIIIIIl1lI1 = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($IIIIIIIl1lI1 == NULL) {
throw new Exception('Could not find ExecuteResult node in XML provided');
return FALSE;
}
$IIIIIIIl1llI = Array();
foreach ($IIIIIIIl1lI1->getElementsByTagName('EntityMetadata') as $IIIIIIIl1lll) {
if ($IIIIIIIl1lll->getElementsByTagName("IsValidForAdvancedFind")->item(0)->textContent == "true"){
$responseElement["LogicalName"] = $IIIIIIIl1lll->getElementsByTagName("LogicalName")->item(0)->textContent;
$responseElement["DisplayName"] = $IIIIIIIl1lll->getElementsByTagName("DisplayName")->item(0)->getElementsByTagName("UserLocalizedLabel")->item(0)->getElementsByTagName("Label")->item(0)->textContent;
array_push($IIIIIIIl1llI,$responseElement);
}
}
$IIIIIIIl1l1l = $IIIIIIIl1llI;
return $IIIIIIIl1l1l;
}
private function checkSecurity($service) {
if (!isset($this->IIIIIIIII1II[$service.'_authmode'])){
$this->IIIIIIIII1II[$service.'_authmode'] = $this->IIIIIIIIIIIl->IIIIIIllIIll;
}
if ($this->IIIIIIIII1II[$service.'_authmode'] == NULL ) return FALSE;
switch ($this->IIIIIIIII1II[$service.'_authmode']) {
case 'Federation':
return $this->checkFederationSecurity($service);
break;
case 'OnlineFederation':
return $this->checkOnlineFederationSecurity($service);
break;
}
return FALSE;
}
private function checkFederationSecurity($service) {
if ($this->IIIIIIIII1II[$service.'_authmode'] != 'Federation') return FALSE;
if ($this->IIIIIIIII1II[$service.'_authuri'] == NULL) return FALSE;
if ($this->IIIIIIIII1II[$service.'_authendpoint'] == NULL) return FALSE;
if ($this->IIIIIIIII1II['username'] == NULL ||$this->IIIIIIIII1II['password'] == NULL) {
return FALSE;
}
return TRUE;
}
private function checkOnlineFederationSecurity($service) {
if ($this->IIIIIIIII1II[$service.'_authmode'] != 'OnlineFederation') return FALSE;
if ($this->IIIIIIIII1II[$service.'_authuri'] == NULL) return FALSE;
if ($this->IIIIIIIII1II[$service.'_authendpoint'] == NULL) return FALSE;
if ($this->IIIIIIIII1II['username'] == NULL ||$this->IIIIIIIII1II['password'] == NULL) {
return FALSE;
}
return TRUE;
}
public function getFederationSecurityURI($service) {
if (isset($this->IIIIIIIII1II[$service.'_authendpoint'])) 
return $this->IIIIIIIII1II[$service.'_authendpoint'];
if (self::$IIIIIIIIlI1l) echo 'Getting WSDL data for Federation Security URI from: '.$this->IIIIIIIII1II[$service.'_authuri'].PHP_EOL;
$IIIIIIIIl1II = new DOMDocument();
@$IIIIIIIIl1II->load($this->IIIIIIIII1II[$service.'_authuri']);
$this->mergeWSDLImports($IIIIIIIIl1II);
$IIIIIIIIl1Il = self::getTrust13UsernameAddress($IIIIIIIIl1II);
return $IIIIIIIIl1Il;
}
protected function getOnlineFederationSecurityURI($service) {
if (isset($this->IIIIIIIII1II[$service.'_authendpoint'])) 
return $this->IIIIIIIII1II[$service.'_authendpoint'];
if (self::$IIIIIIIIlI1l) echo 'Getting WSDL data for OnlineFederation Security URI from: '.$this->IIIIIIIII1II[$service.'_authuri'].PHP_EOL;
$IIIIIIIIl1II = new DOMDocument();
@$IIIIIIIIl1II->load($this->IIIIIIIII1II[$service.'_authuri']);
$this->mergeWSDLImports($IIIIIIIIl1II);
$IIIIIIIIl1Il = self::getLoginOnmicrosoftAddress($IIIIIIIIl1II);
return $IIIIIIIIl1Il;
}
protected static function findSecurityPolicy(DOMDocument $IIIIIIIlII1l,$IIIIIIIlII11) {
$IIIIIIIlIlII = NULL;
foreach ($IIIIIIIlII1l->getElementsByTagName('service') as $IIIIIIIlIlIl) {
if ($IIIIIIIlIlIl->hasAttribute('name') &&$IIIIIIIlIlIl->getAttribute('name') == $IIIIIIIlII11) {
$IIIIIIIlIlII = $IIIIIIIlIlIl;
break;
}
}
if ($IIIIIIIlIlII == NULL) {
throw new Exception('Could not find definition of Service <'.$IIIIIIIlII11.'> in provided WSDL');
return FALSE;
}
$IIIIIIIlIlI1 = NULL;
foreach ($IIIIIIIlIlII->getElementsByTagName('port') as $IIIIIIIlIllI) {
if ($IIIIIIIlIllI->hasAttribute('name')) {
$IIIIIIIlIlI1 = $IIIIIIIlIllI->getAttribute('name');
break;
}
}
if ($IIIIIIIlIlI1 == NULL) {
throw new Exception('Could not find binding for Service <'.$IIIIIIIlII11.'> in provided WSDL');
return FALSE;
}
$IIIIIIIlIlll = NULL;
foreach ($IIIIIIIlII1l->getElementsByTagName('binding') as $IIIIIIIlIlll) {
if ($IIIIIIIlIlll->hasAttribute('name') &&$IIIIIIIlIlll->getAttribute('name') == $IIIIIIIlIlI1) {
break;
}
}
if ($IIIIIIIlIlll == NULL) {
throw new Exception('Could not find defintion of Binding <'.$IIIIIIIlIlI1.'> in provided WSDL');
return FALSE;
}
$policyReferenceURI = NULL;
foreach ($IIIIIIIlIlll->getElementsByTagName('PolicyReference') as $policyReferenceNode) {
if ($policyReferenceNode->hasAttribute('URI')) {
$policyReferenceURI = $GLOBALS['IIIIIIlll1ll']($policyReferenceNode->getAttribute('URI'),1);
break;
}
}
if ($policyReferenceURI == NULL) {
throw new Exception('Could not find Policy Reference for Binding <'.$IIIIIIIlIlI1.'> in provided WSDL');
return FALSE;
}
$IIIIIII11Ill = NULL;
foreach ($IIIIIIIlII1l->getElementsByTagName('Policy') as $policyNode) {
if ($policyNode->hasAttribute('wsu:Id') &&$policyNode->getAttribute('wsu:Id') == $policyReferenceURI) {
$IIIIIII11Ill = $policyNode;
break;
}
}
if ($IIIIIII11Ill == NULL) {
throw new Exception('Could not find Policy with ID <'.$policyReferenceURI.'> in provided WSDL');
return FALSE;
}
return $IIIIIII11Ill;
}
private function getOrganizationDOM() {
if ($this->IIIIIIIIII1I != NULL) return $this->IIIIIIIIII1I;
if ($this->IIIIIIIIIIIl->IIIIIIIIIIll == NULL) {
throw new Exception('Cannot get Organization DOM before determining Organization URI');
}
if (self::$IIIIIIIIlI1l) echo 'Getting WSDL data for Organization DOM from: '.$this->IIIIIIIIIIIl->IIIIIIIIIIll.'?wsdl'.PHP_EOL;
$IIIIIIIIII1I = new DOMDocument();
@$IIIIIIIIII1I->load($this->IIIIIIIIIIIl->IIIIIIIIIIll.'?wsdl');
$this->mergeWSDLImports($IIIIIIIIII1I);
$this->IIIIIIIIII1I = $IIIIIIIIII1I;
return $IIIIIIIIII1I;
}
protected static function generateRetrieveEntityRequest($IIIIIIIIl1ll,$IIIIIIIIl1l1 = NULL,$IIIIIIIIl11I = NULL,$IIIIIIIIl11l = false) {
if ($IIIIIIIIl1l1 != NULL) $IIIIIIIIl1ll = NULL;
else $IIIIIIIIl1l1 = self::EmptyGUID;
if ($IIIIIIIIl11I == NULL) $IIIIIIIIl11I = 'Entity Attributes Privileges Relationships';
$IIIIIIIIl111 = new DOMDocument();
$IIIIIIII1III = $IIIIIIIIl111->appendChild($IIIIIIIIl111->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services','Execute'));
$IIIIIIII1IIl = $IIIIIIII1III->appendChild($IIIIIIIIl111->createElement('request'));
$IIIIIIII1IIl->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance','i:type','b:RetrieveEntityRequest');
$IIIIIIII1IIl->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:b','http://schemas.microsoft.com/xrm/2011/Contracts');
$IIIIIIII1II1 = $IIIIIIII1IIl->appendChild($IIIIIIIIl111->createElement('b:Parameters'));
$IIIIIIII1II1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:c','http://schemas.datacontract.org/2004/07/System.Collections.Generic');
$IIIIIIII1IlI = $IIIIIIII1II1->appendChild($IIIIIIIIl111->createElement('b:KeyValuePairOfstringanyType'));
$IIIIIIII1IlI->appendChild($IIIIIIIIl111->createElement('c:key','EntityFilters'));
$IIIIIIII1Ill = $IIIIIIII1IlI->appendChild($IIIIIIIIl111->createElement('c:value',$IIIIIIIIl11I));
$IIIIIIII1Ill->setAttribute('i:type','d:EntityFilters');
$IIIIIIII1Ill->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:d','http://schemas.microsoft.com/xrm/2011/Metadata');
$IIIIIIII1Il1 = $IIIIIIII1II1->appendChild($IIIIIIIIl111->createElement('b:KeyValuePairOfstringanyType'));
$IIIIIIII1Il1->appendChild($IIIIIIIIl111->createElement('c:key','MetadataId'));
$IIIIIIII1I1I = $IIIIIIII1Il1->appendChild($IIIIIIIIl111->createElement('c:value',$IIIIIIIIl1l1));
$IIIIIIII1I1I->setAttribute('i:type','d:guid');
$IIIIIIII1I1I->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:d','http://schemas.microsoft.com/2003/10/Serialization/');
$IIIIIIII1I1l = $IIIIIIII1II1->appendChild($IIIIIIIIl111->createElement('b:KeyValuePairOfstringanyType'));
$IIIIIIII1I1l->appendChild($IIIIIIIIl111->createElement('c:key','RetrieveAsIfPublished'));
$IIIIIIII1I11 = $IIIIIIII1I1l->appendChild($IIIIIIIIl111->createElement('c:value',($IIIIIIIIl11l?'true':'false')));
$IIIIIIII1I11->setAttribute('i:type','d:boolean');
$IIIIIIII1I11->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:d','http://www.w3.org/2001/XMLSchema');
$IIIIIIII1lII = $IIIIIIII1II1->appendChild($IIIIIIIIl111->createElement('b:KeyValuePairOfstringanyType'));
$IIIIIIII1lII->appendChild($IIIIIIIIl111->createElement('c:key','LogicalName'));
$IIIIIIII1lIl = $IIIIIIII1lII->appendChild($IIIIIIIIl111->createElement('c:value',$IIIIIIIIl1ll));
$IIIIIIII1lIl->setAttribute('i:type','d:string');
$IIIIIIII1lIl->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:d','http://www.w3.org/2001/XMLSchema');
$IIIIIIII1IIl->appendChild($IIIIIIIIl111->createElement('b:RequestId'))->setAttribute('i:nil','true');
$IIIIIIII1IIl->appendChild($IIIIIIIIl111->createElement('b:RequestName','RetrieveEntity'));
return $IIIIIIII1III;
}
public function retrieveEntity($IIIIIIIIl1ll,$IIIIIIIIl1l1 = NULL,$IIIIIIIIl11I = NULL,$IIIIIIIIl11l = false) {
$IIIIIIII1llI = $this->retrieveEntityRaw($IIIIIIIIl1ll,$IIIIIIIIl1l1,$IIIIIIIIl11I,$IIIIIIIIl11l);
$IIIIIIII1lll = self::parseRetrieveEntityResponse($IIIIIIII1llI);
return $IIIIIIII1lll;
}
public function retrieveEntityRaw($IIIIIIIIl1ll,$IIIIIIIIl1l1 = NULL,$IIIIIIIIl11I = NULL,$IIIIIIIIl11l = false) {
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$IIIIIIII1III = self::generateRetrieveEntityRequest($IIIIIIIIl1ll,$IIIIIIIIl1l1,$IIIIIIIIl11I,$IIIIIIIIl11l);
$IIIIIIII1l1l = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationExecuteAction(),$securityToken,$IIIIIIII1III);
$IIIIIIII1l11 = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$IIIIIIII1l1l);
return $IIIIIIII1l11;
}
public function retrieveMultipleEntities($IIIIIIIIl1ll,$IIIIIIII11II = TRUE,$IIIIIIII11Il = NULL,$IIIIIIII11I1 = NULL,$IIIIIIII11l1 = NULL,$IIIIIIII11lI = FALSE){
$IIIIIIII111I = new DOMDocument();
$fetch = $IIIIIIII111I->appendChild($IIIIIIII111I->createElement('fetch'));
$fetch->setAttribute('version','1.0');
$fetch->setAttribute('output-format','xml-platform');
$fetch->setAttribute('mapping','logical');
$fetch->setAttribute('distinct','false');
$entity = $fetch->appendChild($IIIIIIII111I->createElement('entity'));
$entity->setAttribute('name',$IIIIIIIIl1ll);
$entity->appendChild($IIIIIIII111I->createElement('all-attributes'));
$IIIIIIII111I->saveXML($fetch);
return $this->retrieveMultiple($IIIIIIII111I->C14N(),$IIIIIIII11II,$IIIIIIII11Il,$IIIIIIII11I1,$IIIIIIII11l1,$IIIIIIII11lI);
}
public function retrieveRaw(AlexaSDK_Entity $entity,$fieldSet = NULL) {
$IIIIIIIIl1ll = $entity->LogicalName;
$IIIIIIIIl1l1 = $entity->ID;
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$IIIIIIII1III = self::generateRetrieveRequest($IIIIIIIIl1ll,$IIIIIIIIl1l1,$fieldSet);
$retrieveRequest = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationRetrieveAction(),$securityToken,$IIIIIIII1III);
$IIIIIIII1l11 = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$retrieveRequest);
return $IIIIIIII1l11;
}
private function getOrganizationExecuteAction() {
if ($this->IIIIIIIIIlIl == NULL) {
$IIIIIIIlIIIl = $this->getAllOrganizationSoapActions();
$this->IIIIIIIIIlIl = $IIIIIIIlIIIl['Execute'];
}
return $this->IIIIIIIIIlIl;
}
private function getAllOrganizationSoapActions() {
if ($this->IIIIIIIIII1l == NULL) {
$this->IIIIIIIIII1l = self::getAllSoapActions($this->getOrganizationDOM(),'OrganizationService');
}
return $this->IIIIIIIIII1l;
}
public function getOrganizationURI() {
if ($this->IIIIIIIIIIIl->IIIIIIIIIIll != NULL) return $this->IIIIIIIIIIIl->IIIIIIIIIIll;
if ($this->checkSecurity('discovery') == FALSE)
throw new Exception('Cannot determine Organization URI before Discovery Service Security Details are set!');
$securityToken = $this->IIIIIIIIIIII->getDiscoverySecurityToken();
$discoveryServiceSoapAction = $this->getDiscoveryExecuteAction();
$discoverySoapRequest = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIl1,$discoveryServiceSoapAction,$securityToken,self::generateRetrieveOrganizationRequest());
$discovery_data = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIl1,$discoverySoapRequest);
$this->IIIIIIIIIII1 = "wpsdk";
$organizationServiceURI = NULL;
$organizationDomain = NULL;
$IIIIIIIII1Il = new DOMDocument();$IIIIIIIII1Il->loadXML($discovery_data);
if ($IIIIIIIII1Il->getElementsByTagName('OrganizationDetail')->length >0) {
foreach ($IIIIIIIII1Il->getElementsByTagName('OrganizationDetail') as $organizationNode) {
foreach ($organizationNode->getElementsByTagName('Endpoints')->item(0)->getElementsByTagName('KeyValuePairOfEndpointTypestringztYlk6OT') as $endpointDOM) {
if ($endpointDOM->getElementsByTagName('key')->item(0)->textContent == 'OrganizationService') {
$organizationServiceURI = $endpointDOM->getElementsByTagName('value')->item(0)->textContent;
}
if ($endpointDOM->getElementsByTagName('key')->item(0)->textContent == 'WebApplication') {
$organizationDomain = $endpointDOM->getElementsByTagName('value')->item(0)->textContent;
}
}
break;
}
}else {
throw new Exception('Error fetching Organization details:'.PHP_EOL.$discovery_data);
return FALSE;
}
if ($organizationServiceURI == NULL) {
throw new Exception('Could not find OrganizationService URI for the Organization <'.$this->IIIIIIIIIII1.'>');
return FALSE;
}
$this->IIIIIIIIIIlI = $organizationDomain;
$this->IIIIIIIIIIll = $organizationServiceURI;
return $organizationServiceURI;
}
public function retrieveOrganizations(){
$securityToken = $this->IIIIIIIIIIII->getDiscoverySecurityToken();
$discoveryServiceSoapAction = $this->getDiscoveryExecuteAction();
$discoverySoapRequest = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIl1,$discoveryServiceSoapAction,$securityToken,self::generateRetrieveOrganizationRequest());
$discovery_data = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIl1,$discoverySoapRequest);
$organizationDetails = Array();
$IIIIIIIII1Il = new DOMDocument();$IIIIIIIII1Il->loadXML($discovery_data);
if ($IIIIIIIII1Il->getElementsByTagName('OrganizationDetail')->length >0) {
foreach ($IIIIIIIII1Il->getElementsByTagName('OrganizationDetail') as $organizationNode) {
$organization = Array();
foreach ($organizationNode->getElementsByTagName('Endpoints')->item(0)->getElementsByTagName('KeyValuePairOfEndpointTypestringztYlk6OT') as $endpointDOM) {
$organization["Endpoints"][$endpointDOM->getElementsByTagName('key')->item(0)->textContent] = $endpointDOM->getElementsByTagName('value')->item(0)->textContent;
}
if ($organizationNode->getElementsByTagName('FriendlyName')->length >0) {
$organization["FriendlyName"] = $organizationNode->getElementsByTagName('FriendlyName')->item(0)->textContent;
}
if ($organizationNode->getElementsByTagName('OrganizationId')->length >0) {
$organization["OrganizationId"] = $organizationNode->getElementsByTagName('OrganizationId')->item(0)->textContent;
}
if ($organizationNode->getElementsByTagName('OrganizationVersion')->length >0) {
$organization["OrganizationVersion"] = $organizationNode->getElementsByTagName('OrganizationVersion')->item(0)->textContent;
}
if ($organizationNode->getElementsByTagName('State')->length >0) {
$organization["State"] = $organizationNode->getElementsByTagName('State')->item(0)->textContent;
}
if ($organizationNode->getElementsByTagName('UniqueName')->length >0) {
$organization["UniqueName"] = $organizationNode->getElementsByTagName('UniqueName')->item(0)->textContent;
}
if ($organizationNode->getElementsByTagName('UrlName')->length >0) {
$organization["UrlName"] = $organizationNode->getElementsByTagName('UrlName')->item(0)->textContent;
}
array_push($organizationDetails,$organization);
}
}
return $organizationDetails;
}
public function retrieveOrganization($webApplicationUrl){
$organizationDetails = NULL;
$parsedUrl = parse_url($webApplicationUrl);
$organizations = $this->retrieveOrganizations();
foreach($organizations as $organization){
if (substr_count($organization["Endpoints"]["WebApplication"] ,$parsedUrl["host"])){
$organizationDetails = $organization;
}
}
return $organizationDetails;
}
public static function getConnectorTimeout() {
return self::$IIIIIIIII1l1;
}
public static function setConnectorTimeout($_connectorTimeout) {
if (!$GLOBALS['IIIIIIlIlIII']($_connectorTimeout)) return;
self::$IIIIIIIII1l1 = $_connectorTimeout;
}
public static function getCacheTime() {
return self::$IIIIIIIII11I;
}
public static function setCacheTime($_cacheTime) {
if (!$GLOBALS['IIIIIIlIlIII']($_cacheTime)) return;
self::$IIIIIIIII11I = $_cacheTime;
}
public function getDiscoveryURI() {
return $this->discoveryURI;
}
public function getOrganization() {
return $this->IIIIIIIIIII1;
}
public static function getMaximumRecords() {
return self::$IIIIIIIII11l;
}
public static function setMaximumRecords($_maximumRecords) {
if (!$GLOBALS['IIIIIIlIlIII']($_maximumRecords)) return;
self::$IIIIIIIII11l = $_maximumRecords;
}
private static function formatHeaders($soapUrl,$content,$requestType = "POST") {
$scheme = parse_url($soapUrl);
$IIIIII1lI11I = array(
$requestType ." ".$scheme["path"] ." HTTP/1.1",
"Host: ".$scheme["host"],
'Connection: Keep-Alive',
"Content-type: application/soap+xml; charset=UTF-8",
"Content-length: ".strlen($content),
);
return $IIIIII1lI11I;
}
public static function getSoapResponse($soapUrl,$content,$throwException = true) {
$urlDetails = parse_url($soapUrl);
$IIIIII1lI11I = self::formatHeaders($soapUrl,$content);
$cURLHandle = curl_init();
curl_setopt($cURLHandle,CURLOPT_URL,$soapUrl);
curl_setopt($cURLHandle,CURLOPT_RETURNTRANSFER,1);
curl_setopt($cURLHandle,CURLOPT_TIMEOUT,self::$IIIIIIIII1l1);
curl_setopt($cURLHandle,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($cURLHandle,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
curl_setopt($cURLHandle,CURLOPT_HTTPHEADER,$IIIIII1lI11I);
curl_setopt($cURLHandle,CURLOPT_POST,1);
curl_setopt($cURLHandle,CURLOPT_POSTFIELDS,$content);
curl_setopt($cURLHandle,CURLOPT_HEADER,false);
$responseXML = curl_exec($cURLHandle);
if (curl_errno($cURLHandle) != CURLE_OK) {
throw new Exception('cURL Error: '.curl_error($cURLHandle));
}
$httpResponse = curl_getinfo($cURLHandle,CURLINFO_HTTP_CODE);
curl_close($cURLHandle);
$IIIIIII11IIl = new DOMDocument();
$IIIIIII11IIl->loadXML($responseXML);
if ($IIIIIII11IIl->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Envelope')->length <1) {
throw new Exception('Invalid SOAP Response: HTTP Response '.$httpResponse.PHP_EOL.$responseXML.PHP_EOL);
}
if ($IIIIIII11IIl->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Envelope')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Header')->length <1) {
throw new Exception('Invalid SOAP Response: No SOAP Header! '.PHP_EOL.$responseXML.PHP_EOL);
}
$IIIIIIIlIIll = $IIIIIII11IIl->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Envelope')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Header')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2005/08/addressing','Action')->item(0)->textContent;
if (self::$IIIIIIIIlI1l) echo __FUNCTION__.': SOAP Action in returned XML is "'.$IIIIIIIlIIll.'"'.PHP_EOL;
if ($GLOBALS['IIIIIIlII1lI']($IIIIIIIlIIll,self::$IIIIIII111ll) &&$throwException) {
$faultCode = $IIIIIII11IIl->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Envelope')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Body')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Fault')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Code')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Value')->item(0)->nodeValue;
$faultCode = self::stripNS($faultCode);
$faultString = $IIIIIII11IIl->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Envelope')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Body')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Fault')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Reason')->item(0)
->getElementsByTagNameNS('http://www.w3.org/2003/05/soap-envelope','Text')->item(0)->nodeValue.PHP_EOL;
throw new SoapFault($faultCode,$faultString);
}
return $responseXML;
}
private function getDiscoveryExecuteAction() {
if ($this->IIIIIIIII1lI == NULL) {
$IIIIIIIlIIIl = $this->getAllDiscoverySoapActions();
$this->IIIIIIIII1lI = $IIIIIIIlIIIl['Execute'];
}
return $this->IIIIIIIII1lI;
}
private function getAllDiscoverySoapActions() {
if ($this->IIIIIIIII1I1 == NULL) {
$this->IIIIIIIII1I1 = self::getAllSoapActions($this->getDiscoveryDOM(),'DiscoveryService');
}
return $this->IIIIIIIII1I1;
}
private static function getAllSoapActions(DOMDocument $IIIIIIIlII1l,$IIIIIIIlII11) {
$IIIIIIIlIlII = NULL;
foreach ($IIIIIIIlII1l->getElementsByTagName('service') as $IIIIIIIlIlIl) {
if ($IIIIIIIlIlIl->hasAttribute('name') &&$IIIIIIIlIlIl->getAttribute('name') == $IIIIIIIlII11) {
$IIIIIIIlIlII = $IIIIIIIlIlIl;
break;
}
}
if ($IIIIIIIlIlII == NULL) {
throw new Exception('Could not find definition of Service <'.$IIIIIIIlII11.'> in provided WSDL');
return FALSE;
}
$IIIIIIIlIlI1 = NULL;
foreach ($IIIIIIIlIlII->getElementsByTagName('port') as $IIIIIIIlIllI) {
if ($IIIIIIIlIllI->hasAttribute('name')) {
$IIIIIIIlIlI1 = $IIIIIIIlIllI->getAttribute('name');
break;
}
}
if ($IIIIIIIlIlI1 == NULL) {
throw new Exception('Could not find binding for Service <'.$IIIIIIIlII11.'> in provided WSDL');
return FALSE;
}
$IIIIIIIlIlll = NULL;
foreach ($IIIIIIIlII1l->getElementsByTagName('binding') as $IIIIIIIlIlll) {
if ($IIIIIIIlIlll->hasAttribute('name') &&$IIIIIIIlIlll->getAttribute('name') == $IIIIIIIlIlI1) {
break;
}
}
if ($IIIIIIIlIlll == NULL) {
throw new Exception('Could not find defintion of Binding <'.$IIIIIIIlIlI1.'> in provided WSDL');
return FALSE;
}
$IIIIIIIlIll1 = Array();
foreach ($IIIIIIIlIlll->getElementsByTagName('operation') as $IIIIIIIlIl1I) {
if ($IIIIIIIlIl1I->hasAttribute('name')) {
$IIIIIIIlIl1l = $IIIIIIIlIl1I->getAttribute('name');
foreach ($IIIIIIIlIl1I->getElementsByTagName('operation') as $IIIIIIIlIl11) {
if ($IIIIIIIlIl11->hasAttribute('soapAction')) {
$soapAction = $IIIIIIIlIl11->getAttribute('soapAction');
$IIIIIIIlIll1[$IIIIIIIlIl1l] = $soapAction;
}
}
unset($IIIIIIIlIl11);
}
}
return $IIIIIIIlIll1;
}
protected function generateSoapRequest($IIIIIIIlI1I1,$soapAction,$securityToken,DOMNode $IIIIIIIlI1lI) {
$IIIIIIIlI1ll = new DOMDocument();
$IIIIIIIlI1l1 = $IIIIIIIlI1ll->appendChild($IIIIIIIlI1ll->createElementNS('http://www.w3.org/2003/05/soap-envelope','s:Envelope'));
$IIIIIIIlI1l1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:a','http://www.w3.org/2005/08/addressing');
$IIIIIIIlI1l1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:u','http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
$IIIIIIIlI11I = $this->generateSoapHeader($IIIIIIIlI1I1,$soapAction,$securityToken);
$IIIIIIIlI1l1->appendChild($IIIIIIIlI1ll->importNode($IIIIIIIlI11I,true));
$IIIIIIIlI11l = $IIIIIIIlI1l1->appendChild($IIIIIIIlI1ll->createElement('s:Body'));
$IIIIIIIlI11l->appendChild($IIIIIIIlI1ll->importNode($IIIIIIIlI1lI,true));
return $IIIIIIIlI1ll->saveXML($IIIIIIIlI1l1);
}
protected static function generateRetrieveOrganizationRequest() {
$IIIIIIIllIII = new DOMDocument();
$IIIIIIII1III = $IIIIIIIllIII->appendChild($IIIIIIIllIII->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Discovery','Execute'));
$IIIIIIII1IIl = $IIIIIIII1III->appendChild($IIIIIIIllIII->createElement('request'));
$IIIIIIII1IIl->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance','i:type','RetrieveOrganizationsRequest');
$IIIIIIII1IIl->appendChild($IIIIIIIllIII->createElement('AccessType','Default'));
$IIIIIIII1IIl->appendChild($IIIIIIIllIII->createElement('Release','Current'));
return $IIIIIIII1III;
}
protected function generateSoapHeader($IIIIIIIlI1I1,$soapAction,$securityToken) {
$IIIIIIIllII1 = new DOMDocument();
$IIIIIIIllIlI = $IIIIIIIllII1->appendChild($IIIIIIIllII1->createElement('s:Header'));
$IIIIIIIllIlI->appendChild($IIIIIIIllII1->createElement('a:Action',$soapAction))->setAttribute('s:mustUnderstand','1');
$IIIIIIIllIlI->appendChild($IIIIIIIllII1->createElement('a:ReplyTo'))->appendChild($IIIIIIIllII1->createElement('a:Address','http://www.w3.org/2005/08/addressing/anonymous'));
$IIIIIIIllIlI->appendChild($IIIIIIIllII1->createElement('a:MessageId','urn:uuid:'.parent::getUuid()));
$IIIIIIIllIlI->appendChild($IIIIIIIllII1->createElement('a:To',$IIIIIIIlI1I1))->setAttribute('s:mustUnderstand','1');
$IIIIIIIllIll = $this->IIIIIIIIIIII->getSecurityHeaderNode($securityToken);
$IIIIIIIllIlI->appendChild($IIIIIIIllII1->importNode($IIIIIIIllIll,true));
return $IIIIIIIllIlI;
}
private function checkConnectionSettings() {
if ($this->IIIIIIIIIIIl->IIIIIIllIIl1 == NULL) return FALSE;
if ($this->IIIIIIIIIIIl->IIIIIIllII1I == NULL) return FALSE;
switch($this->IIIIIIIIIIIl->IIIIIIllIIll){
case "Federation":
if ($this->IIIIIIIIIIIl->IIIIIIIIIIl1 == NULL) return FALSE;
return TRUE;
case "OnlineFederation":
return TRUE;
default:
return FALSE;
}
}
public function retrieveMultiple($IIIIIIII111I,$IIIIIIII11II = TRUE,$IIIIIIII11Il = NULL,$IIIIIIII11I1 = NULL,$IIIIIIII11l1 = NULL,$IIIIIIII11lI = FALSE) {
$IIIIIIII1lll = NULL;
if ($IIIIIIII11II) $IIIIIIII11Il = NULL;
do {
$IIIIIIII1llI = $this->retrieveMultipleRaw($IIIIIIII111I,$IIIIIIII11Il,$IIIIIIII11I1,$IIIIIIII11l1);
$IIIIIIIllI1l = self::parseRetrieveMultipleResponse($this,$IIIIIIII1llI,$IIIIIIII11lI);
if ($IIIIIIII1lll != NULL) {
$IIIIIIIllI1l->Entities = $GLOBALS['IIIIIIIllI11']($IIIIIIII1lll->Entities,$IIIIIIIllI1l->Entities);
$IIIIIIIllI1l->Count += $IIIIIIII1lll->Count;
}
$IIIIIIII1lll = $IIIIIIIllI1l;
if ($IIIIIIII1lll->MoreRecords &&$IIIIIIII1lll->PagingCookie == NULL) {
if ($IIIIIIII11Il == NULL) {
$IIIIIIIlllII = 1;
}else {
$IIIIIIIlllII = self::getPageNo($IIIIIIII11Il) +1;
}
$IIIIIIII11Il = '<cookie page="'.$IIIIIIIlllII.'"></cookie>';
$IIIIIIII1lll->PagingCookie = $IIIIIIII11Il;
}else {
$IIIIIIII11Il = $IIIIIIII1lll->PagingCookie;
}
}while ($IIIIIIII1lll->MoreRecords &&$IIIIIIII11II);
return $IIIIIIII1lll;
}
public function retrieveMultipleSimple($IIIIIIII111I,$IIIIIIII11II = TRUE,$IIIIIIII11Il = NULL,$IIIIIIII11l1 = NULL,$IIIIIIII11I1 = NULL) {
return $this->retrieveMultiple($IIIIIIII111I,$IIIIIIII11II,$IIIIIIII11Il,$IIIIIIII11I1,$IIIIIIII11l1,true);
}
public function retrieveSingle($IIIIIIII111I){
$IIIIIIIllllI = $this->retrieveMultiple($IIIIIIII111I,FALSE,NULL,1,NULL,false);
return ($IIIIIIIllllI->Count) ?$IIIIIIIllllI->Entities[0] : NULL;
}
public function retrieveMultipleRaw($IIIIIIII111I,$IIIIIIII11Il = NULL,$IIIIIIII11I1 = NULL,$IIIIIIII11l1 = NULL) {
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$IIIIIIII1III = self::generateRetrieveMultipleRequest($IIIIIIII111I,$IIIIIIII11Il,$IIIIIIII11I1,$IIIIIIII11l1);
$IIIIIIIllll1 = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationRetrieveMultipleAction(),$securityToken,$IIIIIIII1III);
$IIIIIIII1l11 = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$IIIIIIIllll1);
return $IIIIIIII1l11;
}
protected static function generateRetrieveMultipleRequest($IIIIIIII111I,$IIIIIIII11Il = NULL,$IIIIIIII11I1 = NULL,$IIIIIIII11l1 = NULL) {
if ($IIIIIIII11Il != NULL) {
$IIIIIIIlll1l = new DOMDocument();$IIIIIIIlll1l->loadXML($IIIIIIII111I);
if ($IIIIIIII11l1 == NULL){
$IIIIIIIlll11 = self::getPageNo($IIIIIIII11Il) +1;
}else{
$IIIIIIIlll11 = $IIIIIIII11l1;
}
$IIIIIIIlll1l->documentElement->setAttribute('page',$IIIIIIIlll11);
$IIIIIIIlll1l->documentElement->setAttribute('paging-cookie',$IIIIIIII11Il);
$IIIIIIII111I = $IIIIIIIlll1l->saveXML($IIIIIIIlll1l->documentElement);
}
$IIIIIIIlll1l = new DOMDocument();
$IIIIIIIlll1l->loadXML($IIIIIIII111I);
$IIIIIIIll1II = self::$IIIIIIIII11l+1;
if ($IIIIIIIlll1l->documentElement->hasAttribute('count')) {
$IIIIIIIll1II = $IIIIIIIlll1l->documentElement->getAttribute('count');
}
$IIIIIIIll1Il = ($IIIIIIII11I1 == NULL) ?self::$IIIIIIIII11l : $IIIIIIII11I1;
if ($IIIIIIIll1Il >self::$IIIIIIIII11l) $IIIIIIIll1Il = self::$IIIIIIIII11l;
if ($IIIIIIIll1II >$IIIIIIIll1Il) {
$IIIIIIIlll1l->documentElement->setAttribute('count',$IIIIIIIll1Il);
$IIIIIIII111I = $IIIIIIIlll1l->saveXML($IIIIIIIlll1l->documentElement);
}
$IIIIIIIll1I1 = new DOMDocument();
$IIIIIIIll1lI = $IIIIIIIll1I1->appendChild($IIIIIIIll1I1->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services','RetrieveMultiple'));
$IIIIIIIll1ll = $IIIIIIIll1lI->appendChild($IIIIIIIll1I1->createElement('query'));
$IIIIIIIll1ll->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance','i:type','b:FetchExpression');
$IIIIIIIll1ll->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:b','http://schemas.microsoft.com/xrm/2011/Contracts');
$IIIIIIIll1ll->appendChild($IIIIIIIll1I1->createElement('b:Query',$GLOBALS['IIIIIIIll1l1']($IIIIIIII111I)));
return $IIIIIIIll1lI;
}
protected static function generateRetrieveRequest($IIIIIIIIl1ll,$IIIIIIIIl1l1,$columnSet) {
$IIIIIIIll111 = new DOMDocument();
$IIIIIIIl1III = $IIIIIIIll111->appendChild($IIIIIIIll111->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services','Retrieve'));
$IIIIIIIl1III->appendChild($IIIIIIIll111->createElement('entityName',$IIIIIIIIl1ll));
$IIIIIIIl1III->appendChild($IIIIIIIll111->createElement('id',$IIIIIIIIl1l1));
$IIIIIIIl1IIl = $IIIIIIIl1III->appendChild($IIIIIIIll111->createElement('columnSet'));
$IIIIIIIl1IIl->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:b','http://schemas.microsoft.com/xrm/2011/Contracts');
$IIIIIIIl1IIl->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:i','http://www.w3.org/2001/XMLSchema-instance');
if ($columnSet != NULL &&$GLOBALS['IIIIIIIl1II1']($columnSet) >0) {
$IIIIIIIl1IIl->appendChild($IIIIIIIll111->createElement('b:AllColumns','false'));
$IIIIIIIl1IlI = $IIIIIIIl1IIl->appendChild($IIIIIIIll111->createElement('b:Columns'));
$IIIIIIIl1IlI->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:c','http://schemas.microsoft.com/2003/10/Serialization/Arrays');
foreach ($columnSet as $IIIIIIIl1Ill) {
$IIIIIIIl1IlI->appendChild($IIIIIIIll111->createElement('c:string',$GLOBALS['IIIIIIIl1Il1']($IIIIIIIl1Ill)));
}
}else {
$IIIIIIIl1IIl->appendChild($IIIIIIIll111->createElement('b:AllColumns','true'));
}
return $IIIIIIIl1III;
}
public static function parseRetrieveMultipleResponse(AlexaSDK $IIIIIIIl1I1I,$IIIIIIII1l11,$IIIIIIII11lI) {
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$IIIIIIIl1lII = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('RetrieveMultipleResponse') as $IIIIIIIl1lIl) {
$IIIIIIIl1lII = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($IIIIIIIl1lII == NULL) {
throw new Exception('Could not find RetrieveMultipleResponse node in XML provided');
return FALSE;
}
$IIIIIIIl1lI1 = NULL;
foreach ($IIIIIIIl1lII->getElementsByTagName('RetrieveMultipleResult') as $IIIIIIIl1lIl) {
$IIIIIIIl1lI1 = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($IIIIIIIl1lI1 == NULL) {
throw new Exception('Could not find RetrieveMultipleResult node in XML provided');
return FALSE;
}
$IIIIIIIl1llI = Array();
$IIIIIIIl1llI['EntityName'] = $IIIIIIIl1lI1->getElementsByTagName('EntityName')->length == 0 ?NULL : $IIIIIIIl1lI1->getElementsByTagName('EntityName')->item(0)->textContent;
$IIIIIIIl1llI['MoreRecords'] = ($IIIIIIIl1lI1->getElementsByTagName('MoreRecords')->item(0)->textContent == 'true');
$IIIIIIIl1llI['PagingCookie'] = $IIIIIIIl1lI1->getElementsByTagName('PagingCookie')->length == 0 ?NULL : $IIIIIIIl1lI1->getElementsByTagName('PagingCookie')->item(0)->textContent;
$IIIIIIIl1llI['Entities'] = Array();
foreach ($IIIIIIIl1lI1->getElementsByTagName('Entities')->item(0)->getElementsByTagName('Entity') as $IIIIIIIl1lll) {
if ($IIIIIIII11lI) {
$IIIIIIIl1ll1 = Array();
$IIIIIIIl1l1I = $IIIIIIIl1lll->getElementsByTagName('Attributes')->item(0)->getElementsByTagName('KeyValuePairOfstringanyType');
self::addAttributes($IIIIIIIl1ll1,$IIIIIIIl1l1I);
$IIIIIIIl1l1I = $IIIIIIIl1lll->getElementsByTagName('FormattedValues')->item(0)->getElementsByTagName('KeyValuePairOfstringstring');
self::addFormattedValues($IIIIIIIl1ll1,$IIIIIIIl1l1I);
$IIIIIIIl1llI['Entities'][] = (Object)$IIIIIIIl1ll1;
}else {
$entity = AlexaSDK_Entity::fromDOM($IIIIIIIl1I1I,$IIIIIIIl1llI['EntityName'],$IIIIIIIl1lll);
$IIIIIIIl1llI['Entities'][] = $entity;
}
}
$IIIIIIIl1llI['Count'] = $GLOBALS['IIIIIIIl1II1']($IIIIIIIl1llI['Entities']);
$IIIIIIIl1l1l = (Object)$IIIIIIIl1llI;
return $IIIIIIIl1l1l;
}
protected static function addAttributes(Array &$IIIIIIIl11II,DOMNodeList $IIIIIIIl1l1I,Array $IIIIIIIl11Il = NULL,$IIIIIIIl11I1 = NULL) {
foreach ($IIIIIIIl1l1I as $IIIIIIIl11lI) {
$IIIIIIIl11ll = $IIIIIIIl11lI->getElementsByTagName('key')->item(0)->textContent;
$IIIIIIIl11l1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type');
$IIIIIIIl11l1 = self::stripNS($IIIIIIIl11l1);
switch ($IIIIIIIl11l1) {
case 'AliasedValue':
list($IIIIIIIl11ll,) = $GLOBALS['IIIIIIIl111I']('.',$IIIIIIIl11ll,2);
$IIIIIIIl111l = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('EntityLogicalName')->item(0)->textContent;
$IIIIIIIl1111 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('AttributeLogicalName')->item(0)->textContent;
$IIIIIII1IIII = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->textContent;
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll,$IIIIIIIl11II)) {
$IIIIIII1III1 = $IIIIIIIl11II[$IIIIIIIl11ll];
$IIIIIII1III1->$IIIIIIIl1111 = $IIIIIII1IIII;
unset($IIIIIIIl11II[$IIIIIIIl11ll]);
}else {
$IIIIIII1III1 = (Object)Array('LogicalName'=>$IIIIIIIl111l,$IIIIIIIl1111 =>$IIIIIII1IIII);
}
break;
case 'EntityReference':
$IIIIIIIl1111 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('LogicalName')->item(0)->textContent;
$attributeId = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Id')->item(0)->textContent;
$attributeName = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Name')->item(0)->textContent;
$IIIIIII1III1 = (Object)Array('LogicalName'=>$IIIIIIIl1111,
'Id'=>$attributeId,
'Name'=>$attributeName);
break;
case 'OptionSetValue':
$IIIIIII1III1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->textContent;
break;
case 'dateTime':
$IIIIIII1III1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->textContent;
$IIIIIII1III1 = self::parseTime($IIIIIII1III1,'%Y-%m-%dT%H:%M:%SZ');
break;
default:
$IIIIIII1III1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->textContent;
}
if ($IIIIIIIl11Il == NULL) {
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll,$IIIIIIIl11II)) {
$IIIIIIIl1llI[$IIIIIIIl11ll] = (Object)Array('Value'=>$IIIIIII1III1,
'FormattedValue'=>$IIIIIIIl11II[$IIIIIIIl11ll]);
}else {
$IIIIIIIl11II[$IIIIIIIl11ll] = $IIIIIII1III1;
}
}else {
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll,$IIIIIIIl11II)) {
if (isset($IIIIIIIl11II[$IIIIIIIl11ll]->$IIIIIIIl11I1)) {
$IIIIIIIl11II[$IIIIIIIl11ll]->$IIIIIIIl11I1 = (Object)Array(
'Value'=>$IIIIIII1III1,
'FormattedValue'=>$IIIIIIIl11II[$IIIIIIIl11ll]->$IIIIIIIl11I1);
}else {
$IIIIIIIl11II[$IIIIIIIl11ll]->$IIIIIIIl11I1 = $IIIIIII1III1;
}
}else {
$IIIIIII1IIlI = (Object)Array();
foreach ($IIIIIIIl11Il as $IIIIIII1IIll) {$IIIIIII1IIlI->$IIIIIII1IIll = NULL;}
$IIIIIII1IIlI->$IIIIIIIl11I1 = $IIIIIII1III1;
$IIIIIIIl11II[$IIIIIIIl11ll] = $IIIIIII1IIlI;
}
}
}
}
private static function getPageNo($IIIIIIII11Il) {
$IIIIIII1II1I = new DOMDocument();$IIIIIII1II1I->loadXML($IIIIIIII11Il);
$IIIIIIIlllII = $IIIIIII1II1I->documentElement->getAttribute('page');
return (int)$IIIIIIIlllII;
}
private function getOrganizationRetrieveMultipleAction() {
if ($this->IIIIIIIIIllI == NULL) {
$IIIIIIIlIIIl = $this->getAllOrganizationSoapActions();
$this->IIIIIIIIIllI = $IIIIIIIlIIIl['RetrieveMultiple'];
}
return $this->IIIIIIIIIllI;
}
private function getOrganizationRetrieveAction() {
if ($this->IIIIIIIIIlI1 == NULL) {
$IIIIIIIlIIIl = $this->getAllOrganizationSoapActions();
$this->IIIIIIIIIlI1 = $IIIIIIIlIIIl['Retrieve'];
}
return $this->IIIIIIIIIlI1;
}
private function getOrganizationCreateAction() {
if ($this->IIIIIIIIII11 == NULL) {
$IIIIIIIlIIIl = $this->getAllOrganizationSoapActions();
$this->IIIIIIIIII11 = $IIIIIIIlIIIl['Create'];
}
return $this->IIIIIIIIII11;
}
private function getOrganizationDeleteAction() {
if ($this->IIIIIIIIIlII == NULL) {
$IIIIIIIlIIIl = $this->getAllOrganizationSoapActions();
$this->IIIIIIIIIlII = $IIIIIIIlIIIl['Delete'];
}
return $this->IIIIIIIIIlII;
}
private function getOrganizationUpdateAction() {
if ($this->IIIIIIIIIlll == NULL) {
$IIIIIIIlIIIl = $this->getAllOrganizationSoapActions();
$this->IIIIIIIIIlll = $IIIIIIIlIIIl['Update'];
}
return $this->IIIIIIIIIlll;
}
public function isEntityDefinitionCached($IIIIIIIl111l) {
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl111l,$this->IIIIIIIIIl1l)) {
if (self::$IIIIIIIIlI1l) echo "entity definition cached";
return true;
}else {
return false;
}
}
public function setCachedEntityShortDefinition($entitiesDefinitions){
$this->IIIIIIIII111 = $entitiesDefinitions;
$this->IIIIIIIIIl11->set("entities_definitions",$GLOBALS['IIIIIII11l1l']($this->IIIIIIIII111) ,self::$IIIIIIIII11I);
}
public function setCachedEntityDefinition($IIIIIIIl111l,
SimpleXMLElement $IIIIIII1I1ll,Array $IIIIIII1IlIl,Array $IIIIIII1IlI1,
Array $IIIIIII1IllI,Array $IIIIIII1Illl,$IIIIIII1Ill1,$IIIIIII1Il1I,
$IIIIIII1Il1l,$IIIIIII1Il11,$IIIIIII1I1II,
Array $IIIIIII1I1Il,Array $IIIIIII1I1I1,Array $IIIIIII1I1lI) {
$this->IIIIIIIIIl1l[$IIIIIIIl111l] = Array(
$IIIIIII1IlIl,$IIIIIII1IlI1,
$IIIIIII1IllI,$IIIIIII1Illl,$IIIIIII1Ill1,
$IIIIIII1Il1I,$IIIIIII1Il1l,$IIIIIII1Il11,
$IIIIIII1I1II,$IIIIIII1I1Il,$IIIIIII1I1I1,$IIIIIII1I1lI);
$this->IIIIIIIIIl11->set("entities",$GLOBALS['IIIIIII11l1l']($this->IIIIIIIIIl1l) ,self::$IIIIIIIII11I);
}
public function getCachedEntityDefinition($IIIIIIIl111l,
&$IIIIIII1I1ll,Array &$IIIIIII1IlIl,Array &$IIIIIII1IlI1,Array &$IIIIIII1IllI,
Array &$IIIIIII1Illl,&$IIIIIII1Ill1,&$IIIIIII1Il1I,&$IIIIIII1Il1l,
&$IIIIIII1Il11,&$IIIIIII1I1II,Array &$IIIIIII1I1Il,
Array &$IIIIIII1I1I1,Array &$IIIIIII1I1lI) {
if ($this->isEntityDefinitionCached($IIIIIIIl111l)) {
$IIIIIII1IlIl = $this->IIIIIIIIIl1l[$IIIIIIIl111l][0];
$IIIIIII1IlI1 = $this->IIIIIIIIIl1l[$IIIIIIIl111l][1];
$IIIIIII1IllI = $this->IIIIIIIIIl1l[$IIIIIIIl111l][2];
$IIIIIII1Illl = $this->IIIIIIIIIl1l[$IIIIIIIl111l][3];
$IIIIIII1Ill1 = $this->IIIIIIIIIl1l[$IIIIIIIl111l][4];
$IIIIIII1Il1I = $this->IIIIIIIIIl1l[$IIIIIIIl111l][5];
$IIIIIII1Il1l = $this->IIIIIIIIIl1l[$IIIIIIIl111l][6];
$IIIIIII1Il11 = $this->IIIIIIIIIl1l[$IIIIIIIl111l][7];
$IIIIIII1I1II = $this->IIIIIIIIIl1l[$IIIIIIIl111l][8];
$IIIIIII1I1Il = $this->IIIIIIIIIl1l[$IIIIIIIl111l][9];
$IIIIIII1I1I1 = $this->IIIIIIIIIl1l[$IIIIIIIl111l][10];
$IIIIIII1I1lI = $this->IIIIIIIIIl1l[$IIIIIIIl111l][11];
return true;
}else {
$IIIIIII1I1ll = NULL;
$IIIIIII1IlIl = NULL;
$IIIIIII1IlI1 = NULL;
$IIIIIII1IllI = NULL;
$IIIIIII1Illl = NULL;
$IIIIIII1Ill1 = NULL;
$IIIIIII1Il1I = NULL;
$IIIIIII1Il1l = NULL;
$IIIIIII1Il11 = NULL;
$IIIIIII1I1II = NULL;
$IIIIIII1I1Il = NULL;
$IIIIIII1I1I1 = NULL;
$IIIIIII1I1lI = NULL;
return false;
}
}
private function getLoginCache() {
return Array(
$this->discoveryURI,
$this->IIIIIIIIIII1,
$this->IIIIIIIIIIIl->IIIIIIIIIIll,
$this->IIIIIIIII1II,
NULL,
$this->IIIIIIIII1I1,
$this->IIIIIIIII1lI,
NULL,
NULL,
$this->IIIIIIIIII1l,
$this->IIIIIIIIII11,
$this->IIIIIIIIIlII,
$this->IIIIIIIIIlIl,
$this->IIIIIIIIIlI1,
$this->IIIIIIIIIllI,
$this->IIIIIIIIIlll,
NULL,
$this->IIIIIIIIIl1I,
Array(),
self::$IIIIIIIII1l1,
self::$IIIIIIIII11l,);
}
private function loadLoginCache(Array $IIIIIII1I11I) {
list(
$this->discoveryURI,
$this->IIIIIIIIIII1,
$this->organizationURI,
$this->IIIIIIIII1II,
$this->IIIIIIIII1Il,
$this->IIIIIIIII1I1,
$this->IIIIIIIII1lI,
$this->IIIIIIIII1ll,
$this->IIIIIIIIII1I,
$this->IIIIIIIIII1l,
$this->IIIIIIIIII11,
$this->IIIIIIIIIlII,
$this->IIIIIIIIIlIl,
$this->IIIIIIIIIlI1,
$this->IIIIIIIIIllI,
$this->IIIIIIIIIlll,
$this->IIIIIIIIIll1,
$this->IIIIIIIIIl1I,
self::$IIIIIIIII1l1,
self::$IIIIIIIII11l) = $IIIIIII1I11I;
}
private function loadEntityDefinitionCache() {
$entities = $this->IIIIIIIIIl11->get('entities');
if ($entities != null){
$this->IIIIIIIIIl1l = $GLOBALS['IIIIIII1lIIl']($entities);
}
$IIIIIII1lII1 = $this->IIIIIIIIIl11->get('entities_definitions');
if ($IIIIIII1lII1){
$this->IIIIIIIII111 = $GLOBALS['IIIIIII1lIIl']($IIIIIII1lII1);
}
}
public function retrieve(AlexaSDK_Entity $entity,$fieldSet = NULL) {
if ($entity->ID == self::EmptyGUID) {
throw new Exception('Cannot Retrieve an Entity without an ID.');
return FALSE;
}
$IIIIIIII1llI = $this->retrieveRaw($entity,$fieldSet);
$newEntity = self::parseRetrieveResponse($this,$entity->LogicalName,$IIIIIIII1llI);
return $newEntity;
}
public function create(AlexaSDK_Entity &$entity) {
if ($entity->ID != self::EmptyGUID) {
throw new Exception('Cannot Create an Entity that already exists.');
return FALSE;
}
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$createNode = self::generateCreateRequest($entity);
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'Create Request: '.PHP_EOL.$createNode->C14N().PHP_EOL.PHP_EOL;
$createRequest = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationCreateAction(),$securityToken,$createNode);
$IIIIIIII1l11 = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$createRequest);
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'Create Response: '.PHP_EOL.$IIIIIIII1l11.PHP_EOL.PHP_EOL;
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$createResponseNode = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('CreateResponse') as $IIIIIIIl1lIl) {
$createResponseNode = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($createResponseNode == NULL) {
throw new Exception('Could not find CreateResponse node in XML returned from Server');
return FALSE;
}
$IIIIIIlIIII1 = $createResponseNode->getElementsByTagName('CreateResult')->item(0)->textContent;
$entity->ID = $IIIIIIlIIII1;
$entity->reset();
return $IIIIIIlIIII1;
}
protected static function generateCreateRequest(AlexaSDK_Entity $entity) {
$createRequestDOM = new DOMDocument();
$createNode = $createRequestDOM->appendChild($createRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services','Create'));
$createNode->appendChild($createRequestDOM->importNode($entity->getEntityDOM(),true));
return $createNode;
}
public function update(AlexaSDK_Entity &$entity) {
if ($entity->ID == self::EmptyGUID) {
throw new Exception('Cannot Update an Entity without an ID.');
return FALSE;
}
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$updateNode = self::generateUpdateRequest($entity);
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'Update Request: '.PHP_EOL.$updateNode->C14N().PHP_EOL.PHP_EOL;
$updateRequest = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationUpdateAction(),$securityToken,$updateNode);
$IIIIIIII1l11 = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$updateRequest);
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'Update Response: '.PHP_EOL.$IIIIIIII1l11.PHP_EOL.PHP_EOL;
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$updateResponseNode = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('UpdateResponse') as $IIIIIIIl1lIl) {
$updateResponseNode = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($updateResponseNode == NULL) {
throw new Exception('Could not find UpdateResponse node in XML returned from Server');
return FALSE;
}
return $updateResponseNode->C14N();
}
protected static function generateUpdateRequest(AlexaSDK_Entity $entity) {
$updateRequestDOM = new DOMDocument();
$updateNode = $updateRequestDOM->appendChild($updateRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services','Update'));
$updateNode->appendChild($updateRequestDOM->importNode($entity->getEntityDOM(),true));
return $updateNode;
}
public function delete(AlexaSDK_Entity &$entity) {
if ($entity->ID == self::EmptyGUID) {
throw new Exception('Cannot Delete an Entity without an ID.');
return FALSE;
}
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$deleteNode = self::generateDeleteRequest($entity);
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'Delete Request: '.PHP_EOL.$deleteNode->C14N().PHP_EOL.PHP_EOL;
$deleteRequest = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationDeleteAction(),$securityToken,$deleteNode);
$IIIIIIII1l11 = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$deleteRequest);
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'Delete Response: '.PHP_EOL.$IIIIIIII1l11.PHP_EOL.PHP_EOL;
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$deleteResponseNode = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('DeleteResponse') as $IIIIIIIl1lIl) {
$deleteResponseNode = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($deleteResponseNode == NULL) {
throw new Exception('Could not find DeleteResponse node in XML returned from Server');
return FALSE;
}
return TRUE;
}
protected static function generateDeleteRequest(AlexaSDK_Entity $entity) {
$deleteRequestDOM = new DOMDocument();
$deleteNode = $deleteRequestDOM->appendChild($deleteRequestDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services','Delete'));
$deleteNode->appendChild($deleteRequestDOM->createElement('entityName',$entity->logicalName));
$deleteNode->appendChild($deleteRequestDOM->createElement('id',$entity->ID));
return $deleteNode;
}
protected static function generateExecuteActionRequest($IIIIIII1lllI,$IIIIIII1ll1I = NULL,$requestType = NULL){
$IIIIIII1lI1l = new DOMDocument();
$IIIIIII1llll = $IIIIIII1lI1l->appendChild($IIIIIII1lI1l->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services','Execute'));
$IIIIIII1llll->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:i','http://www.w3.org/2001/XMLSchema-instance');
$IIIIIIII1IIl = $IIIIIII1llll->appendChild($IIIIIII1lI1l->createElement('request'));
$IIIIIIII1IIl->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:b','http://schemas.microsoft.com/xrm/2011/Contracts');
if ($requestType){
$IIIIIIII1IIl->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:e','http://schemas.microsoft.com/crm/2011/Contracts');
$IIIIIIII1IIl->setAttribute('i:type','e:'.$requestType);
}
$IIIIIIII1II1 = $IIIIIIII1IIl->appendChild($IIIIIII1lI1l->createElement('b:Parameters'));
$IIIIIIII1II1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:c','http://schemas.datacontract.org/2004/07/System.Collections.Generic');
if ($IIIIIII1ll1I != NULL &&$GLOBALS['IIIIIIIIlII1']($IIIIIII1ll1I)){
foreach ($IIIIIII1ll1I as $parameter){
$IIIIIII1lI1I = $IIIIIIII1II1->appendChild($IIIIIII1lI1l->createElement('b:KeyValuePairOfstringanyType'));
$IIIIIII1lI1I->appendChild($IIIIIII1lI1l->createElement('c:key',$parameter["key"]));
$IIIIIII1llIl = $parameter["value"];
$IIIIIII1llII = NULL;
$IIIIIII1lIlI = $GLOBALS['IIIIIIIl1Il1']($parameter["type"]);
$IIIIIII1lI11 = 'http://www.w3.org/2001/XMLSchema';
switch ($IIIIIII1lIlI) {
case 'memo':
$IIIIIII1lIlI = 'string';
break;
case 'integer':
$IIIIIII1lIlI = 'int';
break;
case 'uniqueidentifier':
$IIIIIII1lIlI = 'guid';
break;
case 'money':
$IIIIIII1lIlI = 'Money';
$IIIIIII1llIl = $IIIIIII1lI1l->createElement('c:Value',$parameter["value"]);
break;
case 'picklist':
case 'state':
case 'status':
$IIIIIII1lIlI = 'OptionSetValue';
$IIIIIII1lI11 = 'http://schemas.microsoft.com/xrm/2011/Contracts';
$IIIIIII1llIl = NULL;
$IIIIIII1llII = $IIIIIII1lI1l->createElement('b:Value',$parameter["value"]);
break;
case 'boolean':
$IIIIIII1llIl = ($parameter["value"]) ?"true": "false";
break;
case 'guid':
$IIIIIII1lIlI = 'guid';
$IIIIIII1lI11 = 'http://schemas.microsoft.com/2003/10/Serialization/';
break;
case 'base64binary':
$IIIIIII1lIlI = 'base64Binary';
break;
case 'string':
case 'int':
case 'decimal':
case 'double':
break;
default:
trigger_error('No Create/Update handling implemented for type '.$IIIIIII1lIlI.' used by field '.$IIIIIII1lIll,
E_USER_WARNING);
}
$IIIIIII1lIl1 = $IIIIIII1lI1I->appendChild($IIIIIII1lI1l->createElement('c:value'));
$IIIIIII1lIl1->setAttribute('i:type','d:'.$IIIIIII1lIlI);
$IIIIIII1lIl1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:d',$IIIIIII1lI11);
if ($IIIIIII1llII != NULL) {
$IIIIIII1lIl1->appendChild($IIIIIII1llII);
}
if ($IIIIIII1llIl != NULL) {
$IIIIIII1lIl1->appendChild(new DOMText($IIIIIII1llIl));
}
}
}
$IIIIIII1llI1 = $IIIIIIII1IIl->appendChild($IIIIIII1lI1l->createElement('b:RequestId'));
$IIIIIII1llI1->setAttribute('i:nil','true');
$IIIIIIII1IIl->appendChild($IIIIIII1lI1l->createElement('b:RequestName',$IIIIIII1lllI));
return $IIIIIII1llll;
}
public function executeAction($IIIIIII1lllI,$IIIIIII1ll1I = NULL){
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$IIIIIII1llll = self::generateExecuteActionRequest($IIIIIII1lllI,$IIIIIII1ll1I);
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'ExecuteAction Request: '.PHP_EOL.$IIIIIII1llll->C14N().PHP_EOL.PHP_EOL;
$IIIIIII1ll1l = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationExecuteAction(),$securityToken,$IIIIIII1llll);
$IIIIIIII1l11 = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$IIIIIII1ll1l);
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'ExecuteAction Response: '.PHP_EOL.$IIIIIIII1l11.PHP_EOL.PHP_EOL;
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$IIIIIII1ll11 = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('ExecuteResult') as $IIIIIIIl1lIl) {
$IIIIIII1ll11 = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($IIIIIII1ll11 == NULL) {
throw new Exception('Could not find ExecuteResult node in XML returned from Server');
return FALSE;
}
$IIIIIII1l1II = Array();
foreach( $IIIIIII1ll11->getElementsByTagName('KeyValuePairOfstringanyType') as $IIIIIIIl11lI){
$IIIIIII1l1II[$IIIIIIIl11lI->getElementsByTagName('key')->item(0)->textContent] = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->textContent;
}
$IIIIIIIl1llI = (Object)$IIIIIII1l1II;
return $IIIIIIIl1llI;
}
public function retrieveMetadataChanges(){
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$IIIIIII1l1I1 = self::generateRetrieveMetadataChangesRequest();
$IIIIIII1l1lI = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationExecuteAction(),$securityToken,$IIIIIII1l1I1);
$IIIIIIII1l11 = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$IIIIIII1l1lI);
}
private static function generateRetrieveMetadataChangesRequest(){
$IIIIIII1l1l1 = new DOMDocument();
$IIIIIII1l11I = $IIIIIII1l1l1->appendChild($IIIIIII1l1l1->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts/Services','Execute'));
$IIIIIII1l11I->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:i','http://www.w3.org/2001/XMLSchema-instance');
$IIIIIIII1IIl = $IIIIIII1l11I->appendChild($IIIIIII1l1l1->createElement('request'));
$IIIIIIII1IIl->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:b','http://schemas.microsoft.com/xrm/2011/Contracts');
$IIIIIIII1II1 = $IIIIIIII1IIl->appendChild($IIIIIII1l1l1->createElement('b:Parameters'));
$IIIIIIII1II1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:c','http://schemas.datacontract.org/2004/07/System.Collections.Generic');
$IIIIIII1lI1I = $IIIIIIII1II1->appendChild($IIIIIII1l1l1->createElement('b:KeyValuePairOfstringanyType'));
$IIIIIII1lI1I->appendChild($IIIIIII1l1l1->createElement('c:key',"Query"));
$IIIIIII1lIl1 = $IIIIIII1lI1I->appendChild($IIIIIII1l1l1->createElement('c:value'));
$IIIIIII1lIl1->setAttribute('i:type','d:EntityQueryExpression');
$IIIIIII1lIl1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:d','http://schemas.microsoft.com/xrm/2011/Metadata/Query');
$IIIIIII1lIl1->appendChild(new DOMText(""));
$IIIIIII1llI1 = $IIIIIIII1IIl->appendChild($IIIIIII1l1l1->createElement('b:RequestId'));
$IIIIIII1llI1->setAttribute('i:nil','true');
$IIIIIIII1IIl->appendChild($IIIIIII1l1l1->createElement('b:RequestName',"RetrieveMetadataChanges"));
return $IIIIIII1l11I;
}
function retrieveAllEntities(){
if (!empty($this->IIIIIIIII111)){
return $this->IIIIIIIII111;
}
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$request = '<Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                        <request i:type="b:RetrieveAllEntitiesRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                            <b:Parameters xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
                                <b:KeyValuePairOfstringanyType>
                                    <c:key>EntityFilters</c:key>
                                    <c:value i:type="d:EntityFilters" xmlns:d="http://schemas.microsoft.com/xrm/2011/Metadata">Entity</c:value>
                                </b:KeyValuePairOfstringanyType>
                                <b:KeyValuePairOfstringanyType>
                                    <c:key>RetrieveAsIfPublished</c:key>
                                    <c:value i:type="d:boolean" xmlns:d="http://www.w3.org/2001/XMLSchema">false</c:value>
                                </b:KeyValuePairOfstringanyType>
                            </b:Parameters>
                            <b:RequestId i:nil="true"/>
                            <b:RequestName>RetrieveAllEntities</b:RequestName>
                        </request>
                    </Execute>';
$IIIIIII11III = new DOMDocument();
$IIIIIII11III->loadXML($request);
$IIIIIIII1III = $IIIIIII11III->getElementsByTagName('Execute')->item(0);
$IIIIIIII1l1l = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationExecuteAction(),$securityToken,$IIIIIIII1III);
$IIIIIII11IIl = new DOMDocument();
$IIIIIII11IIl->loadXML($IIIIIIII1l1l);
$IIIIIIII1llI = $this->getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$IIIIIIII1l1l);
$IIIIIII11II1 = self::parseRetrieveAllEntitiesResponse($this,$IIIIIIII1llI,false);
$this->setCachedEntityShortDefinition($IIIIIII11II1);
return $IIIIIII11II1;
}
protected static function getSTSidentifier(DOMNode $IIIIIII11Ill){
$IIIIIII11Il1 = NULL;
if ($IIIIIII11Ill->getElementsByTagName('SecureTokenService')->length == 0) {
throw new Exception('Could not find SecureTokenService tag in provided security policy XML');
return FALSE;
}
$IIIIIII11I1I = $IIIIIII11Ill->getElementsByTagName('SecureTokenService')->item(0);
if ($IIIIIII11I1I->getElementsByTagName('Identifier')->length == 0) {
throw new Exception('Could not find SecureTokenService/Identifier tag in provided security policy XML');
return FALSE;
}
$IIIIIII11I1l = $IIIIIII11I1I->getElementsByTagName('Identifier')->item(0);
$IIIIIII11Il1 = $IIIIIII11I1l->textContent;
if ($IIIIIII11Il1 == NULL) {
throw new Exception('Could not find SecurityTokenServiceIdentifier in provided security policy WSDL');
return FALSE;
}
return $IIIIIII11Il1;
}
public function importSolution($IIIIIII11lII,$IIIIIII11lIl,$IIIIIII11lI1){
if (!$GLOBALS['IIIIIII11llI']($IIIIIII11lII)){
$IIIIIII11lII = $GLOBALS['IIIIIII11lll']($IIIIIII11lII);
}
$IIIIIII1ll1I = array(
array('key'=>'OverwriteUnmanagedCustomizations',
'value'=>$IIIIIII11lIl,
'type'=>'boolean'
),
array('key'=>'PublishWorkflows',
'value'=>$IIIIIII11lI1,
'type'=>'boolean'
),
array('key'=>'CustomizationFile',
'value'=>$IIIIIII11lII,
'type'=>'base64Binary'
),
array('key'=>'ImportJobId',
'value'=>self::EmptyGUID,
'type'=>'guid'
),
);
$securityToken = $this->IIIIIIIIIIII->getOrganizationSecurityToken();
$IIIIIII1llll = self::generateExecuteActionRequest('ImportSolution',$IIIIIII1ll1I,'ImportSolutionRequest');
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'ExecuteAction Request: '.PHP_EOL.$IIIIIII1llll->C14N().PHP_EOL.PHP_EOL;
$IIIIIII1ll1l = $this->generateSoapRequest($this->IIIIIIIIIIIl->IIIIIIIIIIll,$this->getOrganizationExecuteAction(),$securityToken,$IIIIIII1llll);
$IIIIIIII1l11 = self::getSoapResponse($this->IIIIIIIIIIIl->IIIIIIIIIIll,$IIIIIII1ll1l,true);
if (self::$IIIIIIIIlI1l) echo PHP_EOL.'ExecuteAction Response: '.PHP_EOL.$IIIIIIII1l11.PHP_EOL.PHP_EOL;
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$IIIIIII1ll11 = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('ExecuteResult') as $IIIIIIIl1lIl) {
$IIIIIII1ll11 = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($IIIIIII1ll11 == NULL) {
throw new Exception('Could not find ExecuteResult node in XML returned from Server');
return FALSE;
}
$IIIIIII1l1II = Array();
foreach( $IIIIIII1ll11->getElementsByTagName('KeyValuePairOfstringanyType') as $IIIIIIIl11lI){
$IIIIIII1l1II[$IIIIIIIl11lI->getElementsByTagName('key')->item(0)->textContent] = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->textContent;
}
$IIIIIIIl1llI = (Object)$IIIIIII1l1II;
return $IIIIIIIl1llI;
}
public function setCachedSecurityToken($service,$IIIIIII11l1I){
if ($this->IIIIIIIIIl11){
$this->IIIIIIIIIl11->set($GLOBALS['IIIIIIIl1Il1']($service)."securitytoken",$GLOBALS['IIIIIII11l1l']($IIIIIII11l1I),$GLOBALS['IIIIIII11l11']($IIIIIII11l1I['expiryTime'] -$GLOBALS['IIIIIII111II']()));
}
}
public function getCachedSecurityToken($service,&$securityToken){
if ($this->IIIIIIIIIl11){
$IIIIIII11l1I = $this->IIIIIIIIIl11->get($GLOBALS['IIIIIIIl1Il1']($service)."securitytoken");
if ($IIIIIII11l1I != NULL){
$securityToken = $GLOBALS['IIIIIII1lIIl']($IIIIIII11l1I);
return TRUE;
}
}
$securityToken = NULL;
return FALSE;
}
}
?>
