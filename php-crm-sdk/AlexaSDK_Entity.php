<?php

if (!class_exists("AlexaSDK_Entity")) :

class AlexaSDK_Entity extends AlexaSDK_Abstract {
        /**
	 * Overridden in each child class
	 * @var String entityLogicalName this is how Dynamics refers to this Entity
	 */
	protected $entityLogicalName = NULL;
        
        /** 
         * @var String entityName, display name of Entity object record 
         */
        protected $displayName = NULL;
        
	/** 
         * @var String DisplayName of Entity the field to use to display the entity's Name (Example: Contact, Account) 
         */
	protected $entityDisplayName = NULL;
        
        /** 
         * @var String DisplayCollectionName of Entity, field that displays multiple entities name of one type (Example: Contacts, Accounts) 
         */
        protected $entityDisplayCollectionName = NULL;
        
        /** 
         * @var String EntityDescription description of specified Entity 
         */
        protected $entityDescription = NULL;
        
        /* @var String EntityTypeCode, ObjectTypeCode is the same */
        private $entitytypecode = NULL;
        
	/* The details of the Entity structure (SimpleXML object) */
	public $entityData;
        
	/* The details of the Entity structure (as Arrays) */
	public $properties = Array();
	public $mandatories = Array();
	public $optionSets = Array();
	/* The details of this instance of the Entity - the added AliasedValue properites */
	protected $localProperties = Array();
	/* The details of this instance of the Entity - the property Values */
	public $propertyValues = Array();
        /* The details of this instance of the Entity - the propery Formatted Values */
        public $formattedValues = Array();
        
        public $manyToManyRelationships = Array();
        
        public $manyToOneRelationships = Array();
        
        public $oneToManyRelationships = Array();
        
        /* Entity field values validation */
        public $fieldValidation = TRUE;
        
        protected $validator = NULL;
        
