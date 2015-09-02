<?php $OOO000000=urldecode('%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64');$GLOBALS['OOO0000O0']=$OOO000000{4}.$OOO000000{9}.$OOO000000{3}.$OOO000000{5}.$OOO000000{2}.$OOO000000{10}.$OOO000000{13}.$OOO000000{16};$GLOBALS['OOO0000O0'].=$GLOBALS['OOO0000O0']{3}.$OOO000000{11}.$OOO000000{12}.$GLOBALS['OOO0000O0']{7}.$OOO000000{5};?><?php eval($GLOBALS['OOO0000O0']('JEdMT0JBTFNbJ0lJSUlJSWxJMUlsbCddPSdkYXRlJzskR0xPQkFMU1snSUlJSUlJbEkxMTFJJ109J3NvcnQnOyRHTE9CQUxTWydJSUlJSUlsSTExbDEnXT0nYXJyYXlfa2V5cyc7JEdMT0JBTFNbJ0lJSUlJSUlsMTExSSddPSdleHBsb2RlJzskR0xPQkFMU1snSUlJSUlJbEkxSTExJ109J3N0cnBvcyc7JEdMT0JBTFNbJ0lJSUlJSWxJbGwxSSddPSdnbWRhdGUnOyRHTE9CQUxTWydJSUlJSUlJSWxJSTEnXT0naXNfYXJyYXknOyRHTE9CQUxTWydJSUlJSUlsSWwxMTEnXT0ncmVzZXQnOyRHTE9CQUxTWydJSUlJSUlsSWxJSUknXT0naXNfaW50JzskR0xPQkFMU1snSUlJSUlJbElJMTFJJ109J2lzX3N0cmluZyc7JEdMT0JBTFNbJ0lJSUlJSWxJSTFsbCddPSdpbXBsb2RlJzskR0xPQkFMU1snSUlJSUlJbElJMWxJJ109J2luX2FycmF5JzskR0xPQkFMU1snSUlJSUlJbElJMUlJJ109J3N0cnRvdXBwZXInOyRHTE9CQUxTWydJSUlJSUlJbDFJbDEnXT0nc3RydG9sb3dlcic7JEdMT0JBTFNbJ0lJSUlJSUkxSUlJbCddPSdhcnJheV9rZXlfZXhpc3RzJzs=')); ?><?php 
class AlexaSDK_Entity extends AlexaSDK_Abstract {
protected $IIIIIIIl111l = NULL;
private $IIIIIIlIIII1;
protected $IIIIIII1Ill1 = NULL;
protected $IIIIIII1Il1l = NULL;
protected $IIIIIII1Il11 = NULL;
protected $IIIIIII1I1II = NULL;
protected $IIIIIII1Il1I = NULL;
public $IIIIIII1I1ll;
private $properties = Array();
private $IIIIIIll11II = Array();
private $optionSets = Array();
protected $localProperties = Array();
private $propertyValues = Array();
private $IIIIIIlI1IIl = Array();
private $IIIIIII1I1Il = Array();
private $IIIIIII1I1I1 = Array();
private $IIIIIII1I1lI = Array();
public $fieldValidation = TRUE;
protected $validator = NULL;
public $errors = Array();
private $entityDomain = NULL;
private $IIIIIIlIll11;
function __construct(AlexaSDK $IIIIIIllI1I1,$IIIIIIlIIIlI = NULL,$IIIIIIlIIIl1 = NULL) {
$this->IIIIIIlIll11 = $IIIIIIllI1I1;
if ($IIIIIIlIIIlI != NULL &&$IIIIIIlIIIlI != $this->IIIIIIIl111l) {
if ($this->IIIIIIIl111l != NULL) {
throw new Exception('Cannot override the Entity Logical Name on a strongly typed Entity');
}
$this->IIIIIIIl111l = $IIIIIIlIIIlI;
}
if ($this->IIIIIIIl111l == NULL) {
throw new Execption('Cannot instantiate an abstract Entity - specify the Logical Name');
}
$this->setEntityDomain($this->IIIIIIlIll11);
$this->validator = new AlexaSDK_FormValidator();
if ($this->IIIIIIlIll11->isEntityDefinitionCached($this->IIIIIIIl111l)) {
$IIIIIIlIIIll = $this->IIIIIIlIll11->getCachedEntityDefinition($this->IIIIIIIl111l,
$this->IIIIIII1I1ll,$this->properties,$this->propertyValues,$this->IIIIIIll11II,
$this->optionSets,$this->IIIIIII1Ill1,$this->IIIIIII1Il1I,$this->IIIIIII1Il1l,
$this->IIIIIII1Il11,$this->IIIIIII1I1II,$this->IIIIIII1I1Il,
$this->IIIIIII1I1I1,$this->IIIIIII1I1lI);
if (self::$IIIIIIIIlI1l){echo "Cached ".$this->IIIIIIIl111l;}
if ($IIIIIIlIIIll){
if ($IIIIIIlIIIl1 != NULL) {
$this->setID($IIIIIIlIIIl1);
$IIIIIIII1llI = $this->IIIIIIlIll11->retrieveRaw($this);
$this->ParseRetrieveResponse($this->IIIIIIlIll11,$this->LogicalName,$IIIIIIII1llI);
}
return;
}
}
$this->IIIIIII1I1ll = $this->IIIIIIlIll11->retrieveEntity($this->IIIIIIIl111l);
if (!$this->IIIIIII1I1ll) {
throw new Execption('Unable to load metadata simple_xml_class'.$this->IIIIIII1I1ll);
}
$this->IIIIIII1I1II = (String)$this->IIIIIII1I1ll->Description->LocalizedLabels->LocalizedLabel->Label;
$this->IIIIIII1Il1l = (String)$this->IIIIIII1I1ll->DisplayName->LocalizedLabels->LocalizedLabel->Label;
$this->IIIIIII1Il11 = (String)$this->IIIIIII1I1ll->DisplayCollectionName->LocalizedLabels->LocalizedLabel->Label;
$this->IIIIIII1Il1I = (String)$this->IIIIIII1I1ll->ObjectTypeCode;
foreach ($this->IIIIIII1I1ll->Attributes[0]->AttributeMetadata as $IIIIIIIIll11) {
$IIIIIIlIII1I = $IIIIIIIIll11->attributes('http://www.w3.org/2001/XMLSchema-instance');
$IIIIIIlIII1l = self::stripNS($IIIIIIlIII1I['type']);
$isLookup = ($IIIIIIlIII1l == 'LookupAttributeMetadata');
if ($isLookup) {
$lookupTypes = Array();
foreach ($IIIIIIIIll11->Targets->string as $target) {
array_push($lookupTypes,(string)$target);
}
}else {
$lookupTypes = NULL;
}
$IIIIIIlIIl1I = (String)$IIIIIIIIll11->RequiredLevel->Value;
if (!empty($IIIIIIIIll11->OptionSet) &&!empty($IIIIIIIIll11->OptionSet->Name)) {
$IIIIIIlIIlll = (String)$IIIIIIIIll11->OptionSet->Name;
$optionSetGlobal = ($IIIIIIIIll11->OptionSet->IsGlobal == 'true');
$IIIIIIlIIlII = (String)$IIIIIIIIll11->OptionSet->OptionSetType;
$IIIIIIlIIlIl = Array();
if (self::$IIIIIIIIlI1l) {
}
switch ($IIIIIIlIIlII) {
case 'Boolean':
$value = (int)$IIIIIIIIll11->OptionSet->FalseOption->Value;
$label = (String)$IIIIIIIIll11->OptionSet->FalseOption->Label->UserLocalizedLabel->Label[0];
$IIIIIIlIIlIl[$value] = $label;
$value = (int)$IIIIIIIIll11->OptionSet->TrueOption->Value;
$label = (String)$IIIIIIIIll11->OptionSet->TrueOption->Label->UserLocalizedLabel->Label[0];
$IIIIIIlIIlIl[$value] = $label;
break;
case 'State':
foreach ($IIIIIIIIll11->OptionSet->Options->OptionMetadata as $IIIIIIll1ll1) {
$value = (int)$IIIIIIll1ll1->Value;
$label = (String)$IIIIIIll1ll1->Label->UserLocalizedLabel->Label[0];
if ($GLOBALS['IIIIIII1IIIl']($value,$IIIIIIlIIlIl)) {
trigger_error('Option '.$label.' of OptionSet '.$IIIIIIlIIlll.' used by field '.(String)$IIIIIIIIll11->SchemaName.' has the same Value as another Option in this Set',
E_USER_WARNING);
}else {
$IIIIIIlIIlIl[$value] = $label;
}
}
break;
case 'Status':
foreach ($IIIIIIIIll11->OptionSet->Options->OptionMetadata as $IIIIIIll1ll1) {
$value = (int)$IIIIIIll1ll1->Value;
$label = (String)$IIIIIIll1ll1->Label->UserLocalizedLabel->Label[0];
if ($GLOBALS['IIIIIII1IIIl']($value,$IIIIIIlIIlIl)) {
trigger_error('Option '.$label.' of OptionSet '.$IIIIIIlIIlll.' used by field '.(String)$IIIIIIIIll11->SchemaName.' has the same Value as another Option in this Set',
E_USER_WARNING);
}else {
$IIIIIIlIIlIl[$value] = $label;
}
}
break;
case 'Picklist':
foreach ($IIIIIIIIll11->OptionSet->Options->OptionMetadata as $IIIIIIll1ll1) {
$value = (int)$IIIIIIll1ll1->Value;
$label = (String)$IIIIIIll1ll1->Label->UserLocalizedLabel->Label[0];
if ($GLOBALS['IIIIIII1IIIl']($value,$IIIIIIlIIlIl)) {
trigger_error('Option '.$label.' of OptionSet '.$IIIIIIlIIlll.' used by field '.(String)$IIIIIIIIll11->SchemaName.' has the same Value as another Option in this Set',
E_USER_WARNING);
}else {
$IIIIIIlIIlIl[$value] = $label;
}
}
break;
default:
echo "DEFAULTOPTIONSET";
trigger_error('No OptionSet handling implemented for Type '.$IIIIIIlIIlII.' used by field '.(String)$IIIIIIIIll11->SchemaName.' in Entity '.$this->IIIIIIIl111l,
E_USER_WARNING);
}
if (self::$IIIIIIIIlI1l) {
}
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIlIIlll,$this->optionSets)) {
if (!$optionSetGlobal) {
trigger_error('OptionSet '.$IIIIIIlIIlll.' used by field '.(String)$IIIIIIIIll11->SchemaName.' has a name clash with another OptionSet in Entity '.$this->IIIIIIIl111l,
E_USER_WARNING);
}
}else {
$this->optionSets[$IIIIIIlIIlll] = $IIIIIIlIIlIl;
}
}else {
$IIIIIIlIIlll = NULL;
}
if ((String)$IIIIIIIIll11->IsPrimaryName === 'true') {
$this->IIIIIII1Ill1 = $GLOBALS['IIIIIIIl1Il1']((String)$IIIIIIIIll11->LogicalName);
}
$this->properties[$GLOBALS['IIIIIIIl1Il1']((String)$IIIIIIIIll11->LogicalName)] = Array(
'Label'=>(String)$IIIIIIIIll11->DisplayName->UserLocalizedLabel->Label,
'Description'=>(String)$IIIIIIIIll11->Description->UserLocalizedLabel->Label,
'Format'=>(String)$IIIIIIIIll11->Format,
'MaxLength'=>(String)$IIIIIIIIll11->MaxLength,
'ImeMode'=>(String)$IIIIIIIIll11->ImeMode,
'isCustom'=>((String)$IIIIIIIIll11->IsCustomAttribute === 'true'),
'isPrimaryId'=>((String)$IIIIIIIIll11->IsPrimaryId === 'true'),
'isPrimaryName'=>((String)$IIIIIIIIll11->IsPrimaryName === 'true'),
'Type'=>(String)$IIIIIIIIll11->AttributeType,
'isLookup'=>$isLookup,
'lookupTypes'=>$lookupTypes,
'Create'=>((String)$IIIIIIIIll11->IsValidForCreate === 'true'),
'Update'=>((String)$IIIIIIIIll11->IsValidForUpdate === 'true'),
'Read'=>((String)$IIIIIIIIll11->IsValidForRead === 'true'),
'RequiredLevel'=>$IIIIIIlIIl1I,
'AttributeOf'=>(String)$IIIIIIIIll11->AttributeOf,
'OptionSet'=>$IIIIIIlIIlll,
);
if (self::$IIIIIIIIlI1l) {
}
$this->propertyValues[$GLOBALS['IIIIIIIl1Il1']((String)$IIIIIIIIll11->LogicalName)] = Array(
'Value'=>NULL,
'Changed'=>false,
);
if ($IIIIIIlIIl1I != 'None'&&$IIIIIIlIIl1I != 'Recommended') {
$this->IIIIIIll11II[$GLOBALS['IIIIIIIl1Il1']((String)$IIIIIIIIll11->LogicalName)] = $IIIIIIlIIl1I;
}
}
foreach($this->IIIIIII1I1ll->OneToManyRelationships->OneToManyRelationshipMetadata as $IIIIIIlIIl1l){
$this->IIIIIII1I1lI[(string)$IIIIIIlIIl1l->ReferencingEntity] = "";
}
$this->IIIIIIlIll11->setCachedEntityDefinition($this->IIIIIIIl111l,
$this->IIIIIII1I1ll,$this->properties,$this->propertyValues,$this->IIIIIIll11II,
$this->optionSets,$this->IIIIIII1Ill1,$this->IIIIIII1Il1I,$this->IIIIIII1Il1l,
$this->IIIIIII1Il11,$this->IIIIIII1I1II,$this->IIIIIII1I1Il,
$this->IIIIIII1I1I1,$this->IIIIIII1I1lI);
if ($IIIIIIlIIIl1 != NULL) {
$this->setID($IIIIIIlIIIl1);
$IIIIIIII1llI = $this->IIIIIIlIll11->retrieveRaw($this);
$this->ParseRetrieveResponse($this->IIIIIIlIll11,$this->LogicalName,$IIIIIIII1llI);
}
return;
}
public function __get($IIIIIII1lIll) {
switch ($GLOBALS['IIIIIIlII1II']($IIIIIII1lIll)) {
case 'ID':
return $this->getID();
break;
case 'LOGICALNAME':
return $this->IIIIIIIl111l;
break;
case 'DISPLAYNAME':
if ($this->IIIIIII1Ill1 != NULL) {
$IIIIIII1lIll = $this->IIIIIII1Ill1;
}else {
return NULL;
}
break;
case 'OBJECTTYPECODE':
case 'ENTITYTYPECODE':
if ($this->IIIIIII1Il1I != NULL) {
return $this->IIIIIII1Il1I;
}else {
return NULL;
}
break;
case 'ONETOMANYRELATIONSHIPS':
return $this->IIIIIII1I1lI;
break;
case 'OPTIONSETS':
return $this->optionSets;
break;
case 'PROPERTIES':
return $this->properties;
break;
case 'ENTITYNAME':
if ($this->IIIIIII1Il1l != NULL) {
return $this->IIIIIII1Il1l;
}else {
return NULL;
}
break;
case 'ENTITYDESCRIPTION':
if ($this->IIIIIII1I1II != NULL) {
return $this->IIIIIII1I1II;
}else {
return NULL;
}
break;
case 'ENTITIESNAMES':
case 'ENTITYCOLLECTIONNAME':
if ($this->IIIIIII1Il11 != NULL) {
return $this->IIIIIII1Il11;
}else {
return NULL;
}
break;
}
$IIIIIII1lIll = $GLOBALS['IIIIIIIl1Il1']($IIIIIII1lIll);
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->properties) &&$this->properties[$IIIIIII1lIll]['Read'] === true) {
return $this->propertyValues[$IIIIIII1lIll]['Value'];
}
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->localProperties) &&$this->localProperties[$IIIIIII1lIll]['Read'] === true) {
return $this->propertyValues[$IIIIIII1lIll]['Value'];
}
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->properties) ||$GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->localProperties)) {
trigger_error('Property '.$IIIIIII1lIll.' of the '.$this->IIIIIIIl111l.' entity is not Readable',E_USER_NOTICE);
return NULL;
}
$IIIIIIlII1Il = debug_backtrace();
trigger_error('Undefined property via __get(): '.$IIIIIII1lIll 
.' in '.$IIIIIIlII1Il[0]['file'] .' on line '.$IIIIIIlII1Il[0]['line'],
E_USER_NOTICE);
return NULL;
}
public function __set($IIIIIII1lIll,$value) {
switch ($GLOBALS['IIIIIIlII1II']($IIIIIII1lIll)) {
case 'ID':
$this->setID($value);
return;
case 'DISPLAYNAME':
if ($this->IIIIIII1Ill1 != NULL) {
$IIIIIII1lIll = $this->IIIIIII1Ill1;
}else {
return;
}
break;
}
$IIIIIII1lIll = $GLOBALS['IIIIIIIl1Il1']($IIIIIII1lIll);
if (!$GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->properties)) {
$IIIIIIlII1Il = debug_backtrace();
trigger_error('Undefined property via __set() - '.$this->IIIIIIIl111l .' does not support property: '.$IIIIIII1lIll 
.' in '.$IIIIIIlII1Il[0]['file'] .' on line '.$IIIIIIlII1Il[0]['line'],
E_USER_NOTICE);
return;
}
if ($this->properties[$IIIIIII1lIll]['Create'] == false &&$this->properties[$IIIIIII1lIll]['Update'] == false) {
trigger_error('Property '.$IIIIIII1lIll.' of the '.$this->IIIIIIIl111l
.' entity cannot be set',E_USER_NOTICE);
return;
}
if ($this->fieldValidation == TRUE){
$this->validate($IIIIIII1lIll,$value);
}
if ($this->properties[$IIIIIII1lIll]['isLookup'] &&$value != null) {
if (!$value instanceOf self) {
$IIIIIIlII1Il = debug_backtrace();
throw new Exception('Property '.$IIIIIII1lIll.' of the '.$this->IIIIIIIl111l
.' entity must be a object of '.get_class()
.' class in '.$IIIIIIlII1Il[0]['file'] .' on line '.$IIIIIIlII1Il[0]['line'],
E_USER_ERROR);
return;
}
if (!$GLOBALS['IIIIIIlII1lI']($value->IIIIIIIl111l,$this->properties[$IIIIIII1lIll]['lookupTypes'])) {
$IIIIIIlII1Il = debug_backtrace();
throw new Exception('Property '.$IIIIIII1lIll.' of the '.$this->IIIIIIIl111l
.' entity must be a '.$GLOBALS['IIIIIIlII1ll'](' or ',$this->properties[$IIIIIII1lIll]['lookupTypes'])
.' in '.$IIIIIIlII1Il[0]['file'] .' on line '.$IIIIIIlII1Il[0]['line'],
E_USER_ERROR);
return;
}
$this->clearAttributesOf($IIIIIII1lIll);
}
if ($this->properties[$IIIIIII1lIll]['OptionSet'] != NULL) {
$IIIIIIlIIlll = $this->properties[$IIIIIII1lIll]['OptionSet'];
$IIIIIIlII1l1 = NULL;
if ($GLOBALS['IIIIIIlII11I']($value)) {
foreach ($this->optionSets[$IIIIIIlIIlll] as $IIIIIIlII11l =>$IIIIIIlII111) {
if (strcasecmp($value,$IIIIIIlII111) == 0) {
$IIIIIIlII1l1 = new AlexaSDK_OptionSetValue($IIIIIIlII11l,$IIIIIIlII111);
break;
}else{
if ($GLOBALS['IIIIIII1IIIl']($value,$this->optionSets[$IIIIIIlIIlll])) {
$IIIIIIlII1l1 = $value;
}
}
}
}
if ($GLOBALS['IIIIIIlIlIII']($value)) {
if ($GLOBALS['IIIIIII1IIIl']($value,$this->optionSets[$IIIIIIlIIlll])) {
$IIIIIIlII1l1 = new AlexaSDK_OptionSetValue($value,$this->optionSets[$IIIIIIlIIlll][$value]);
}else{
if ($GLOBALS['IIIIIII1IIIl']($value,$this->optionSets[$IIIIIIlIIlll])) {
$IIIIIIlII1l1 = $value;
}
}
}
if ($value instanceof AlexaSDK_OptionSetValue) {
if ($GLOBALS['IIIIIII1IIIl']($value->Value,$this->optionSets[$IIIIIIlIIlll])) {
$IIIIIIlII1l1 = $value;
}
}
if ($IIIIIIlII1l1 != NULL) {
$value = $IIIIIIlII1l1;
$this->clearAttributesOf($IIIIIII1lIll);
}elseif($value == ""||$value == NULL) {
$value = NULL;
}else {
$IIIIIIlII1Il = debug_backtrace();
trigger_error('Property '.$IIIIIII1lIll.' of the '.$this->IIIIIIIl111l
.' entity must be a valid OptionSetValue of type '.$IIIIIIlIIlll
.' in '.$IIIIIIlII1Il[0]['file'] .' on line '.$IIIIIIlII1Il[0]['line'],
E_USER_WARNING);
return;
}
}
if ($this->propertyValues[$IIIIIII1lIll]['Value'] != $value){
$this->propertyValues[$IIIIIII1lIll]['Value'] = $value;
$this->propertyValues[$IIIIIII1lIll]['Changed'] = true;
}
}
public function validate($IIIIIII1lIll,$value) {
$errorsFound = false;
if (isset($this->IIIIIIll11II[$IIIIIII1lIll]) &&!$value){
$this->errors[$IIIIIII1lIll] = $this->getPropertyLabel($IIIIIII1lIll)." is required";
}
switch($this->properties[$IIIIIII1lIll]["Type"]){
case "String":
if ($this->properties[$IIIIIII1lIll]["MaxLength"] &&(strlen($value) >$this->properties[$IIIIIII1lIll]["MaxLength"])){
$this->errors[$IIIIIII1lIll] = "Must be less than ".$this->properties[$IIIIIII1lIll]['MaxLength']." characters";
}
switch($this->properties[$IIIIIII1lIll]["Format"]){
case "Text":
if ($value &&!$this->validator->validateItem($value,'anything')){
$this->errors[$IIIIIII1lIll] = "Incorrect text value";
}
break;
case "Email":
if ($value &&!$this->validator->validateItem($value,'email')){
$this->errors[$IIIIIII1lIll] = "Incorrect email";
}
break;
default:
break;
}
break;
case "Boolean":
break;
case "Picklist":
break;
case "Lookup":
break;
case "Integer":
if ($value &&!$this->validator->validateItem($value,'amount')){
$this->errors[$IIIIIII1lIll] = "Incorrect text value";
}
break;
case "Double":
if ($value &&!$this->validator->validateItem($value,'float')){
$this->errors[$IIIIIII1lIll] = "Incorrect text value";
}
break;
case "Money":
if ($value &&!$this->validator->validateItem($value,'number')){
$this->errors[$IIIIIII1lIll] = "Incorrect text value";
}
break;
case "Memo":
break;
default:
if (!$this->properties[$IIIIIII1lIll]["isLookup"]){
}
break;
}
if (!isset($this->errors[$IIIIIII1lIll])){
$errorsFound = true;
}
return $errorsFound;
}
public function __isset($IIIIIII1lIll) {
switch ($GLOBALS['IIIIIIlII1II']($IIIIIII1lIll)) {
case 'ID':
return ($this->IIIIIIlIIII1 == NULL);
break;
case 'LOGICALNAME':
return true;
break;
case 'DISPLAYNAME':
if ($this->IIIIIII1Ill1 != NULL) {
$IIIIIII1lIll = $this->IIIIIII1Ill1;
}else {
return false;
}
break;
}
$IIIIIII1lIll = $GLOBALS['IIIIIIIl1Il1']($IIIIIII1lIll);
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->properties) &&$this->properties[$IIIIIII1lIll]['Read'] === true) {
return true;
}
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->localProperties) &&$this->localProperties[$IIIIIII1lIll]['Read'] === true) {
return true;
}
return false;
}
private function clearAttributesOf($baseProperty) {
foreach ($this->properties as $IIIIIII1lIll =>$IIIIIIlIlll1) {
if ($IIIIIIlIlll1['AttributeOf'] == $baseProperty) {
$this->propertyValues[$IIIIIII1lIll]['Value'] = NULL;
}
}
}
public function __toString() {
if ($this->IIIIIII1Ill1 != NULL) {
$IIIIIII1Ill1 = $this->DisplayName;
}else {
$IIIIIII1Ill1 = '';
}
return $IIIIIII1Ill1;
}
public function reset() {
foreach ($this->propertyValues as &$IIIIIII1lIll) {
$IIIIIII1lIll['Changed'] = false;
}
}
public function isChanged($IIIIIII1lIll) {
$IIIIIII1lIll = $GLOBALS['IIIIIIIl1Il1']($IIIIIII1lIll);
if (!$GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->propertyValues)) {
$IIIIIIlII1Il = debug_backtrace();
trigger_error('Undefined property via isChanged(): '.$IIIIIII1lIll
.' in '.$IIIIIIlII1Il[0]['file'] .' on line '.$IIIIIIlII1Il[0]['line'],
E_USER_NOTICE);
return;
}
return $this->propertyValues[$IIIIIII1lIll]['Changed'];
}
public function getChangedPropertyValues(){
$IIIIIIlIlII1 = array();
foreach($this->propertyValues as $IIIIIIlIlIlI =>$IIIIIIlIlIll){
if ($IIIIIIlIlIll['Changed']){
$IIIIIIlIlII1[$IIIIIIlIlIlI] = $IIIIIIlIlIll;
}
}
return $IIIIIIlIlII1;
}
private function getID() {
if ($this->IIIIIIlIIII1 == NULL) return self::EmptyGUID;
else return $this->IIIIIIlIIII1;
}
private function setID($value) {
if ($this->IIIIIIlIIII1 != NULL) {
throw new Exception('Cannot change the ID of an Entity');
}
$this->IIIIIIlIIII1 = $value;
}
public function checkMandatories(Array &$IIIIIIlIllII = NULL) {
$IIIIIIlIlI11 = true;
$IIIIIIlIlI1I = Array();
foreach ($this->IIIIIIll11II as $IIIIIII1lIll =>$IIIIIIlIlI1l) {
if ($this->properties[$IIIIIII1lIll]['AttributeOf'] != NULL) {
$IIIIIIlIlIl1 = $this->properties[$IIIIIII1lIll]['AttributeOf'];
}else {
$IIIIIIlIlIl1 = $IIIIIII1lIll;
}
if ($this->propertyValues[$IIIIIIlIlIl1]['Value'] == NULL) {
if ($this->properties[$IIIIIIlIlIl1]['Create'] ||$this->properties[$IIIIIIlIlIl1]['Update']) {
$IIIIIIlIlI1I[$IIIIIIlIlIl1] = $IIIIIIlIlI1l;
$IIIIIIlIlI11 = false;
}
}
}
if ($GLOBALS['IIIIIIIIlII1']($IIIIIIlIllII) &&$IIIIIIlIlI11 == false) {
$IIIIIIlIllII += $IIIIIIlIlI1I;
}
return $IIIIIIlIlI11;
}
public function getEntityDOM($IIIIIIlIllIl = false) {
$IIIIIIlIlllI = new DOMDocument();
$IIIIIIIl1lll = $IIIIIIlIlllI->appendChild($IIIIIIlIlllI->createElement('entity'));
$IIIIIIIl1lll->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:i','http://www.w3.org/2001/XMLSchema-instance');
$IIIIIIlIllll = $IIIIIIIl1lll->appendChild($IIIIIIlIlllI->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts','b:Attributes'));
$IIIIIIlIllll->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:c','http://schemas.datacontract.org/2004/07/System.Collections.Generic');
foreach ($this->properties as $IIIIIII1lIll =>$IIIIIIlIlll1) {
if ($this->propertyValues[$IIIIIII1lIll]['Changed']) {
$IIIIIII1lI1I = $IIIIIIlIllll->appendChild($IIIIIIlIlllI->createElement('b:KeyValuePairOfstringanyType'));
$IIIIIII1lI1I->appendChild($IIIIIIlIlllI->createElement('c:key',$IIIIIII1lIll));
if ($IIIIIIlIlll1['isLookup']) {
$IIIIIII1lIl1 = $IIIIIII1lI1I->appendChild($IIIIIIlIlllI->createElement('c:value'));
if ($this->propertyValues[$IIIIIII1lIll]['Value'] != NULL){
$IIIIIII1lIl1->setAttribute('i:type','b:EntityReference');
$IIIIIII1lIl1->appendChild($IIIIIIlIlllI->createElement('b:Id',($this->propertyValues[$IIIIIII1lIll]['Value']) ?$this->propertyValues[$IIIIIII1lIll]['Value']->ID : ""));
$IIIIIII1lIl1->appendChild($IIIIIIlIlllI->createElement('b:LogicalName',($this->propertyValues[$IIIIIII1lIll]['Value']) ?$this->propertyValues[$IIIIIII1lIll]['Value']->logicalname : ""));
$IIIIIII1lIl1->appendChild($IIIIIIlIlllI->createElement('b:Name'))->setAttribute('i:nil','true');
}else{
$IIIIIII1lIl1->setAttribute('i:nil','true');
}
}else if($GLOBALS['IIIIIIIl1Il1']($IIIIIIlIlll1['Type']) == "money") {
$IIIIIII1lIl1 = $IIIIIII1lI1I->appendChild($IIIIIIlIlllI->createElement('c:value'));
if ($this->propertyValues[$IIIIIII1lIll]['Value']){
$IIIIIII1lIl1->setAttribute('i:type','b:Money');
$IIIIIII1lIl1->appendChild($IIIIIIlIlllI->createElement('b:Value',$this->propertyValues[$IIIIIII1lIll]['Value']));
}else{
$IIIIIII1lIl1->setAttribute('i:nil','true');
}
}else if ($GLOBALS['IIIIIIIl1Il1']($IIIIIIlIlll1['Type']) == "datetime") {
$IIIIIII1lIl1 = $IIIIIII1lI1I->appendChild($IIIIIIlIlllI->createElement('c:value'));
if ($this->propertyValues[$IIIIIII1lIll]['Value']){
$IIIIIII1lIl1->setAttribute('i:type','d:dateTime');
$IIIIIII1lIl1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:d','http://www.w3.org/2001/XMLSchema');
$IIIIIII1lIl1->appendChild(new DOMText($GLOBALS['IIIIIIlIll1I']("Y-m-d\TH:i:s\Z",$this->propertyValues[$IIIIIII1lIll]['Value'])));
}else{
$IIIIIII1lIl1->setAttribute('i:nil','true');
}
}else if ($GLOBALS['IIIIIIIl1Il1']($IIIIIIlIlll1['Type']) == "picklist"){
$IIIIIII1lIl1 = $IIIIIII1lI1I->appendChild($IIIIIIlIlllI->createElement('c:value'));
if ($this->propertyValues[$IIIIIII1lIll]['Value']){
$IIIIIII1lIl1->setAttribute('i:type','d:OptionSetValue');
$IIIIIII1lIl1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:d','http://schemas.microsoft.com/xrm/2011/Contracts');
$IIIIIII1lIl1->appendChild($IIIIIIlIlllI->createElement('b:Value',$this->propertyValues[$IIIIIII1lIll]['Value']));
}else{
$IIIIIII1lIl1->setAttribute('i:nil','true');
}
}else {
$IIIIIII1llIl = $this->propertyValues[$IIIIIII1lIll]['Value'];
$IIIIIII1llII = NULL;
$IIIIIII1lIlI = $GLOBALS['IIIIIIIl1Il1']($IIIIIIlIlll1['Type']);
$IIIIIII1lI11 = 'http://www.w3.org/2001/XMLSchema';
switch ($GLOBALS['IIIIIIIl1Il1']($IIIIIIlIlll1['Type'])) {
case 'memo':
$IIIIIII1lIlI = 'string';
break;
case 'integer':
$IIIIIII1lIlI = 'int';
break;
case 'uniqueidentifier':
$IIIIIII1lIlI = 'guid';
break;
case 'state':
case 'status':
$IIIIIII1lIlI = 'OptionSetValue';
$IIIIIII1lI11 = 'http://schemas.microsoft.com/xrm/2011/Contracts';
$IIIIIII1llIl = NULL;
$IIIIIII1llII = $IIIIIIlIlllI->createElement('b:Value',$this->propertyValues[$IIIIIII1lIll]['Value']);
break;
case 'boolean':
$IIIIIII1llIl = $this->propertyValues[$IIIIIII1lIll]['Value'];
break;
case 'string':
case 'int':
case 'decimal':
case 'double':
case 'guid':
break;
default:
trigger_error('No Create/Update handling implemented for type '.$IIIIIIlIlll1['Type'].' used by field '.$IIIIIII1lIll,
E_USER_WARNING);
}
$IIIIIII1lIl1 = $IIIIIII1lI1I->appendChild($IIIIIIlIlllI->createElement('c:value'));
$IIIIIII1lIl1->setAttribute('i:type','d:'.$IIIIIII1lIlI);
$IIIIIII1lIl1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:d',$IIIIIII1lI11);
if ($IIIIIII1llII != NULL) $IIIIIII1lIl1->appendChild($IIIIIII1llII);
if ($IIIIIII1llIl != NULL) $IIIIIII1lIl1->appendChild(new DOMText($IIIIIII1llIl));
}
}
}
$IIIIIIIl1lll->appendChild($IIIIIIlIlllI->createElement('b:EntityState'))->setAttribute('i:nil','true');
$IIIIIIlIl1ll = $IIIIIIIl1lll->appendChild($IIIIIIlIlllI->createElement('b:FormattedValues'));
$IIIIIIlIl1ll->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:c','http://schemas.datacontract.org/2004/07/System.Collections.Generic');
$IIIIIIIl1lll->appendChild($IIIIIIlIlllI->createElement('b:Id',$this->getID()));
$IIIIIIIl1lll->appendChild($IIIIIIlIlllI->createElement('b:LogicalName',$this->IIIIIIIl111l));
$IIIIIIlIl1I1 = $IIIIIIIl1lll->appendChild($IIIIIIlIlllI->createElement('b:RelatedEntities'));
$IIIIIIlIl1I1->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:c','http://schemas.datacontract.org/2004/07/System.Collections.Generic');
return $IIIIIIIl1lll;
}
public static function fromLogicalName(AlexaSDK $IIIIIIlIll11,$IIIIIIIl111l) {
$IIIIIIlIll1l = self::getClassName($IIIIIIIl111l);
if (!class_exists($IIIIIIlIll1l,true)) {
$IIIIIIlIll1l = 'AlexaSDK_Entity';
}
return new $IIIIIIlIll1l($IIIIIIlIll11,$IIIIIIIl111l);
}
public static function fromDOM(AlexaSDK $IIIIIIlIll11,$IIIIIIIl111l,DOMElement $IIIIIIlIl1II) {
$entity = self::fromLogicalName($IIIIIIlIll11,$IIIIIIIl111l);
$IIIIIIlIl1I1 = NULL;
$IIIIIIlIl1lI = NULL;
$IIIIIIlIl1ll = NULL;
$IIIIIIlIl1l1 = NULL;
$IIIIIIlIl11I = NULL;
foreach ($IIIIIIlIl1II->childNodes as $IIIIIIlIl11l) {
switch ($IIIIIIlIl11l->localName) {
case 'RelatedEntities':
$IIIIIIlIl1I1 = $IIIIIIlIl11l;
break;
case 'Attributes':
$IIIIIIlIl1lI = $IIIIIIlIl11l;
break;
case 'FormattedValues':
$IIIIIIlIl1ll = $IIIIIIlIl11l;
break;
case 'Id':
$entity->ID = $IIIIIIlIl11l->textContent;
break;
case 'LogicalName':
$IIIIIIlIl1l1 = $IIIIIIlIl11l->textContent;
break;
case 'EntityState':
$IIIIIIlIl11I = $IIIIIIlIl11l->textContent;
break;
}
}
if ($IIIIIIlIl1l1 != $IIIIIIIl111l) {
trigger_error('Expected to get a '.$IIIIIIIl111l.' but actually received a '.$IIIIIIlIl1l1.' from the server!',
E_USER_WARNING);
}
$entity->setAttributesFromDOM($IIIIIIlIll11,$IIIIIIlIl1lI,$IIIIIIlIl1ll);
$entity->reset();
return $entity;
}
private function setAttributesFromDOM(AlexaSDK $IIIIIIlIll11,DOMElement $IIIIIIlIl1lI,DOMElement $IIIIIIlIl1ll) {
$IIIIIIlI1IIl = Array();
$IIIIIIIl1l1I = $IIIIIIlIl1ll->getElementsByTagName('KeyValuePairOfstringstring');
self::addFormattedValues($IIIIIIlI1IIl,$IIIIIIIl1l1I);
foreach($IIIIIIlI1IIl as $key =>$value){
$this->IIIIIIlI1IIl[$key] = $value;
}
$IIIIIIIl1l1I = $IIIIIIlIl1lI->getElementsByTagName('KeyValuePairOfstringanyType');
foreach ($IIIIIIIl1l1I as $IIIIIIIl11lI) {
$IIIIIIIl11ll = $IIIIIIIl11lI->getElementsByTagName('key')->item(0)->textContent;
$IIIIIIIl11l1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type');
$IIIIIIIl11l1 = self::stripNS($IIIIIIIl11l1);
$IIIIIII1III1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->textContent;
switch ($IIIIIIIl11l1) {
case 'string':
case 'guid':
$IIIIIIlI1IlI = $IIIIIII1III1;
break;
case 'dateTime':
$IIIIIIlI1IlI = self::parseTime($IIIIIII1III1,'%Y-%m-%dT%H:%M:%SZ');
break;
case "BooleanManagedProperty":
case 'boolean':
$IIIIIIlI1IlI = ($GLOBALS['IIIIIIIl1Il1']($IIIIIII1III1) == 'true'?true : false);
break;
case 'decimal':
$IIIIIIlI1IlI = (float)$IIIIIII1III1;
break;
case 'double':
$IIIIIIlI1IlI = (float)$IIIIIII1III1;
break;
case 'int':
$IIIIIIlI1IlI = (int)$IIIIIII1III1;
break;
case 'Money':
$IIIIIIlI1IlI = (float)$IIIIIII1III1;
break;
case 'OptionSetValue':
$IIIIIIlII1l1 = (int)$IIIIIII1III1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->textContent;
$IIIIIIlI1IlI = new AlexaSDK_OptionSetValue($IIIIIIlII1l1,$IIIIIIlI1IIl[$IIIIIIIl11ll]);
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll.'name',$this->properties)) {
if ($this->propertyValues[$IIIIIIIl11ll.'name']['Value'] == NULL) {
$this->propertyValues[$IIIIIIIl11ll.'name']['Value'] = $IIIIIIlI1IIl[$IIIIIIIl11ll];
}
}
break;
case 'EntityReference':
$IIIIIIlI1Il1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('LogicalName')->item(0)->textContent;
$IIIIIIlI1I1I = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Id')->item(0)->textContent;
$IIIIIIlI1I1l = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Name')->item(0)->textContent;
$IIIIIIlI1IlI = self::fromLogicalName($IIIIIIlIll11,$IIIIIIlI1Il1);
$IIIIIIlI1IlI->ID = $IIIIIIlI1I1I;
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll.'name',$this->properties)) {
if ($this->propertyValues[$IIIIIIIl11ll.'name']['Value'] == NULL) {
$this->propertyValues[$IIIIIIIl11ll.'name']['Value'] = $IIIIIIlI1I1l;
}
if ($IIIIIIlI1IlI->IIIIIII1Ill1 != NULL) {
$IIIIIIlI1IlI->propertyValues[$IIIIIIlI1IlI->IIIIIII1Ill1]['Value'] = $IIIIIIlI1I1l;
}
}
break;
case 'AliasedValue':
if ($GLOBALS['IIIIIIlI1I11']($IIIIIIIl11ll,'.') === FALSE) {
$IIIIIIlI1lII = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('AttributeLogicalName')->item(0)->textContent;
$this->localProperties[$IIIIIIIl11ll] = Array(
'Label'=>'AliasedValue: '.$IIIIIIIl11ll,
'Description'=>'Aggregate field with alias '.$IIIIIIIl11ll.' based on field '.$IIIIIIlI1lII,
'isCustom'=>true,
'isPrimaryId'=>false,
'isPrimaryName'=>false,
'Type'=>'AliasedValue',
'isLookup'=>false,
'lookupTypes'=>NULL,
'Create'=>false,
'Update'=>false,
'Read'=>true,
'RequiredLevel'=>'None',
'AttributeOf'=>NULL,
'OptionSet'=>NULL,
);
$this->propertyValues[$IIIIIIIl11ll] = Array(
'Value'=>NULL,
'Changed'=>false,
);
$IIIIIIlI1lIl =  $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->getAttribute('type');
$IIIIIIlI1IlI = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->textContent;
}else {
list($IIIIIIlI1lI1,$IIIIIIlI1lII) = $GLOBALS['IIIIIIIl111I']('.',$IIIIIIIl11ll);
$IIIIIIlI1llI = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('EntityLogicalName')->item(0)->textContent;
$IIIIIIlI1lII = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('AttributeLogicalName')->item(0)->textContent;
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIlI1lI1,$this->propertyValues)) {
$IIIIIIlI1IlI = $this->propertyValues[$IIIIIIlI1lI1]['Value'];
if ($IIIIIIlI1IlI == NULL) {
$IIIIIIlI1IlI = self::fromLogicalName($IIIIIIlIll11,$IIIIIIlI1llI);
if (!$GLOBALS['IIIIIIlII1lI']($IIIIIIlI1llI,$this->properties[$IIIIIIlI1lI1]['lookupTypes'])) {
trigger_error('Alias '.$IIIIIIlI1lI1.' overlaps and existing field of type '.$GLOBALS['IIIIIIlII1ll'](' or ',$this->properties[$IIIIIIlI1lI1]['lookupTypes'])
.' but is being set to a '.$IIIIIIlI1llI,
E_USER_WARNING);
}
}else {
if ($IIIIIIlI1IlI->logicalName != $IIIIIIlI1llI) {
trigger_error('Alias '.$IIIIIIlI1lI1.' was created as a '.$IIIIIIlI1IlI->logicalName.' but is now referenced as a '.$IIIIIIlI1llI.' in field '.$IIIIIIIl11ll,
E_USER_WARNING);
}
}
}else {
$IIIIIIlI1IlI = self::fromLogicalName($IIIIIIlIll11,$IIIIIIlI1llI);
$this->localProperties[$IIIIIIlI1lI1] = Array(
'Label'=>'AliasedValue: '.$IIIIIIlI1lI1,
'Description'=>'Related '.$IIIIIIlI1llI.' with alias '.$IIIIIIlI1lI1,
'isCustom'=>true,
'isPrimaryId'=>false,
'isPrimaryName'=>false,
'Type'=>'AliasedValue',
'isLookup'=>true,
'lookupTypes'=>NULL,
'Create'=>false,
'Update'=>false,
'Read'=>true,
'RequiredLevel'=>'None',
'AttributeOf'=>NULL,
'OptionSet'=>NULL,
);
$this->propertyValues[$IIIIIIlI1lI1] = Array(
'Value'=>NULL,
'Changed'=>false,
);
}
$IIIIIIlI1ll1 = new DOMDocument();
$IIIIIIlI1l1l = $IIIIIIlI1ll1->appendChild($IIIIIIlI1ll1->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts','b:Attributes'));
$aliasAttributeNode = $IIIIIIlI1l1l->appendChild($IIIIIIlI1ll1->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts','b:KeyValuePairOfstringanyType'));
$aliasAttributeNode->appendChild($IIIIIIlI1ll1->createElementNS('http://schemas.datacontract.org/2004/07/System.Collections.Generic','c:key',$IIIIIIlI1lII));
$aliasAttributeValueNode = $aliasAttributeNode->appendChild($IIIIIIlI1ll1->createElementNS('http://schemas.datacontract.org/2004/07/System.Collections.Generic','c:value'));
foreach ($IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->childNodes as $child){
$aliasAttributeValueNode->appendChild($IIIIIIlI1ll1->importNode($child,true));
}
$aliasAttributeValueNode->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance','i:type',
$IIIIIIIl11lI->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance','type'));
$IIIIIIlI1lll = $IIIIIIlI1ll1->appendChild($IIIIIIlI1ll1->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts','b:FormattedValues'));
$IIIIIIlI1lll->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:c','http://schemas.datacontract.org/2004/07/System.Collections.Generic');
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll,$IIIIIIlI1IIl)) {
$IIIIIIlI1l1I = $IIIIIIlI1lll->appendChild($IIIIIIlI1ll1->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts','b:KeyValuePairOfstringstring'));
$IIIIIIlI1l1I->appendChild($IIIIIIlI1ll1->createElementNS('http://schemas.datacontract.org/2004/07/System.Collections.Generic','c:key',$IIIIIIlI1lII));
$IIIIIIlI1l1I->appendChild($IIIIIIlI1ll1->createElementNS('http://schemas.datacontract.org/2004/07/System.Collections.Generic','c:value',$IIIIIIlI1IIl[$IIIIIIIl11ll]));
}
$IIIIIIlI1IlI->setAttributesFromDOM($IIIIIIlIll11,$IIIIIIlI1l1l,$IIIIIIlI1lll);
$IIIIIIIl11ll = $IIIIIIlI1lI1;
}
break;
default:
trigger_error('No parse handling implemented for type '.$IIIIIIIl11l1.' used by field '.$IIIIIIIl11ll,
E_USER_WARNING);
$IIIIIII1III1 = $IIIIIIIl11lI->getElementsByTagName('value')->item(0)->C14N();
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll,$IIIIIIlI1IIl)) {
$IIIIIIlI1IlI = Array('XML'=>$IIIIIII1III1,'FormattedText'=>$IIIIIIlI1IIl[$IIIIIIIl11ll]);
}else {
$IIIIIIlI1IlI = $IIIIIII1III1;
}
}
$this->propertyValues[$IIIIIIIl11ll]['Value'] = $IIIIIIlI1IlI;
if ($GLOBALS['IIIIIII1IIIl']($IIIIIIIl11ll,$this->properties) &&$this->properties[$IIIIIIIl11ll]['isPrimaryId'] &&$this->IIIIIIlIIII1 == NULL) {
if ($IIIIIIlI1IlI != NULL &&$IIIIIIlI1IlI != self::EmptyGUID) {
$this->IIIIIIlIIII1 = $IIIIIIlI1IlI;
}
}
}
}
public function printDetails($IIIIIIlI1l11 = false,$IIIIIIlI11II = 0,$IIIIIIlI11Il = true) {
echo str_repeat("\t",$IIIIIIlI11II).$this->IIIIIII1Ill1.' ('.$this->getURL(true).')'.PHP_EOL;
$IIIIIIlI11II++;
$IIIIIIlI11lI = str_repeat("\t",$IIIIIIlI11II);
$IIIIIIlI11ll = $GLOBALS['IIIIIIlI11l1']($this->propertyValues);
$GLOBALS['IIIIIIlI111I']($IIIIIIlI11ll);
foreach ($IIIIIIlI11ll as $IIIIIII1lIll) {
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->properties)) {
$IIIIIIlIlll1 = $this->properties[$IIIIIII1lIll];
}else {
$IIIIIIlIlll1 = $this->localProperties[$IIIIIII1lIll];
}
if ($IIIIIIlI1l11 &&$IIIIIIlIlll1['AttributeOf'] != NULL) continue;
if (!$IIIIIIlI11Il &&$this->propertyValues[$IIIIIII1lIll]['Value'] == NULL) continue;
echo $IIIIIIlI11lI.$IIIIIII1lIll.' ['.$IIIIIIlIlll1['Label'].']: ';
if ($this->propertyValues[$IIIIIII1lIll]['Value'] == NULL) {
echo 'NULL ('.$IIIIIIlIlll1['Type'].')'.PHP_EOL;
continue;
}else {
echo PHP_EOL;
}
if ($IIIIIIlIlll1['isLookup']) {
if ($IIIIIIlI1l11) {
$this->propertyValues[$IIIIIII1lIll]['Value']->printDetails($IIIIIIlI1l11,$IIIIIIlI11II+1);
}else {
echo $IIIIIIlI11lI."\t".$this->propertyValues[$IIIIIII1lIll]['Value'].PHP_EOL;
}
continue;
}
switch ($GLOBALS['IIIIIIIl1Il1']($IIIIIIlIlll1['Type'])) {
case 'datetime':
echo $IIIIIIlI11lI."\t".$GLOBALS['IIIIIIlI1Ill']('Y-m-d H:i:s P',$this->propertyValues[$IIIIIII1lIll]['Value']).PHP_EOL;
break;
case 'boolean':
if ($this->propertyValues[$IIIIIII1lIll]['Value']) {
echo $IIIIIIlI11lI."\t".'('.$IIIIIIlIlll1['Type'].') TRUE'.PHP_EOL;
}else {
echo $IIIIIIlI11lI."\t".'('.$IIIIIIlIlll1['Type'].') FALSE'.PHP_EOL;
}
break;
case 'picklist':
case 'state':
case 'status':
case 'decimal':
case 'double':
case 'uniqueidentifier':
case 'memo':
case 'string':
case 'virtual':
case 'entityname':
case 'integer':
echo $IIIIIIlI11lI."\t".'('.$IIIIIIlIlll1['Type'].') '.$this->propertyValues[$IIIIIII1lIll]['Value'].PHP_EOL;
break;
default:
trigger_error('No output handling implemented for type '.$IIIIIIlIlll1['Type'].' used by field '.$IIIIIII1lIll,
E_USER_WARNING);
echo $IIIIIIlI11lI."\t".'('.$IIIIIIlIlll1['Type'].') '.print_r($this->propertyValues[$IIIIIII1lIll]['Value'],true).PHP_EOL;
}
}
}
public function getProperties(){
return $this->properties;
}
public function getPropertyValues(){
return $this->propertyValues;
}
public function getPropertyKeys(){
return $GLOBALS['IIIIIIlI11l1']($this->propertyValues);
}
public function getPrimaryNameField(){
return $this->IIIIIII1Ill1;
}
public function getURL($absolute = false) {
if ($this->IIIIIIlIIII1 == NULL) return NULL;
$entityURL = 'main.aspx?etn='.$this->IIIIIIIl111l.'&pagetype=entityrecord&id='.$this->IIIIIIlIIII1;
if ($absolute) {
return $this->entityDomain.$entityURL;
}else {
return $entityURL;
}
}
protected function setEntityDomain(AlexaSDK $IIIIIIlIll11) {
$organizationURL = $IIIIIIlIll11->getOrganizationURI();
$urlDetails = parse_url($organizationURL);
$domainURL = $urlDetails['scheme'].'://'.$urlDetails['host'].'/';
if (strstr($organizationURL,'/'.$IIIIIIlIll11->getOrganization().'/') !== FALSE) {
$domainURL = $domainURL .$IIIIIIlIll11->getOrganization() .'/';
}
$this->entityDomain = $domainURL;
}
public function getOptionSetValues($IIIIIII1lIll) {
$IIIIIII1lIll = $GLOBALS['IIIIIIIl1Il1']($IIIIIII1lIll);
if (!$GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->properties)) return NULL;
$IIIIIIlIIlll = $this->properties[$IIIIIII1lIll]['OptionSet'];
if ($IIIIIIlIIlll == NULL) return NULL;
return $this->optionSets[$IIIIIIlIIlll];
}
public function getPropertyLabel($IIIIIII1lIll) {
$IIIIIII1lIll = $GLOBALS['IIIIIIIl1Il1']($IIIIIII1lIll);
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->properties)) {
return $this->properties[$IIIIIII1lIll]['Label'];
}
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->localProperties)) {
return $this->localProperties[$IIIIIII1lIll]['Label'];
}
return '';
}
public function getFormattedValue($IIIIIII1lIll,$timezoneoffset = NULL){
switch ($GLOBALS['IIIIIIlII1II']($IIIIIII1lIll)) {
case 'ID':
return $this->getID();
break;
case 'LOGICALNAME':
return $this->IIIIIIIl111l;
break;
case 'DISPLAYNAME':
if ($this->IIIIIII1Ill1 != NULL) {
$IIIIIII1lIll = $this->IIIIIII1Ill1;
}else {
return NULL;
}
break;
}
$IIIIIII1lIll = $GLOBALS['IIIIIIIl1Il1']($IIIIIII1lIll);
if ($timezoneoffset != NULL &&$GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->properties) &&$this->properties[$IIIIIII1lIll]['Type'] == "DateTime"&&$this->properties[$IIIIIII1lIll]['Read'] === true){
if($this->propertyValues[$IIIIIII1lIll]['Value'] == NULL){
return "";
}else if ($this->properties[$IIIIIII1lIll]['Format'] == "DateAndTime"){
return $GLOBALS['IIIIIIlI1Ill']("n/j/Y H:i",$this->propertyValues[$IIIIIII1lIll]['Value'] -$timezoneoffset * 60);
}else if($this->properties[$IIIIIII1lIll]['Format'] == "DateOnly"){
return $GLOBALS['IIIIIIlI1Ill']("n/j/Y",$this->propertyValues[$IIIIIII1lIll]['Value'] -$timezoneoffset * 60);
}
}
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->IIIIIIlI1IIl) &&$this->properties[$IIIIIII1lIll]['Read'] === true) {
return $this->IIIIIIlI1IIl[$IIIIIII1lIll];
}
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->properties) &&$this->properties[$IIIIIII1lIll]['Read'] === true) {
return $this->propertyValues[$IIIIIII1lIll]['Value'];
}
if ($GLOBALS['IIIIIII1IIIl']($IIIIIII1lIll,$this->localProperties) &&$this->localProperties[$IIIIIII1lIll]['Read'] === true) {
return $this->propertyValues[$IIIIIII1lIll]['Value'];
}
$IIIIIIlII1Il = debug_backtrace();
trigger_error('Undefined property via __get(): '.$IIIIIII1lIll 
.' in '.$IIIIIIlII1Il[0]['file'] .' on line '.$IIIIIIlII1Il[0]['line'],
E_USER_NOTICE);
return NULL;
}
private function parseRetrieveResponse(AlexaSDK $IIIIIIlIll11,$IIIIIIIl111l,$IIIIIIII1l11) {
$IIIIIIIl1I11 = new DOMDocument();
$IIIIIIIl1I11->loadXML($IIIIIIII1l11);
$IIIIIIlI1111 = NULL;
foreach ($IIIIIIIl1I11->getElementsByTagName('RetrieveResponse') as $IIIIIIIl1lIl) {
$IIIIIIlI1111 = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($IIIIIIlI1111 == NULL) {
throw new Exception('Could not find RetrieveResponse node in XML provided');
return FALSE;
}
$IIIIIIllIIII = NULL;
foreach ($IIIIIIlI1111->getElementsByTagName('RetrieveResult') as $IIIIIIIl1lIl) {
$IIIIIIllIIII = $IIIIIIIl1lIl;
break;
}
unset($IIIIIIIl1lIl);
if ($IIIIIIllIIII == NULL) {
throw new Exception('Could not find RetrieveResult node in XML provided');
return FALSE;
}
$this->setValuesFromDom($IIIIIIlIll11,$IIIIIIIl111l,$IIIIIIllIIII);
}
private function setValuesFromDom(AlexaSDK $IIIIIIlIll11,$IIIIIIIl111l,DOMElement $IIIIIIlIl1II) {
$IIIIIIlIl1I1 = NULL;
$IIIIIIlIl1lI = NULL;
$IIIIIIlIl1ll = NULL;
$IIIIIIlIl1l1 = NULL;
$IIIIIIlIl11I = NULL;
foreach ($IIIIIIlIl1II->childNodes as $IIIIIIlIl11l) {
switch ($IIIIIIlIl11l->localName) {
case 'RelatedEntities':
$IIIIIIlIl1I1 = $IIIIIIlIl11l;
break;
case 'Attributes':
$IIIIIIlIl1lI = $IIIIIIlIl11l;
break;
case 'FormattedValues':
$IIIIIIlIl1ll = $IIIIIIlIl11l;
break;
case 'Id':
break;
case 'LogicalName':
$IIIIIIlIl1l1 = $IIIIIIlIl11l->textContent;
break;
case 'EntityState':
$IIIIIIlIl11I = $IIIIIIlIl11l->textContent;
break;
}
}
if ($IIIIIIlIl1l1 != $IIIIIIIl111l) {
trigger_error('Expected to get a '.$IIIIIIIl111l.' but actually received a '.$IIIIIIlIl1l1.' from the server!',
E_USER_WARNING);
}
if (self::$IIIIIIIIlI1l) echo 'Entity <'.$entity->ID.'> has EntityState: '.$IIIIIIlIl11I.PHP_EOL;
$this->setAttributesFromDOM($IIIIIIlIll11,$IIIIIIlIl1lI,$IIIIIIlIl1ll);
$this->reset();
}
}?>