        /* The errors in the property Values */
        public $errors = Array();
	/* The ID of the Entity */
	private $entityID;
	/* The Domain/URL of the Dynamics CRM Server where this is stored */
	private $entityDomain = NULL;
        
        
        /**
	 * 
	 * @param AlexaSDK $auth Connection to the Dynamics CRM server - should be active already.
	 * @param String $_logicalName Allows constructing arbritrary Entities by setting the EntityLogicalName directly
         * @param String $_ID Allows constructing arbritrary Entities by setting the EntityLogicalName directly
	 */
	function __construct(AlexaSDK $auth, $_logicalName = NULL, $_ID = NULL) {
		/* If a new LogicalName was passed, set it in this Entity */
		if ($_logicalName != NULL && $_logicalName != $this->entityLogicalName) {
			/* If this value was already set, don't allow changing it. */
			/* - otherwise, you could have a AlexaSDK_Incident that was actually an Account! */
			if ($this->entityLogicalName != NULL) {
				throw new Exception('Cannot override the Entity Logical Name on a strongly typed Entity');
			}
			/* Set the Logical Name */
			$this->entityLogicalName = $_logicalName;
		}
		/* Check we have a Logical Name for the Entity */
		if ($this->entityLogicalName == NULL) {
			throw new Execption('Cannot instantiate an abstract Entity - specify the Logical Name');
		}
		/* Set the Domain that this Entity is associated with */
		$this->setEntityDomain($auth);
                
                $this->validator = new AlexaSDK_FormValidator();
                
                /* Check if the Definition of this Entity is Cached on the Connector */
		if ($auth->isEntityDefinitionCached($this->entityLogicalName)) {
                    
			/* Use the Cached values */
			$isDefined = $auth->getCachedEntityDefinition($this->entityLogicalName, 
					$this->entityData, $this->properties, $this->propertyValues, $this->mandatories,
					$this->optionSets, $this->displayName, $this->entitytypecode, $this->entityDisplayName, 
                                        $this->entityDisplayCollectionName, $this->entityDescription, $this->manyToManyRelationships, 
                                        $this->manyToOneRelationships, $this->oneToManyRelationships);
                        
                        if (self::$debugMode){ echo "Cached ". $this->entityLogicalName; }
                        
                        if ($isDefined){ 
                            
                            /* TODO: Add checkings */
                           /* $this->entityDescription = (String)$this->entityData->Description->LocalizedLabels->LocalizedLabel->Label;

                            $this->entityDisplayName = (String)$this->entityData->DisplayName->LocalizedLabels->LocalizedLabel->Label;

                            $this->entityDisplayCollectionName = (String)$this->entityData->DisplayCollectionName->LocalizedLabels->LocalizedLabel->Label;

                            $this->entitytypecode = (String)$this->entityData->ObjectTypeCode;*/
                            
                            
                            /* Set EntityValues if specified Entity ID */
                            if ($_ID != NULL) {
                                    /* Set the ID of Entity record */
                                    $this->setID($_ID);
                                    /* Get the raw XML data */
                                    $rawSoapResponse = $auth->retrieveRaw($this);
                                    
                                    /* NOTE: ParseRetrieveResponse method of AlexaSDK_Entity class, not the AlexaSDK class */
                                    $this->ParseRetrieveResponse($auth, $this->LogicalName, $rawSoapResponse);
                            }
                            
                            return;
                            
                        }	
		}
                
                /* At this point, we assume Entity is not Cached */
		/* So, get the full details of what an Incident is on this server */
		$this->entityData = $auth->retrieveEntity($this->entityLogicalName);
                
                /* Check we have a Simple XML Class for the Entity */
		if (!$this->entityData) {
			throw new Execption('Unable to load metadata simple_xml_class'.$this->entityData);
		}
                
                /* TODO: Add checkings */
                $this->entityDescription = (String)$this->entityData->Description->LocalizedLabels->LocalizedLabel->Label;
                
                $this->entityDisplayName = (String)$this->entityData->DisplayName->LocalizedLabels->LocalizedLabel->Label;
                
                $this->entityDisplayCollectionName = (String)$this->entityData->DisplayCollectionName->LocalizedLabels->LocalizedLabel->Label;
                
                $this->entitytypecode = (String)$this->entityData->ObjectTypeCode;
                
                /* Next, we analyse this data and determine what Properties this Entity has */
		foreach ($this->entityData->Attributes[0]->AttributeMetadata as $attribute) {
			/* Determine the Type of the Attribute */
			$attributeList = $attribute->attributes('http://www.w3.org/2001/XMLSchema-instance');
			$attributeType = self::stripNS($attributeList['type']);
			/* Handle the special case of Lookup types */
			$isLookup = ($attributeType == 'LookupAttributeMetadata');
			/* If it's a Lookup, check what Targets are allowed */
			if ($isLookup) {
				$lookupTypes = Array();
                                /* Add lookup types to Properties, search for target entities */
				foreach ($attribute->Targets->string as $target) {
					array_push($lookupTypes, (string)$target);
				}
			} else {
				$lookupTypes = NULL;
			}
			/* Check if this field is mandatory */
			$requiredLevel = (String)$attribute->RequiredLevel->Value;
                        
			/* If this is an OptionSet, determine the OptionSet details */
			if (!empty($attribute->OptionSet) && !empty($attribute->OptionSet->Name)) {
				/* Determine the Name of the OptionSet */
				$optionSetName = (String)$attribute->OptionSet->Name;
				$optionSetGlobal = ($attribute->OptionSet->IsGlobal == 'true');
				/* Determine the Type of the OptionSet */
				$optionSetType = (String)$attribute->OptionSet->OptionSetType;
				/* Array to store the Options for this OptionSet */
				$optionSetValues = Array();
				
				/* Debug logging - Identify the OptionSet */
				if (self::$debugMode) {
                                      //  echo "<pre>";
					//echo 'Attribute '.(String)$attribute->SchemaName.' is an OptionSet'.PHP_EOL;
					//echo "\tName:\t".$optionSetName.($optionSetGlobal ? ' (Global)' : '').PHP_EOL;
					//echo "\tType:\t".$optionSetType.PHP_EOL;
                                       // echo "</pre>";
				}
				
				/* Handle the different types of OptionSet */
				switch ($optionSetType) {
					case 'Boolean':
						/* Parse the FalseOption */
						$value = (int)$attribute->OptionSet->FalseOption->Value;
						$label = (String)$attribute->OptionSet->FalseOption->Label->UserLocalizedLabel->Label[0];
						$optionSetValues[$value] = $label;
						/* Parse the TrueOption */
						$value = (int)$attribute->OptionSet->TrueOption->Value;
						$label = (String)$attribute->OptionSet->TrueOption->Label->UserLocalizedLabel->Label[0];
						$optionSetValues[$value] = $label;
						break;
					case 'State':
                                                foreach ($attribute->OptionSet->Options->OptionMetadata as $option) {
							/* Parse the Option */
							$value = (int)$option->Value;
							$label = (String)$option->Label->UserLocalizedLabel->Label[0];
							/* Check for duplicated Values */
							if (array_key_exists($value, $optionSetValues)) {
								trigger_error('Option '.$label.' of OptionSet '.$optionSetName.' used by field '.(String)$attribute->SchemaName.' has the same Value as another Option in this Set',
										E_USER_WARNING);
							} else {
								/* Store the Option */
								$optionSetValues[$value] = $label;
							}
						}
                                                break;
					case 'Status':
                                                foreach ($attribute->OptionSet->Options->OptionMetadata as $option) {
							/* Parse the Option */
							$value = (int)$option->Value;
							$label = (String)$option->Label->UserLocalizedLabel->Label[0];
							/* Check for duplicated Values */
							if (array_key_exists($value, $optionSetValues)) {
								trigger_error('Option '.$label.' of OptionSet '.$optionSetName.' used by field '.(String)$attribute->SchemaName.' has the same Value as another Option in this Set',
										E_USER_WARNING);
							} else {
								/* Store the Option */
								$optionSetValues[$value] = $label;
							}
						}
                                                break;
					case 'Picklist':
						/* Loop through the available Options */
						foreach ($attribute->OptionSet->Options->OptionMetadata as $option) {
							/* Parse the Option */
							$value = (int)$option->Value;
							$label = (String)$option->Label->UserLocalizedLabel->Label[0];
							/* Check for duplicated Values */
							if (array_key_exists($value, $optionSetValues)) {
								trigger_error('Option '.$label.' of OptionSet '.$optionSetName.' used by field '.(String)$attribute->SchemaName.' has the same Value as another Option in this Set',
										E_USER_WARNING);
							} else {
								/* Store the Option */
								$optionSetValues[$value] = $label;
							}
						}
						break;
					default:
                                            echo "DEFAULTOPTIONSET";
						/* If we're using Default, Warn user that the OptionSet handling is not defined */
						trigger_error('No OptionSet handling implemented for Type '.$optionSetType.' used by field '.(String)$attribute->SchemaName.' in Entity '.$this->entityLogicalName,
								E_USER_WARNING);
				}
				
				/* DebugLogging - Identify the OptionSet Values */
				if (self::$debugMode) {
					//foreach ($optionSetValues as $value => $label) {
					//	echo "\t\tOption ".$value.' => '.$label.PHP_EOL;
					//}
				}
                                
				/* Save this OptionSet in the Design */
				if (array_key_exists($optionSetName, $this->optionSets)) {
					/* If this isn't a Global OptionSet, warn of the name clash */
					if (!$optionSetGlobal) {
						trigger_error('OptionSet '.$optionSetName.' used by field '.(String)$attribute->SchemaName.' has a name clash with another OptionSet in Entity '.$this->entityLogicalName,
								E_USER_WARNING);
					}
				} else {
					/* Not already present - store the details */
					$this->optionSets[$optionSetName] = $optionSetValues;
				}
			} else {
				/* Not an OptionSet */
				$optionSetName = NULL;
			}
			/* If this is the Primary Name of the Entity, set the Display Name to match */
			if ((String)$attribute->IsPrimaryName === 'true') {
				$this->displayName = strtolower((String)$attribute->LogicalName);
			}

			/* Add this property to the Object's Property array */
			$this->properties[strtolower((String)$attribute->LogicalName)] = Array(
					'Label' => (String)$attribute->DisplayName->UserLocalizedLabel->Label,
					'Description' => (String)$attribute->Description->UserLocalizedLabel->Label,
                                        'Format' => (String)$attribute->Format,
                                        'MaxLength' => (String)$attribute->MaxLength,
                                        'ImeMode' => (String)$attribute->ImeMode,
					'isCustom' => ((String)$attribute->IsCustomAttribute === 'true'),
					'isPrimaryId' => ((String)$attribute->IsPrimaryId === 'true'),
					'isPrimaryName' => ((String)$attribute->IsPrimaryName === 'true'),
					'Type'  => (String)$attribute->AttributeType,
					'isLookup' => $isLookup,
					'lookupTypes' => $lookupTypes,
					'Create' => ((String)$attribute->IsValidForCreate === 'true'),
					'Update' => ((String)$attribute->IsValidForUpdate === 'true'),
					'Read'   => ((String)$attribute->IsValidForRead === 'true'),
					'RequiredLevel' => $requiredLevel,
					'AttributeOf' => (String)$attribute->AttributeOf,
					'OptionSet' => $optionSetName,
				);
                        
                        /* Debug logging - List properties */
                        if (self::$debugMode) {
                            /*
                            echo "<pre>";
                            print_r($this->properties);
                            echo "</pre>";*/
                        }
                        
			$this->propertyValues[strtolower((String)$attribute->LogicalName)] = Array(
					'Value'  => NULL,
					'Changed' => false,
				);
                        
			/* If appropriate, add this to the Mandatory Field list */
			if ($requiredLevel != 'None' && $requiredLevel != 'Recommended') {
				$this->mandatories[strtolower((String)$attribute->LogicalName)] = $requiredLevel;
			}
		}
                
                /* Store manytomanyrelationships */
                
                /* Store manytoonerelationships */
                
                /* Store OneToManyRelationships */
                foreach($this->entityData->OneToManyRelationships->OneToManyRelationshipMetadata as $OneToManyRelationship){
                    
                    $this->oneToManyRelationships[(string)$OneToManyRelationship->ReferencingEntity] = "";
                    
                }
                
                /* Ensure that this Entity Definition is Cached for next time */
		$auth->setCachedEntityDefinition($this->entityLogicalName, 
				$this->entityData, $this->properties, $this->propertyValues, $this->mandatories,
				$this->optionSets, $this->displayName, $this->entitytypecode, $this->entityDisplayName, 
                                $this->entityDisplayCollectionName, $this->entityDescription, $this->manyToManyRelationships, 
                                $this->manyToOneRelationships, $this->oneToManyRelationships);
                
                
                /* Set EntityValues if specified Entity ID */
                if ($_ID != NULL) {
                        /* Set the ID of Entity record */
			$this->setID($_ID);
                        /* Get the raw XML data */
                        $rawSoapResponse = $auth->retrieveRaw($this);
                        /* NOTE: ParseRetrieveResponse method of AlexaSDK_Entity class, not the AlexaSDK class */
                        $this->ParseRetrieveResponse($auth, $this->LogicalName, $rawSoapResponse);
		}
                
		return;
                
	}
        
        
        /**
	 * 
	 * @param String $property to be fetched
	 * @return value of the property, if it exists & is readable
	 */
	public function __get($property) {
		/* Handle special fields */
		switch (strtoupper($property)) {
			case 'ID':
				return $this->getID();
				break;
			case 'LOGICALNAME':
				return $this->entityLogicalName;
				break;
			case 'DISPLAYNAME':
				if ($this->displayName != NULL) {
					$property = $this->displayName;
				} else {
					return NULL;
				}
				break;
                        case 'OBJECTTYPECODE':
                        case 'ENTITYTYPECODE':
                                if ($this->entitytypecode != NULL) {
					return $this->entitytypecode;
				} else {
					return NULL;
				}
				break;
                        /*case 'ENTITYNAME':
				if ($this->entityDisplayName != NULL) {
					return $this->entityDisplayName;
				} else {
					return NULL;
				}
				break;
                        case 'ENTITYDESCRIPTION':
				if ($this->entityDescription != NULL) {
					return $this->entityDescription;
				} else {
					return NULL;
				}
				break;
                        case 'ENTITIESNAMES':
                        case 'ENTITYCOLLECTIONNAME':
				if ($this->entityDisplayCollectionName != NULL) {
					return $this->entityDisplayCollectionName;
				} else {
					return NULL;
				}
				break;*/
                        
		}
		/* Handle dynamic properties... */
		$property = strtolower($property);
                
		/* Only return the value if it exists & is readable */
		if (array_key_exists($property, $this->properties) && $this->properties[$property]['Read'] === true) {
			return $this->propertyValues[$property]['Value'];
		}
		/* Also check for an AliasedValue */
		if (array_key_exists($property, $this->localProperties) && $this->localProperties[$property]['Read'] === true) {
			return $this->propertyValues[$property]['Value'];
		}
		/* Property is not readable, but does exist - different error message! */
		if (array_key_exists($property, $this->properties) || array_key_exists($property, $this->localProperties)) {
			trigger_error('Property '.$property.' of the '.$this->entityLogicalName.' entity is not Readable', E_USER_NOTICE);
			return NULL;
		}
                
		/* Property doesn't exist - standard error */
		$trace = debug_backtrace();
		trigger_error('Undefined property via __get(): ' . $property 
				. ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
				E_USER_NOTICE);
		return NULL;
	}
        
        /*
        public function getPropertyDefinition($property) { 
            
        }*/
        
        
        /**
	 * 
	 * @param String $property to be changed
	 * @param mixed $value new value for the property
	 */
	public function __set($property, $value) {
		/* Handle special fields */
		switch (strtoupper($property)) {
			case 'ID':
				$this->setID($value);
				return;
			case 'DISPLAYNAME':
				if ($this->displayName != NULL) {
					$property = $this->displayName;
				} else {
					return;
				}
				break;
		}
		/* Handle dynamic properties... */
		$property = strtolower($property);
		/* Property doesn't exist - standard error */
		if (!array_key_exists($property, $this->properties)) {
			$trace = debug_backtrace();
			trigger_error('Undefined property via __set() - ' . $this->entityLogicalName . ' does not support property: ' . $property 
					. ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
					E_USER_NOTICE);
			return;
		}
                
		/* Check that this property can be set in Creation or Update */
		if ($this->properties[$property]['Create'] == false && $this->properties[$property]['Update'] == false) {
			trigger_error('Property '.$property.' of the '.$this->entityLogicalName
					.' entity cannot be set', E_USER_NOTICE);
			return;
		}
                /* If field validation is ON validate field value */
                if ($this->fieldValidation == TRUE){
                    $this->validate($property, $value);
                }
                /*
                if ($this->properties[$property]['Type'] == "DateTime"){
                    $value = strtotime($value);
                }*/
                
                /*
                 * NOTE: For fast work set STRING value with ENTITY ID
                 */
                
		/* If this is a Lookup field, it MUST be set to an Entity of an appropriate type */
		if ($this->properties[$property]['isLookup'] && $value != null) {
			/* Check the new value is an Entity */
			if (!$value instanceOf self) {
				$trace = debug_backtrace();
				trigger_error('Property '.$property.' of the '.$this->entityLogicalName
						.' entity must be a '.get_class()
						. ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
						E_USER_ERROR);
				return;
			}
			/* Check the new value is the right type of Entity */
			if (!in_array($value->entityLogicalName, $this->properties[$property]['lookupTypes'])) {
				$trace = debug_backtrace();
				trigger_error('Property '.$property.' of the '.$this->entityLogicalName
						.' entity must be a '.implode(' or ', $this->properties[$property]['lookupTypes'])
						. ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
						E_USER_ERROR);
				return;
			}
			/* Clear any AttributeOf related to this field */
			$this->clearAttributesOf($property);
		}
                
		/* If this is an OptionSet field, it MUST be set to a valid OptionSetValue
		 * according to the definition of the OptionSet
		 */
		if ($this->properties[$property]['OptionSet'] != NULL) {
			/* Which OptionSet is used? */
			$optionSetName = $this->properties[$property]['OptionSet'];
			/* Container for the final value */
			$optionSetValue = NULL;
			
			/* Handle passing a String value */
			if (is_string($value)) {
				/* Look for an option with this label */
				foreach ($this->optionSets[$optionSetName] as $optionValue => $optionLabel) {
					/* Check for a case-insensitive match */
					if (strcasecmp($value, $optionLabel) == 0) {
						/* Create the Value object */
						$optionSetValue = new AlexaSDK_OptionSetValue($optionValue, $optionLabel);
						break;
                                        }else{
                                            if (array_key_exists($value, $this->optionSets[$optionSetName])) {
                                                    /* Copy the Value object */
                                                    $optionSetValue = $value;
                                            }
                                        }
				}
			}
			/* Handle passing an Integer value */
			if (is_int($value)) {
				/* Look for an option with this value */
				if (array_key_exists($value, $this->optionSets[$optionSetName])) {
					/* Create the Value object */
					$optionSetValue = new AlexaSDK_OptionSetValue($value, $this->optionSets[$optionSetName][$value]);
				}else{
                                    if (array_key_exists($value, $this->optionSets[$optionSetName])) {
                                            /* Copy the Value object */
                                            $optionSetValue = $value;
                                    }
                                }
			}
			/* Handle passing an OptionSetValue */
			if ($value instanceof AlexaSDK_OptionSetValue) {
				/* Check it's a valid option (by Value) */
				if (array_key_exists($value->Value, $this->optionSets[$optionSetName])) {
					/* Copy the Value object */
					$optionSetValue = $value;
				}
			}
                        
                        /* Handle passing an Boolean value */
                        //if (is_bool($value)) {
                            //TODO: Add boolean OptionSet handling 
                          //  $value = ($value) ? true : false;
                        //}
			
			/* Check we found a valid OptionSetValue */
			if ($optionSetValue != NULL) {
				/* Set the value to be retained */
				$value = $optionSetValue;
				/* Clear any AttributeOf related to this field */
				$this->clearAttributesOf($property);
                        } elseif($value == "" || $value == NULL) {
                                $value = NULL;
                        }else {
				$trace = debug_backtrace();
				trigger_error('Property '.$property.' of the '.$this->entityLogicalName
						.' entity must be a valid OptionSetValue of type '.$optionSetName
						. ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
						E_USER_WARNING);
				return;
			}
		}	
                
                
                if ($this->propertyValues[$property]['Value'] != $value){
                    
                    /* Update the property value with whatever value was passed */
                    $this->propertyValues[$property]['Value'] = $value;
                    /* Mark the property as changed */
                    $this->propertyValues[$property]['Changed'] = true;
                }
	}
        
        
        /**
	 * Validate property value by rules described in entity metadata and custom rules for fields
         * 
	 * @param String $property to be changed
	 * @param mixed $value new value for the property
         * @return mixed false, if there is no errors and validation passed, array if validation errors exists
	 */
        public function validate($property, $value) {
            
                $errorsFound = false;
                
                if (isset($this->mandatories[$property]) && !$value){
                     $this->errors[$property] = $this->getPropertyLabel($property)." is required";
                }
                
                switch($this->properties[$property]["Type"]){
                    
                        case "String":

                                if ($this->properties[$property]["MaxLength"] && (strlen($value) > $this->properties[$property]["MaxLength"])){
                                        $this->errors[$property] = "Must be less than ".$this->properties[$property]['MaxLength']." characters" ;
                                }

                                switch($this->properties[$property]["Format"]){
                                        case "Text":
                                             if ($value && !$this->validator->validateItem($value, 'anything')){
                                                 $this->errors[$property] = "Incorrect text value";
                                             }
                                        break;
                                        case "Email":
                                            if ($value && !$this->validator->validateItem($value, 'email')){
                                                 $this->errors[$property] = "Incorrect email";
                                             }
                                        break;
                                        default:
                                           // echo "No text validation for field ".$property." ".$this->properties[$property]["Format"]." <br />";
                                        break;
                                }
                        break;
                        /*case "DateTime":
                            if ($value && !$this->validator->validateItem($value, 'date')){
                                $this->errors[$property] = "Incorrect date";
                                echo "DateTime validation for field ".$property." <br />";
                            }
                        break;*/
                        case "Boolean":
                        break;
                        case "Picklist":
                        break;
                        case "Lookup":
                        break;
                        case "Integer":
                            if ($value && !$this->validator->validateItem($value, 'amount')){
                                $this->errors[$property] = "Incorrect text value";
                            }
                        break;
                        case "Double":
                            if ($value && !$this->validator->validateItem($value, 'float')){
                                $this->errors[$property] = "Incorrect text value";
                            }
                        break;
                        case "Money":
                            if ($value && !$this->validator->validateItem($value, 'number')){
                                $this->errors[$property] = "Incorrect text value";
                            }
                        break;
                        case "Memo":
                        break;
                        default:
                            if (!$this->properties[$property]["isLookup"]){
                            }
                        break;
                    
                }
                
                
                if (!isset($this->errors[$property])){
                    $errorsFound = true;
                }
            
                return $errorsFound;
        }
        
        
        
        /**
	 * Check if a property exists on this entity.  Called by isset().
	 * Note that this implementation does not check if the property is actually a non-null value.
	 *
	 * @param String $property to be checked
	 * @return boolean true, if it exists & is readable
	 */
	public function __isset($property) {
		/* Handle special fields */
		switch (strtoupper($property)) {
			case 'ID':
				return ($this->entityID == NULL);
				break;
			case 'LOGICALNAME':
				return true;
				break;
			case 'DISPLAYNAME':
				if ($this->displayName != NULL) {
					$property = $this->displayName;
				} else {
					return false;
				}
				break;
		}
		/* Handle dynamic properties... */
		$property = strtolower($property);
		/* Value "Is Set" if it exists as a property, and is readable */
		/* Note: NULL values count as "Set" -> use "Empty" on the return of "Get" to check for NULLs */
		if (array_key_exists($property, $this->properties) && $this->properties[$property]['Read'] === true) {
			return true;
		}
		/* Also check if this is an AliasedValue */
		if (array_key_exists($property, $this->localProperties) && $this->localProperties[$property]['Read'] === true) {
			return true;
		}
		return false;
	}
	
	/**
	 * Utility function to clear all "AttributeOf" fields relating to the base field
	 * @param String $baseProperty
	 */
	private function clearAttributesOf($baseProperty) {
		/* Loop through all the properties */
		foreach ($this->properties as $property => $propertyDetails) {
			/* Check if this Property is an "AttributeOf" the base Property */
			if ($propertyDetails['AttributeOf'] == $baseProperty) {
				/* Clear the property value */
				$this->propertyValues[$property]['Value'] = NULL;
			}
		}
	}
	
	/**
	 * @return String description of the Entity including Type, DisplayName and ID
	 */
	public function __toString() {
		/* Does this Entity have a DisplayName part? */
		if ($this->displayName != NULL) {
			/* Use the magic __get to determine the DisplayName */
			$displayName = $this->DisplayName;
		} else {
			/* No DisplayName */
			$displayName = '';
		}
		/* EntityType: Display Name <GUID> */
		//return $this->entityLogicalName.$displayName.'<'.$this->getID().'>';
                
                /* Display Name */
                return $displayName;
	}
	
	/**
	 * Reset all changed values to unchanged
	 */
	public function reset() {
		/* Loop through all the properties */
		foreach ($this->propertyValues as &$property) {
			$property['Changed'] = false;
		}
	}
	
	/**
	 * Check if a property has been changed since creation of the Entity
	 * @param String $property
	 * @return boolean
	 */
	public function isChanged($property) {
		/* Dynamic properties are all stored in lowercase */
		$property = strtolower($property);
		/* Property doesn't exist - standard error */
		if (!array_key_exists($property, $this->propertyValues)) {
			$trace = debug_backtrace();
			trigger_error('Undefined property via isChanged(): ' . $property
					. ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
					E_USER_NOTICE);
			return;
		}
		return $this->propertyValues[$property]['Changed'];
	}
        
        
        public function getChangedProperties(){
                $changedPropertyValues = array();
            
                foreach($this->propertyValues as $propertyKey => $propertyValue){
                    if ($propertyValue['Changed']){
                        $changedPropertyValues[$propertyKey] = $propertyValue;
                    }
                }
                return $changedPropertyValues;
        }
        
        /**
	 * Private utility function to get the ID field; enforces NULL --> EmptyGUID
	 * @ignore
	 */
	private function getID() {
		if ($this->entityID == NULL) return self::EmptyGUID;
		else return $this->entityID;
	}
	
	/**
	 * Private utility function to set the ID field; enforces "Set Once" logic
	 * @param String $value
	 * @throws Exception if the ID is already set
	 */
	private function setID($value) {
		/* Only allow setting the ID once */
		if ($this->entityID != NULL) {
			throw new Exception('Cannot change the ID of an Entity');
		}
		$this->entityID = $value;
	}
        
        /**
	 * Utility function to check all mandatory fields are filled
	 * @param Array $details populated with any failures found
	 * @return boolean true if all mandatories are filled
	 */
	public function checkMandatories(Array &$details = NULL) {
		/* Assume true, until proved false */
		$allMandatoriesFilled = true;
		$missingFields = Array();
		/* Loop through all the Mandatory fields */
		foreach ($this->mandatories as $property => $reason) {
			/* If this is an attribute of another property, check that property instead */
			if ($this->properties[$property]['AttributeOf'] != NULL) {
				/* Check the other property */
				$propertyToCheck = $this->properties[$property]['AttributeOf'];
			} else {
				/* Check this property */
				$propertyToCheck = $property;
			}
			if ($this->propertyValues[$propertyToCheck]['Value'] == NULL) {
				/* Ignore values that can't be in Create or Update */
				if ($this->properties[$propertyToCheck]['Create'] || $this->properties[$propertyToCheck]['Update']) {
					$missingFields[$propertyToCheck] = $reason;
					$allMandatoriesFilled = false;
				}
			}
		}
		/* If not all Mandatories were filled, and we have been given a Details array, populate it */
		if (is_array($details) && $allMandatoriesFilled == false) {
			$details += $missingFields;
		}
		/* Return the result */
		return $allMandatoriesFilled;
	}
        
        
        /**
	 * Create a DOMNode that represents this Entity, and can be used in a Create or Update 
	 * request to the CRM server
	 * 
	 * @param boolean $allFields indicates if we should include all fields, or only changed fields
	 */
	public function getEntityDOM($allFields = false) {
		/* Generate the Entity XML */
		$entityDOM = new DOMDocument();
		$entityNode = $entityDOM->appendChild($entityDOM->createElement('entity'));
		$entityNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance');
		$attributeNode = $entityNode->appendChild($entityDOM->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'b:Attributes'));
		$attributeNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic');
		/* Loop through all the attributes of this Entity */
		foreach ($this->properties as $property => $propertyDetails) {
			/* Only include changed properties */
			if ($this->propertyValues[$property]['Changed']) {
				/* Create a Key/Value Pair of String/Any Type */
				$propertyNode = $attributeNode->appendChild($entityDOM->createElement('b:KeyValuePairOfstringanyType'));
				/* Set the Property Name */
				$propertyNode->appendChild($entityDOM->createElement('c:key', $property));
				/* Check the Type of the Value */
				if ($propertyDetails['isLookup']) {
                                        
					/* Special handling for Lookups - use an EntityReference, not the AttributeType */
                                        
					$valueNode = $propertyNode->appendChild($entityDOM->createElement('c:value'));
                                        
                                        if ($this->propertyValues[$property]['Value'] != NULL){
                                            $valueNode->setAttribute('i:type', 'b:EntityReference');
                                            $valueNode->appendChild($entityDOM->createElement('b:Id', ($this->propertyValues[$property]['Value']) ? $this->propertyValues[$property]['Value']->ID : ""));
                                            $valueNode->appendChild($entityDOM->createElement('b:LogicalName', ($this->propertyValues[$property]['Value']) ?  $this->propertyValues[$property]['Value']->entityLogicalName : ""));
                                            $valueNode->appendChild($entityDOM->createElement('b:Name'))->setAttribute('i:nil', 'true');
                                        }else{
                                            $valueNode->setAttribute('i:nil', 'true');
                                        }
                                }else if(strtolower($propertyDetails['Type']) == "money") {
                                    
                                    $valueNode = $propertyNode->appendChild($entityDOM->createElement('c:value'));
                                    
                                    if ($this->propertyValues[$property]['Value']){
                                        $valueNode->setAttribute('i:type', 'b:Money');
                                        $valueNode->appendChild($entityDOM->createElement('b:Value', $this->propertyValues[$property]['Value']));
                                    }else{
                                        $valueNode->setAttribute('i:nil', 'true');
                                    }
                                }else if (strtolower($propertyDetails['Type']) == "datetime") {

                                        $valueNode = $propertyNode->appendChild($entityDOM->createElement('c:value'));

                                        if ($this->propertyValues[$property]['Value']){
                                            $valueNode->setAttribute('i:type', 'd:dateTime');
                                            $valueNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://www.w3.org/2001/XMLSchema');
                                            $valueNode->appendChild(new DOMText(gmdate("Y-m-d\TH:i:s\Z",$this->propertyValues[$property]['Value'])));
                                        }else{
                                            $valueNode->setAttribute('i:nil', 'true');
                                        }
                                }else if (strtolower($propertyDetails['Type']) == "picklist"){
                                        $valueNode = $propertyNode->appendChild($entityDOM->createElement('c:value'));

                                        if ($this->propertyValues[$property]['Value']){
                                            $valueNode->setAttribute('i:type', 'd:OptionSetValue');
                                            $valueNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/2011/Contracts');
                                            $valueNode->appendChild($entityDOM->createElement('b:Value', $this->propertyValues[$property]['Value']));
                                        }else{
                                            $valueNode->setAttribute('i:nil', 'true');
                                        }
                                }else {
					/* Determine the Type, Value and XML Namespace for this field */
					$xmlValue = $this->propertyValues[$property]['Value'];
					$xmlValueChild = NULL;
					$xmlType = strtolower($propertyDetails['Type']);
					$xmlTypeNS = 'http://www.w3.org/2001/XMLSchema';
					/* Special Handing for certain types of field */
					switch (strtolower($propertyDetails['Type'])) {
						case 'memo':
							/* Memo - This gets treated as a normal String */
							$xmlType = 'string';
							break;
						case 'integer':
							/* Integer - This gets treated as an "int" */
							$xmlType = 'int';
							break;
						case 'uniqueidentifier':
							/* Uniqueidentifier - This gets treated as a guid */
							$xmlType = 'guid';
							break;
						case 'state':
						case 'status':
							/* OptionSetValue - Just get the numerical value, but as an XML structure */
							$xmlType = 'OptionSetValue';
							$xmlTypeNS = 'http://schemas.microsoft.com/xrm/2011/Contracts';
							$xmlValue = NULL;
							$xmlValueChild = $entityDOM->createElement('b:Value', $this->propertyValues[$property]['Value']);
							break;
						case 'boolean':
							/* Boolean - Just get the numerical value */
							$xmlValue = $this->propertyValues[$property]['Value'];
							break;
                                                
						case 'string':
						case 'int':
						case 'decimal':
                                                case 'double':
						case 'guid':
							/* No special handling for these types */
							break;
						default:
							/* If we're using Default, Warn user that the XML handling is not defined */
							trigger_error('No Create/Update handling implemented for type '.$propertyDetails['Type'].' used by field '.$property,
									E_USER_WARNING);
					}
					/* Now create the XML Node for the Value */
					$valueNode = $propertyNode->appendChild($entityDOM->createElement('c:value'));
					/* Set the Type of the Value */
					$valueNode->setAttribute('i:type', 'd:'.$xmlType);
					$valueNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', $xmlTypeNS);
					/* If there is a child node needed, append it */
					if ($xmlValueChild != NULL) $valueNode->appendChild($xmlValueChild);
					/* If there is a value, set it */
					if ($xmlValue != NULL) $valueNode->appendChild(new DOMText($xmlValue));
				}
			}
		}
		/* Entity State */
		$entityNode->appendChild($entityDOM->createElement('b:EntityState'))->setAttribute('i:nil', 'true');
		/* Formatted Values */
		$formattedValuesNode = $entityNode->appendChild($entityDOM->createElement('b:FormattedValues'));
		$formattedValuesNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic');
		/* Entity ID */
		$entityNode->appendChild($entityDOM->createElement('b:Id', $this->getID()));
		/* Logical Name */
		$entityNode->appendChild($entityDOM->createElement('b:LogicalName', $this->entityLogicalName));
		/* Related Entities */
		$relatedEntitiesNode = $entityNode->appendChild($entityDOM->createElement('b:RelatedEntities'));
		$relatedEntitiesNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic');
		/* Return the root node for the Entity */                
		return $entityNode;
	}
        
        
        /**
	 * Generate an Entity based on a particular Logical Name - will try to be as Strongly Typed as possible
	 * 
	 * @param AlexaSDK $conn
	 * @param String $entityLogicalName
	 * @return AlexaSDK_Entity of the specified type, or a generic Entity if no Class exists
	 */
	public static function fromLogicalName(AlexaSDK $conn, $entityLogicalName) {
		/* Determine which Class we will create */
		$entityClassName = self::getClassName($entityLogicalName);
		/* If a specific class for this Entity doesn't exist, use the Entity class */
		if (!class_exists($entityClassName, true)) {
			$entityClassName = 'AlexaSDK_Entity';
		}
		/* Create a new instance of the Class */
		return new $entityClassName($conn, $entityLogicalName);
	}
        
        
        /**
	 * Generate an Entity from the DOM object that describes its properties
	 * 
	 * @param AlexaSDK $conn
	 * @param String $entityLogicalName
	 * @param DOMElement $domNode
	 * @return AlexaSDK_Entity of the specified type, with the properties found in the DOMNode
	 */
	public static function fromDOM(AlexaSDK $conn, $entityLogicalName, DOMElement $domNode) {
		/* Create a new instance of the appropriate Class */
		$entity = self::fromLogicalName($conn, $entityLogicalName);
		
		/* Store values from the main RetrieveResult node */
		$relatedEntitiesNode = NULL;
		$attributesNode = NULL;
		$formattedValuesNode = NULL;
		$retrievedEntityName = NULL;
		$entityState = NULL;
		
 		/* Loop through the nodes directly beneath the RetrieveResult node */
 		foreach ($domNode->childNodes as $childNode) {
			switch ($childNode->localName) {
				case 'RelatedEntities':
					$relatedEntitiesNode = $childNode;
					break;
				case 'Attributes':
					$attributesNode = $childNode;
					break;
				case 'FormattedValues':
					$formattedValuesNode = $childNode;
					break;
				case 'Id':
					/* Set the Entity ID */
					$entity->ID = $childNode->textContent;
					break;
		 		case 'LogicalName':
		 			$retrievedEntityName = $childNode->textContent;
		 			break;
		 		case 'EntityState':
		 			$entityState = $childNode->textContent;
		 			break;
			}
 		}
 		
 		/* Verify that the Retrieved Entity Name matches the expected one */
 		if ($retrievedEntityName != $entityLogicalName) {
 			trigger_error('Expected to get a '.$entityLogicalName.' but actually received a '.$retrievedEntityName.' from the server!',
 					E_USER_WARNING);
 		}
		
 		/* Log the Entity State - Never seen this used! */
 		//if (self::$debugMode) echo 'Entity <'.$entity->ID.'> has EntityState: '.$entityState.PHP_EOL;
 		
 		/* Parse the Attributes & FormattedValues to set the properties of the Entity */
 		$entity->setAttributesFromDOM($conn, $attributesNode, $formattedValuesNode);
 		
		/* Before returning the Entity, reset it so all fields are marked unchanged */
		$entity->reset();
		return $entity;
	}
        
        
        /**
	 * 
	 * @param AlexaSDK $conn
	 * @param DOMElement $attributesNode
	 * @param DOMElement $formattedValuesNode
	 * @ignore
	 */
	private function setAttributesFromDOM(AlexaSDK $conn, DOMElement $attributesNode, DOMElement $formattedValuesNode) {
		/* First, parse out the FormattedValues - these will be required when analysing Attributes */
		$formattedValues = Array();
		/* Identify the FormattedValues */
		$keyValueNodes = $formattedValuesNode->getElementsByTagName('KeyValuePairOfstringstring');
		/* Add the Formatted Values in the Key/Value Pairs of String/String to the Array */
		self::addFormattedValues($formattedValues, $keyValueNodes);
                
                foreach($formattedValues as $key => $value){
                    $this->formattedValues[$key] = $value;
                }
		
		/* Identify the Attributes */
		$keyValueNodes = $attributesNode->getElementsByTagName('KeyValuePairOfstringanyType');
		foreach ($keyValueNodes as $keyValueNode) {
			/* Get the Attribute name (key) */
			$attributeKey = $keyValueNode->getElementsByTagName('key')->item(0)->textContent;
			/* Check the Value Type */
			$attributeValueType = $keyValueNode->getElementsByTagName('value')->item(0)->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type');
			/* Strip any Namespace References from the Type */
			$attributeValueType = self::stripNS($attributeValueType);
			/* Get the basic Text Content of the Attribute */
			$attributeValue = $keyValueNode->getElementsByTagName('value')->item(0)->textContent;
			/* Handle the Value in an appropriate way */
			switch ($attributeValueType) {
				case 'string':
				case 'guid':
					/* String, Guid - just take the attribute text content */
					$storedValue = $attributeValue;
					break;
				case 'dateTime':
					/* Date/Time - Parse this into a PHP Date/Time */
					//$storedValue = date("m/d/y", self::parseTime($attributeValue, '%Y-%m-%dT%H:%M:%SZ'));
                                        $storedValue = self::parseTime($attributeValue, '%Y-%m-%dT%H:%M:%SZ');
					break;
                                case "BooleanManagedProperty":
				case 'boolean':
					/* Boolean - Map "True" to TRUE, all else is FALSE (case insensitive) */
					$storedValue = (strtolower($attributeValue) == 'true' ? true : false);
					break;
				case 'decimal':
					/* Decimal - Cast the String to a Float */
					$storedValue = (float)$attributeValue;
					break;
                                case 'double':
					/* Decimal - Cast the String to a Float */
					$storedValue = (float)$attributeValue;
					break;
				case 'int':
					/* Int - Cast the String to an Int */
					$storedValue = (int)$attributeValue;
					break;
                                case 'Money':
					/* Decimal - Cast the String to a Float */
					$storedValue = (float)$attributeValue;
					break;
				case 'OptionSetValue':
					/* OptionSetValue - We need the Numerical Value for Updates, Text for Display */
					$optionSetValue = (int)$attributeValue = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->textContent;
					$storedValue = new AlexaSDK_OptionSetValue($optionSetValue, $formattedValues[$attributeKey]);
					/* Check if we have a matching "xxxName" property, and set that too */
					if (array_key_exists($attributeKey.'name', $this->properties)) {
						/* Don't overwrite something that's already set */
						if ($this->propertyValues[$attributeKey.'name']['Value'] == NULL) {
							$this->propertyValues[$attributeKey.'name']['Value'] = $formattedValues[$attributeKey];
						}
					}
					break;
				case 'EntityReference':
					/* EntityReference - We need the Id and Type to create a placeholder Entity */
					$entityReferenceType = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('LogicalName')->item(0)->textContent;
					$entityReferenceId = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Id')->item(0)->textContent;
					/* Also get the Name of the Entity - might be able to store this for View */
					$entityReferenceName = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Name')->item(0)->textContent;
					/* Create the Placeholder Entity */
					$storedValue = self::fromLogicalName($conn, $entityReferenceType);
					$storedValue->ID = $entityReferenceId;
					/* Check if we have a matching "xxxName" property, and set that too */
					if (array_key_exists($attributeKey.'name', $this->properties)) {
						/* Don't overwrite something that's already set */
						if ($this->propertyValues[$attributeKey.'name']['Value'] == NULL) {
							$this->propertyValues[$attributeKey.'name']['Value'] = $entityReferenceName;
						}
						/* If the Entity has a defined way to get the Display Name, use it too */
						if ($storedValue->displayName != NULL) {
							$storedValue->propertyValues[$storedValue->displayName]['Value'] = $entityReferenceName;
						}
					}
					break;
				case 'AliasedValue':
					/* If there is a "." in the AttributeKey, it's a proper "Entity" alias */
					/* Otherwise, it's an Alias for an Aggregate Field */
					if (strpos($attributeKey, '.') === FALSE) {
						/* This is an Aggregate Field alias - do NOT create an Entity */
						$aliasedFieldName = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('AttributeLogicalName')->item(0)->textContent;
						/* Create a new Attribute on this Entity for the Alias */
						$this->localProperties[$attributeKey] = Array(
								'Label' => 'AliasedValue: '.$attributeKey,
								'Description' => 'Aggregate field with alias '.$attributeKey.' based on field '.$aliasedFieldName,
								'isCustom' => true,
								'isPrimaryId' => false,
								'isPrimaryName' => false,
								'Type'  => 'AliasedValue',
								'isLookup' => false,
								'lookupTypes' => NULL,
								'Create' => false,
								'Update' => false,
								'Read'   => true,
								'RequiredLevel' => 'None',
								'AttributeOf' => NULL,
								'OptionSet' => NULL,
							);
						$this->propertyValues[$attributeKey] = Array(
								'Value'  => NULL,
								'Changed' => false,
							);
						/* Determine the Value for this field */
						$valueType =  $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->getAttribute('type');
						$storedValue = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->textContent;
					} else {
						/* For an AliasedValue, we need to find the Alias first */
						list($aliasName, $aliasedFieldName) = explode('.', $attributeKey);
						/* Get the Entity type that is being Aliased */
						$aliasEntityName = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('EntityLogicalName')->item(0)->textContent;
						/* Get the Field of the Entity that is being Aliased */
						$aliasedFieldName = $keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('AttributeLogicalName')->item(0)->textContent;
						/* Next, check if this Alias already has been used */
						if (array_key_exists($aliasName, $this->propertyValues)) {
							/* Get the existing Entity */
							$storedValue = $this->propertyValues[$aliasName]['Value'];
							/* Check if the existing Entity is NULL */
							if ($storedValue == NULL) {
								/* Create a new Entity of the appropriate type */
								$storedValue = self::fromLogicalName($conn, $aliasEntityName);
								/* Alias overlaps with normal field - check this is allowed */
								if (!in_array($aliasEntityName, $this->properties[$aliasName]['lookupTypes'])) {
									trigger_error('Alias '.$aliasName.' overlaps and existing field of type '.implode(' or ', $this->properties[$aliasName]['lookupTypes'])
											.' but is being set to a '.$aliasEntityName,
											E_USER_WARNING);
								}
							} else {
								/* Check it's the right type */
								if ($storedValue->logicalName != $aliasEntityName) {
									trigger_error('Alias '.$aliasName.' was created as a '.$storedValue->logicalName.' but is now referenced as a '.$aliasEntityName.' in field '.$attributeKey,
											E_USER_WARNING);
								}
							}
						} else {
							/* Create a new Entity of the appropriate type */
							$storedValue = self::fromLogicalName($conn, $aliasEntityName);
							/* Create a new Attribute on this Entity for the Alias */
							$this->localProperties[$aliasName] = Array(
									'Label' => 'AliasedValue: '.$aliasName,
									'Description' => 'Related '.$aliasEntityName.' with alias '.$aliasName,
									'isCustom' => true,
									'isPrimaryId' => false,
									'isPrimaryName' => false,
									'Type'  => 'AliasedValue',
									'isLookup' => true,
									'lookupTypes' => NULL,
									'Create' => false,
									'Update' => false,
									'Read'   => true,
									'RequiredLevel' => 'None',
									'AttributeOf' => NULL,
									'OptionSet' => NULL,
								);
							$this->propertyValues[$aliasName] = Array(
									'Value'  => NULL,
									'Changed' => false,
								);
						}
						/* Re-create the DOMElement for just this Attribute */
						$aliasDoc = new DOMDocument();
						$aliasAttributesNode = $aliasDoc->appendChild($aliasDoc->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'b:Attributes'));
						$aliasAttributeNode = $aliasAttributesNode->appendChild($aliasDoc->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'b:KeyValuePairOfstringanyType'));
						$aliasAttributeNode->appendChild($aliasDoc->createElementNS('http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'c:key', $aliasedFieldName));
						$aliasAttributeValueNode = $aliasAttributeNode->appendChild($aliasDoc->createElementNS('http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'c:value'));
						/* Ensure we have all the child nodes of the Value */
						foreach ($keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->childNodes as $child){
							$aliasAttributeValueNode->appendChild($aliasDoc->importNode($child, true));
						}
						/* Ensure we have the Type attribute, with Namespace */
						$aliasAttributeValueNode->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'i:type', 
								$keyValueNode->getElementsByTagName('value')->item(0)->getElementsByTagName('Value')->item(0)->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'type'));
						/* Re-create the DOMElement for this Attribute's FormattedValue */
						$aliasFormattedValuesNode = $aliasDoc->appendChild($aliasDoc->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'b:FormattedValues'));
						$aliasFormattedValuesNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic');
						/* Check if there is a formatted value to add */
						if (array_key_exists($attributeKey, $formattedValues)) {
							$aliasFormattedValueNode = $aliasFormattedValuesNode->appendChild($aliasDoc->createElementNS('http://schemas.microsoft.com/xrm/2011/Contracts', 'b:KeyValuePairOfstringstring'));
							$aliasFormattedValueNode->appendChild($aliasDoc->createElementNS('http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'c:key', $aliasedFieldName));
							$aliasFormattedValueNode->appendChild($aliasDoc->createElementNS('http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'c:value', $formattedValues[$attributeKey]));
						}
						/* Now set the DOM values on the Entity */
						$storedValue->setAttributesFromDOM($conn, $aliasAttributesNode, $aliasFormattedValuesNode);
						/* Finally, ensure that this is stored on the Entity using the Alias */
						$attributeKey = $aliasName;
					}
					break;
				default:
					trigger_error('No parse handling implemented for type '.$attributeValueType.' used by field '.$attributeKey,
							E_USER_WARNING);
					$attributeValue = $keyValueNode->getElementsByTagName('value')->item(0)->C14N();
					/* Check for a Formatted Value */
					if (array_key_exists($attributeKey, $formattedValues)) {
						$storedValue = Array('XML' => $attributeValue, 'FormattedText' => $formattedValues[$attributeKey]);
					} else {
						$storedValue = $attributeValue;
					}
			}
			/* Bypass __set, and set the Value directly in the Properties array */
			$this->propertyValues[$attributeKey]['Value'] = $storedValue;
			/* If we have just set the Primary ID of the Entity, update the ID field if necessary */
			/* Note that "localProperties" (AliasedValues) cannot be a Primary ID */
			if (array_key_exists($attributeKey, $this->properties) && $this->properties[$attributeKey]['isPrimaryId'] && $this->entityID == NULL) {
				/* Only if the new value is valid */
				if ($storedValue != NULL && $storedValue != self::EmptyGUID) {
					$this->entityID = $storedValue;
				}
			}
		}
	}
        
        
        /**
	 * Print a human-readable summary of the Entity with all details and fields
	 * 
	 * @param boolean $recursive if TRUE, prints full details for all sub-entities as well
	 * @param int $tabLevel the started level of indentation used (tabs)
	 * @param boolean $printEmpty if TRUE, prints the details of NULL fields
	 */
	public function printDetails($recursive = false, $tabLevel = 0, $printEmpty = true) {
		/* Print the Entity Summary at current Tab level */
		echo str_repeat("\t", $tabLevel).$this->displayName.' ('.$this->getURL(true).')'.PHP_EOL;
		/* Increment the tabbing level */
		$tabLevel++;
		$linePrefix = str_repeat("\t", $tabLevel);
		/* Get a list of properties of this Entity, in Alphabetical order */
		$propertyList = array_keys($this->propertyValues);
		sort($propertyList);
		/* Loop through each property */
		foreach ($propertyList as $property) {
			/* Get the details of the Property */
			if (array_key_exists($property, $this->properties)) {
				$propertyDetails = $this->properties[$property];
			} else {
				$propertyDetails = $this->localProperties[$property];
			}
			
			/* In Recursive Mode, don't display "AttributeOf" fields */
			if ($recursive && $propertyDetails['AttributeOf'] != NULL) continue;
			/* Don't print NULL fields if printEmpty is FALSE */
			if (!$printEmpty && $this->propertyValues[$property]['Value'] == NULL) continue;
			/* Output the Property Name & Description */
			echo $linePrefix.$property.' ['.$propertyDetails['Label'].']: ';
			/* For NULL values, just output NULL and the Type on one line */
			if ($this->propertyValues[$property]['Value'] == NULL) {
				echo 'NULL ('.$propertyDetails['Type'].')'.PHP_EOL;
				continue;
			} else {
				echo PHP_EOL;
			}
			/* Handle the Lookup types */
			if ($propertyDetails['isLookup']) {
				/* EntityReference - Either just summarise the Entity, or Recurse */
				if ($recursive) {
					$this->propertyValues[$property]['Value']->printDetails($recursive, $tabLevel+1);
				} else {
					echo $linePrefix."\t".$this->propertyValues[$property]['Value'].PHP_EOL;
				}
				continue;
			}
			/* Any other Property Type - depending on its Type */
			switch (strtolower($propertyDetails['Type'])) {
				case 'datetime':
					/* Date/Time - Print this as a formatted Date/Time */
					echo $linePrefix."\t".date('Y-m-d H:i:s P', $this->propertyValues[$property]['Value']).PHP_EOL;
					break;
				case 'boolean':
					/* Boolean - Print as TRUE or FALSE */
					if ($this->propertyValues[$property]['Value']) {
						echo $linePrefix."\t".'('.$propertyDetails['Type'].') TRUE'.PHP_EOL;
					} else {
						echo $linePrefix."\t".'('.$propertyDetails['Type'].') FALSE'.PHP_EOL;
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
                                 
					/* Just cast it to a String to display */
					echo $linePrefix."\t".'('.$propertyDetails['Type'].') '. $this->propertyValues[$property]['Value'].PHP_EOL;
					break;
				default:
					/* If we're using Default, Warn user that the output handling is not defined */
					trigger_error('No output handling implemented for type '.$propertyDetails['Type'].' used by field '.$property,
							E_USER_WARNING);
					/* Use print_r to display unknown formats */
					echo $linePrefix."\t".'('.$propertyDetails['Type'].') '.print_r($this->propertyValues[$property]['Value'], true).PHP_EOL;
			}
		}
	}
        
        
        
        public function getEntityFields(){
                return $this->properties;
        }
        
        public function getPropertyValues(){
                return $this->propertyValues;
        }
        
        public function getPropertyKeys(){
                return array_keys($this->propertyValues);
        }
        
        
        
        /**
	 * Get a URL that can be used to directly open the Entity Details on the CRM
	 * 
	 * @param boolean $absolute If true, include the full domain; otherwise, just return a relative URL.
	 * @return NULL|string the URL for the Entity on the CRM
	 */
	public function getURL($absolute = false) {
		/* Cannot return a URL for an Entity with no ID */
		if ($this->entityID == NULL) return NULL;
		/* The "relative" part of the Entity URL */
		$entityURL = 'main.aspx?etn='.$this->entityLogicalName.'&pagetype=entityrecord&id='.$this->entityID;
		/* If we want an Absolute URL, pre-pend the Domain for the Entity */
		if ($absolute) {
			return $this->entityDomain.$entityURL;
		} else {
			return $entityURL;
		}
	}
        
        /**
	 * Update the Domain Name that this Entity will use when constructing an absolute URL
	 * @param AlexaSDK $auth Connection to the Server currently used
	 */
	protected function setEntityDomain(AlexaSDK $auth) {
		/* Get the URL of the Organization */
		$organizationURL = $auth->getOrganizationURI();
		$urlDetails = parse_url($organizationURL);
		/* Generate the base URL for Entities */
		$domainURL = $urlDetails['scheme'].'://'.$urlDetails['host'].'/';
		/* If the Organization Unique Name is part of the Organization URL, add it to the Domain */
		if (strstr($organizationURL, '/'.$auth->getOrganization().'/') !== FALSE) {
			$domainURL = $domainURL . $auth->getOrganization() .'/';
		}
		/* Update the Entity */
		$this->entityDomain = $domainURL;
	}
        
        
        /**
	 * Get the possible values for a particular OptionSet property
	 * 
	 * @param String $property to list values for
	 * @return Array list of the available options for this Property
	 */
	public function getOptionSetValues($property) {
		/* Check that the specified property exists */
		$property = strtolower($property);
		if (!array_key_exists($property, $this->properties)) return NULL;
		/* Check that the specified property is indeed an OptionSet */
		$optionSetName = $this->properties[$property]['OptionSet'];
		if ($optionSetName == NULL) return NULL;
		/* Return the available options for this property */
		return $this->optionSets[$optionSetName];
	}
        
        
        /**
	 * Get the label for a field
	 * 
	 * @param String $property
	 * @return string
	 */
	public function getPropertyLabel($property) {
		/* Handle dynamic properties... */
		$property = strtolower($property);
		/* Only return the value if it exists & is readable */
		if (array_key_exists($property, $this->properties)) {
			return $this->properties[$property]['Label'];
		}
		/* Also check for an AliasedValue */
		if (array_key_exists($property, $this->localProperties)) {
			return $this->localProperties[$property]['Label'];
		}
		/* Property doesn't exist, return empty string */
		return '';
	}
        
        
        /**
	 * Get Formatted value for property
         * if formatted value doesn't exists, returned propertyValue
	 * 
	 * @param String $property
         * @param Int $timezoneoffset offset in minutes to correct DateTime value
	 * @return string
	 */
        public function getFormattedValue($property, $timezoneoffset = NULL){
                /* Handle special fields */
		switch (strtoupper($property)) {
			case 'ID':
				return $this->getID();
				break;
			case 'LOGICALNAME':
				return $this->entityLogicalName;
				break;
			case 'DISPLAYNAME':
				if ($this->displayName != NULL) {
					$property = $this->displayName;
				} else {
					return NULL;
				}
				break;
		}
            
                /* Handle dynamic properties... */
		$property = strtolower($property);
                
                if ($timezoneoffset != NULL && array_key_exists($property, $this->properties) && $this->properties[$property]['Type'] == "DateTime" && $this->properties[$property]['Read'] === true){
                        if($this->propertyValues[$property]['Value'] == NULL){
                            return "";
                        }else if ($this->properties[$property]['Format'] == "DateAndTime"){
                            return date("n/j/Y H:i", $this->propertyValues[$property]['Value'] - $timezoneoffset * 60);
                        }else if($this->properties[$property]['Format'] == "DateOnly"){
                            return date("n/j/Y", $this->propertyValues[$property]['Value'] - $timezoneoffset * 60);
                        }
                }
                
		/* Only return the value if it exists & is readable */
		if (array_key_exists($property, $this->formattedValues) && $this->properties[$property]['Read'] === true) {
			return $this->formattedValues[$property];
		}
		/* Only return the value if it exists & is readable */
		if (array_key_exists($property, $this->properties) && $this->properties[$property]['Read'] === true) {
			return $this->propertyValues[$property]['Value'];
		}
		/* Also check for an AliasedValue */
		if (array_key_exists($property, $this->localProperties) && $this->localProperties[$property]['Read'] === true) {
			return $this->propertyValues[$property]['Value'];
		}
                
		/* Property doesn't exist - standard error */
		$trace = debug_backtrace();
		trigger_error('Undefined property via __get(): ' . $property 
				. ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
				E_USER_NOTICE);
		return NULL;
        }
        
        
        /**
	 * Parse the results of a RetrieveRequest into a useable PHP object
	 * @param AlexaSDK $conn
	 * @param String $entityLogicalName
	 * @param String $soapResponse
	 * @ignore
	 */
	private function parseRetrieveResponse(AlexaSDK $conn, $entityLogicalName, $soapResponse) {
		/* Load the XML into a DOMDocument */
		$soapResponseDOM = new DOMDocument();
		$soapResponseDOM->loadXML($soapResponse);
		/* Find the RetrieveResponse */
		$retrieveResponseNode = NULL;
		foreach ($soapResponseDOM->getElementsByTagName('RetrieveResponse') as $node) {
			$retrieveResponseNode = $node;
			break;
		}
		unset($node);
		if ($retrieveResponseNode == NULL) {
			throw new Exception('Could not find RetrieveResponse node in XML provided');
			return FALSE;
		}
		/* Find the RetrieveResult node */
		$retrieveResultNode = NULL;
		foreach ($retrieveResponseNode->getElementsByTagName('RetrieveResult') as $node) {
			$retrieveResultNode = $node;
			break;
		}
		unset($node);
		if ($retrieveResultNode == NULL) {
			throw new Exception('Could not find RetrieveResult node in XML provided');
			return FALSE;
		}
                		
		/* Generate a new Entity from the DOMNode */
		$this->setValuesFromDom($conn, $entityLogicalName, $retrieveResultNode);
	}
        
        /**
	 * Generate an Entity from the DOM object that describes its properties
	 * 
	 * @param AlexaSDK $conn
	 * @param String $entityLogicalName
	 * @param DOMElement $domNode
	 */
        private function setValuesFromDom(AlexaSDK $conn, $entityLogicalName, DOMElement $domNode) {
		/* Store values from the main RetrieveResult node */
		$relatedEntitiesNode = NULL;
		$attributesNode = NULL;
		$formattedValuesNode = NULL;
		$retrievedEntityName = NULL;
		$entityState = NULL;
                
 		/* Loop through the nodes directly beneath the RetrieveResult node */
 		foreach ($domNode->childNodes as $childNode) {
                    
			switch ($childNode->localName) {
				case 'RelatedEntities':
					$relatedEntitiesNode = $childNode;
					break;
				case 'Attributes':
					$attributesNode = $childNode;
					break;
				case 'FormattedValues':
					$formattedValuesNode = $childNode;
					break;
				case 'Id':
					/* Set the Entity ID */
					//$entity->ID = $childNode->textContent;
					break;
		 		case 'LogicalName':
		 			$retrievedEntityName = $childNode->textContent;
		 			break;
		 		case 'EntityState':
		 			$entityState = $childNode->textContent;
		 			break;
			}
 		}
 		
 		/* Verify that the Retrieved Entity Name matches the expected one */
 		if ($retrievedEntityName != $entityLogicalName) {
 			trigger_error('Expected to get a '.$entityLogicalName.' but actually received a '.$retrievedEntityName.' from the server!',
 					E_USER_WARNING);
 		}
		
 		/* Log the Entity State - Never seen this used! */
 		if (self::$debugMode) echo 'Entity <'.$entity->ID.'> has EntityState: '.$entityState.PHP_EOL;
 		
 		/* Parse the Attributes & FormattedValues to set the properties of the Entity */
 		$this->setAttributesFromDOM($conn, $attributesNode, $formattedValuesNode);
 		
		/* Before returning the Entity, reset it so all fields are marked unchanged */
		$this->reset();
	}
        
}

endif;