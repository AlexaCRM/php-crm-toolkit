<?php
/**
 * Copyright (c) 2016 AlexaCRM.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Lesser Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace AlexaCRM\CRMToolkit;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use AlexaCRM\CRMToolkit\Entity\MetadataCollection;
use AlexaCRM\CRMToolkit\Entity\EntityReference;
use Exception;
use SoapFault;

/**
 * This class used for work with Dynamics CRM entities
 *
 * @property mixed ID
 * @property string LogicalName
 * @property Entity\Attribute[] attributes
 */
class Entity extends EntityReference {

    protected $entityimage = null;

    /**
     * The details of this instance of the Entity - the added AliasedValue properites
     *
     * @var array
     */
    protected $localProperties = [ ];

    /**
     * The details of this instance of the Entity - the property Values
     *
     * @var array contains values of properties (Entity field values)
     */
    private $propertyValues = [ ];

    /**
     * The details of this instance of the Entity - the propery Formatted Values
     *
     * @var array same as $propertyValues, but contains nicenames of values
     */
    private $formattedValues = [ ];

    /**
     * Object of \AlexaCRM\CRMToolkit\Client class
     *
     * @var Client
     */
    private $client;

    /**
     * Specifies whether entity record exists in the CRM
     *
     * @var bool
     */
    public $exists = false;

    /**
     * Create a new usable Dynamics CRM Entity object
     *
     * @param Client $_client
     * @param String $_logicalName Allows constructing arbitrary Entities by setting the EntityLogicalName directly
     * @param String $IDorKeyAttributes Allows constructing arbitrary Entities by setting the EntityLogicalName directly
     * @param array $columnSet List of Entity fields to retrieve
     *
     * @internal param AlexaCRM\CRMToolkit\AlexaSDK $_auth Connection to the Dynamics CRM server - should be active already.
     */
    public function __construct( Client $_client, $_logicalName = null, $IDorKeyAttributes = null, $columnSet = null ) {
        try {
            /* Store AlexaCRM\CRMToolkit\AlexaSDK object */
            $this->client = $_client;
            /* If a new LogicalName was passed, set it in this Entity */
            if ( $_logicalName != null && $_logicalName != $this->entityLogicalName ) {
                /* If this value was already set, don't allow changing it. */
                /* - otherwise, you could have a AlexaSDK_Incident that was actually an Account! */
                if ( $this->entityLogicalName != null ) {
                    throw new Exception( 'Cannot override the Entity Logical Name on a strongly typed Entity' );
                }
                /* Set the Logical Name */
                $this->entityLogicalName = $_logicalName;
            }
            /* Check we have a Logical Name for the Entity */
            if ( $this->entityLogicalName == null ) {
                throw new Exception( 'Cannot instantiate an abstract Entity - specify the Logical Name' );
            }
            /* Setup property values from entity metadata attributes */
            foreach ( $this->attributes as $attribute ) {
                $this->propertyValues[ $attribute->logicalName ] = Array(
                    'Value'   => null,
                    'Changed' => false,
                );
            }
            /* Check the ID or AlexaCRM\CRMToolkit\KeyAttributes to retrieve the entity values */
            if ( $IDorKeyAttributes != null ) {
                /* Set EntityValues if specified Entity ID */
                if ( is_string( $IDorKeyAttributes ) && self::isGuid( $IDorKeyAttributes ) ) {
                    /* Set the ID of Entity record */
                    $this->setID( $IDorKeyAttributes );
                    /* Get the raw XML data */
                    try {
                        $rawSoapResponse = $this->client->retrieveRaw( $this, $columnSet );
                        $this->parseRetrieveResponse( $this->client, $this->LogicalName, $rawSoapResponse );
                    } catch ( SoapFault $sf ) {
                        $errorCode = $sf->faultcode; // undocumented feature
                        if ( $errorCode == '-2147220969' ) {
                            $this->exists = false;
                        }
                        /* ToDo: Return exception with user-friendly details, maybe invalid ID */
                    }
                } else if ( $IDorKeyAttributes instanceof KeyAttributes ) {
                    if ( version_compare( $this->client->organizationVersion, "7.1.0", "<" ) ) {
                        throw new Exception( 'Entity ID must be a valid GUID for the organization version lower then 7.1.0' );
                    }
                    /* Set the keyAttributes array */
                    $this->keyAttributes = $IDorKeyAttributes;
                    /* Add the KeyAttribute values to the entity object values */
                    foreach ( $IDorKeyAttributes->getKeys() as $key => $attribute ) {
                        $this->propertyValues[ $key ] = array(
                            "Value"   => $attribute,
                            "Changed" => true
                        );
                    }
                    /* Get the raw XML data */
                    try {
                        $rawSoapResponse = $this->client->retrieveRaw( $this, $columnSet );
                        /* NOTE: ParseRetrieveResponse method of AlexaCRM\CRMToolkit\AlexaSDK_Entity class, not the AlexaCRM\CRMToolkit\AlexaSDK class */
                        $this->parseExecuteRetrieveResponse( $this->client, $this->LogicalName, $rawSoapResponse );
                    } catch ( SoapFault $sf ) {
                        $errorCode = $sf->faultcode; // undocumented feature
                        if ( $errorCode == '-2147088239' ) {
                            $this->exists = false;
                        }
                        /* ToDo: Return exception with user-friendly details, maybe KeyAttribute parameters invalid */
                    }
                }
            }
        } catch ( Exception $e ) {
            $this->client->logger->error( 'Caught exception while creating an Entity object', [ 'exception' => $e ] );
        }
    }

    /**
     * Method to access AlexaCRM\CRMToolkit\AlexaSDK_Entity field values
     * Used to access entity field values and some predefined system fields such as
     * (ID, Logicalname, Displayname, Entitytype)
     *
     * @param String $property to be fetched
     *
     * @return Mixed value of the property, if it exists & is readable
     */
    public function __get( $property ) {
        try {
            /* Handle special fields */
            switch ( strtoupper( $property ) ) {
                case 'ID':
                    return $this->getID();
                    break;
                case 'LOGICALNAME':
                    return $this->entityLogicalName;
                    break;
                case 'DISPLAYNAME':
                    $attribute = $this->metadata()->primaryNameAttribute;

                    return ( $this->propertyValues[ $attribute ]['Value'] ) ? $this->propertyValues[ $attribute ]['Value'] : null;
                    break;
                case 'ATTRIBUTES':
                case 'PROPERTIES':
                    return $this->metadata()->attributes;
                    break;
                case 'PROPERTYVALUES':
                    return $this->propertyValues;
                    break;
                case 'FORMATTEDVALUES':
                    return $this->formattedValues;
                    break;
                case "KEYATTRIBUTE":
                case "KEYATTRIBUTES":
                    return $this->keyAttributes;
                    break;
                case "METADATA":
                    return $this->metadata();
            }

            if ( property_exists( 'AlexaCRM\CRMToolkit\Entity\Metadata', $property ) ) {
                return $this->metadata()->$property;
            }
            /* Handle dynamic properties... */
            $property = strtolower( $property );
            /* Only return the value if it exists & is readable */
            if ( array_key_exists( $property, $this->attributes ) && $this->attributes[ $property ]->isValidForRead === true ) {
                return $this->propertyValues[ $property ]['Value'];
            }
            /* Also check for an AliasedValue */
            if ( array_key_exists( $property, $this->localProperties ) && $this->localProperties[ $property ]['Read'] === true ) {
                return $this->propertyValues[ $property ]['Value'];
            }
            /* Property is not readable, but does exist - different error message! */
            if ( array_key_exists( $property, $this->attributes ) || array_key_exists( $property, $this->localProperties ) ) {
                trigger_error( 'Property ' . $property . ' of the ' . $this->entityLogicalName . ' entity is not Readable', E_USER_NOTICE );

                return null;
            }
        } catch ( Exception $e ) {
            $this->client->logger->error( 'Caught exception while accessing Entity::__get()', [ 'exception' => $e, 'property' => $property ] );
        }

        /* Property doesn't exist - standard error */
        $this->client->logger->notice( 'Accessing undefined property via Entity::__get()', [
            'property' => $property,
            'trace' => debug_backtrace(),
        ] );

        return null;
    }

    public function metadata() {
        return MetadataCollection::instance( $this->client )->{$this->entityLogicalName};
    }

    /**
     * Sets entity field value
     *
     * @param string $property to be changed
     * @param mixed $value new value for the property
     *
     * @return void
     */
    public function __set( $property, $value ) {
        try {
            /* Handle special fields */
            switch ( strtoupper( $property ) ) {
                case 'ID':
                    $this->setID( $value );

                    return;
                case 'DISPLAYNAME':
                    $property = $this->metadata()->primaryNameAttribute;
            }
            /* Handle dynamic properties... */
            $property = strtolower( $property );
            /* Property doesn't exist - standard error */
            if ( !array_key_exists( $property, $this->attributes ) ) {
                $trace = debug_backtrace();
                trigger_error( 'Undefined property via __set() - ' . $this->entityLogicalName . ' does not support property: ' . $property
                               . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );

                return;
            }
            /* Check that this property can be set in Creation or Update */
            if ( $this->attributes[ $property ]->isValidForCreate == false && $this->attributes[ $property ]->isValidForUpdate == false ) {
                trigger_error( 'Property ' . $property . ' of the ' . $this->entityLogicalName
                               . ' entity cannot be set', E_USER_NOTICE );

                return;
            }

            /*
             * NOTE: For fast work set STRING value with ENTITY ID
             */

            /* If this is a Lookup field, it MUST be set to an Entity of an appropriate type */
            if ( $this->attributes[ $property ]->isLookup && $value != null ) {
                /* Check the new value is an Entity */
                if ( !$value instanceOf self ) {
                    $trace = debug_backtrace();
                    throw new Exception( 'Property ' . $property . ' of the ' . $this->entityLogicalName
                                         . ' entity must be a object of ' . get_class()
                                         . ' class in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_ERROR );
                }
                /* Check the new value is the right type of Entity */
                if ( !in_array( $value->entityLogicalName, $this->attributes[ $property ]->lookupTypes ) ) {
                    $trace = debug_backtrace();
                    throw new Exception( 'Property ' . $property . ' of the ' . $this->entityLogicalName
                                         . ' entity must be a ' . implode( ' or ', $this->attributes[ $property ]->lookupTypes )
                                         . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_ERROR );
                }
                /* Clear any AttributeOf related to this field */
                $this->clearAttributesOf( $property );
            }
            /* If this is an AlexaCRM\CRMToolkit\Entity\OptionSet field, it MUST be set to a valid OptionSetValue
             * according to the definition of the AlexaCRM\CRMToolkit\Entity\OptionSet
             */
            if ( $this->attributes[ $property ]->optionSet != null ) {
                /* Container for the final value */
                $optionSetValue = null;
                /* Handle passing a String value */
                if ( is_string( $value ) ) {
                    /* Look for an option with this label */
                    foreach ( $this->attributes[ $property ]->optionSet->options as $optionValue => $optionLabel ) {
                        /* Check for a case-insensitive match */
                        if ( strcasecmp( $value, $optionLabel ) == 0 ) {
                            /* Create the Value object */
                            $optionSetValue = new OptionSetValue( $optionValue, $optionLabel );
                            break;
                        } else {
                            if ( array_key_exists( $value, $this->attributes[ $property ]->optionSet->options ) ) {
                                /* Copy the Value object */
                                $optionSetValue = $value;
                            }
                        }
                    }
                }
                /* Handle passing an Integer value */
                if ( is_int( $value ) || is_bool( $value ) ) {
                    /* Look for an option with this value */
                    if ( array_key_exists( (int)$value, $this->attributes[ $property ]->optionSet->options ) ) {
                        /* Create the Value object */
                        $optionSetValue = new OptionSetValue( (int)$value, $this->attributes[ $property ]->optionSet->options[ (int)$value ] );
                    }
                }
                /* Handle passing an OptionSetValue */
                if ( $value instanceof OptionSetValue ) {
                    /* Check it's a valid option (by Value) */
                    if ( array_key_exists( $value->Value, $this->attributes[ $property ]->optionSet->options ) ) {
                        /* Copy the Value object */
                        $optionSetValue = $value;
                    }
                }
                /* Check we found a valid OptionSetValue */
                if ( $optionSetValue != null ) {
                    /* Set the value to be retained */
                    $value = $optionSetValue;
                    /* Clear any AttributeOf related to this field */
                    $this->clearAttributesOf( $property );
                } elseif ( $value == "" || $value == null ) {
                    $value = null;
                } else {
                    $trace = debug_backtrace();
                    trigger_error( 'Property ' . $property . ' of the ' . $this->entityLogicalName
                                   . ' entity must be a valid OptionSetValue of type ' . $this->attributes[ $property ]->optionSet->name
                                   . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING );

                    return;
                }
            }

            if ( $this->propertyValues[ $property ]['Value'] != $value ) {
                /* Update the property value with whatever value was passed */
                $this->propertyValues[ $property ]['Value'] = $value;
                /* Mark the property as changed */
                $this->propertyValues[ $property ]['Changed'] = true;
            }
        } catch ( Exception $e ) {
            $this->client->logger->error( 'Caught exception while accessing Entity::__set()', [ 'exception' => $e, 'property' => $property, 'value' => $value ] );
        }
    }

    /**
     * Check if a property exists on this entity.  Called by isset().
     * Note that this implementation does not check if the property is actually a non-null value.
     *
     * @param String $property to be checked
     *
     * @return boolean true, if it exists & is readable
     */
    public function __isset( $property ) {
        /* Handle special fields */
        switch ( strtoupper( $property ) ) {
            case 'ID':
                return ( $this->getID() != null );
                break;
            case 'LOGICALNAME':
                return true;
                break;
            case 'DISPLAYNAME':
                $property = $this->metadata()->primaryNameAttribute;
        }
        /* Handle dynamic properties... */
        $property = strtolower( $property );
        /* Value "Is Set" if it exists as a property, and is readable */
        /* Note: NULL values count as "Set" -> use "Empty" on the return of "Get" to check for NULLs */
        if ( array_key_exists( $property, $this->attributes ) && $this->attributes[ $property ]->isValidForRead === true ) {
            return true;
        }
        /* Also check if this is an AliasedValue */
        if ( array_key_exists( $property, $this->localProperties ) && $this->localProperties[ $property ]['Read'] === true ) {
            return true;
        }

        return false;
    }

    /**
     * Utility function to clear all "AttributeOf" fields relating to the base field
     *
     * @param String $baseProperty
     *
     * @retrun void
     */
    private function clearAttributesOf( $baseProperty ) {
        /* Loop through all the properties */
        foreach ( $this->attributes as $property => $propertyDetails ) {
            /* Check if this Property is an "AttributeOf" the base Property */
            if ( $propertyDetails->attributeOf == $baseProperty ) {
                /* Clear the property value */
                $this->propertyValues[ $property ]['Value'] = null;
            }
        }
    }

    /**
     * @return String description of the Entity including Type, DisplayName and ID
     */
    public function __toString() {
        /* Does this Entity have a DisplayName part? */
        return ( $this->propertyValues[ $this->metadata()->primaryNameAttribute ]['Value'] );
    }

    /**
     * Reset all changed values to unchanged
     * If property value set to Changed FALSE, it will not be updated on creating and updating
     *
     * @return void
     */
    public function reset() {
        /* Loop through all the properties */
        foreach ( $this->propertyValues as &$property ) {
            $property['Changed'] = false;
        }
    }

    /**
     * Send a Create request to the Dynamics CRM server, and return the ID of the newly created Entity
     *
     * @return bool|string EntityId on success, FALSE on failure
     */
    public function create() {
        return $this->client->create( $this );
    }

    /**
     * Send an Update request to the Dynamics CRM server, and return update response status
     *
     * @return string Formatted raw XML response of update request
     */
    public function update() {
        return $this->client->update( $this );
    }

    /**
     * Send a Delete request to the Dynamics CRM server, and return delete response status
     *
     * @return boolean TRUE on successful delete, false on failure
     */
    public function delete() {
        return $this->client->delete( $this );
    }

    /**
     * Check if a property has been changed since creation of the Entity
     *
     * @param string $property
     *
     * @return boolean
     */
    public function isChanged( $property ) {
        /* Dynamic properties are all stored in lowercase */
        $property = strtolower( $property );

        /* Property doesn't exist - standard error */
        if ( !array_key_exists( $property, $this->propertyValues ) ) {
            $trace = debug_backtrace();
            trigger_error( 'Undefined property via isChanged(): ' . $property
                           . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );

            return false;
        }

        return $this->propertyValues[ $property ]['Changed'];
    }

    /**
     * Return all changed propery values
     *
     * @return array of changed property values
     */
    public function getChangedPropertyValues() {
        $changedPropertyValues = array();

        foreach ( $this->propertyValues as $propertyKey => $propertyValue ) {
            if ( $propertyValue['Changed'] ) {
                $changedPropertyValues[ $propertyKey ] = $propertyValue;
            }
        }

        return $changedPropertyValues;
    }

    /**
     * Utility function to check all mandatory fields are filled
     *
     * @param array $details populated with any failures found
     *
     * @return boolean true if all mandatories are filled
     */
    public function checkMandatories( array &$details = null ) {
        /* Assume true, until proved false */
        $allMandatoriesFilled = true;
        $missingFields        = [ ];
        /* Loop through all the Mandatory fields */
        foreach ( $this->metadata()->mandatories as $property => $reason ) {
            /* If this is an attribute of another property, check that property instead */
            if ( $this->attributes[ $property ]->attributeOf != null ) {
                /* Check the other property */
                $propertyToCheck = $this->attributes[ $property ]->attributeOf;
            } else {
                /* Check this property */
                $propertyToCheck = $property;
            }
            if ( $this->propertyValues[ $propertyToCheck ]['Value'] == null ) {
                /* Ignore values that can't be in Create or Update */
                if ( $this->attributes[ $propertyToCheck ]->isValidForCreate || $this->attributes[ $propertyToCheck ]->isValidForUpdate ) {
                    $missingFields[ $propertyToCheck ] = $reason;
                    $allMandatoriesFilled              = false;
                }
            }
        }
        /* If not all Mandatories were filled, and we have been given a Details array, populate it */
        if ( is_array( $details ) && $allMandatoriesFilled == false ) {
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
     *
     * @return DOMNode that represents this Entity
     */
    public function getEntityDOM( $allFields = false ) {
        try {
            /* Generate the Entity XML */
            $entityDOM  = new DOMDocument();
            $entityNode = $entityDOM->appendChild( $entityDOM->createElement( 'entity' ) );
            $entityNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance' );
            $attributeNode = $entityNode->appendChild( $entityDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts', 'b:Attributes' ) );
            $attributeNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );
            /* Loop through all the attributes of this Entity */
            foreach ( $this->attributes as $property => $propertyDetails ) {
                /* Only include changed properties */
                if ( $this->propertyValues[ $property ]['Changed'] ) {
                    /* Create a Key/Value Pair of String/Any Type */
                    $propertyNode = $attributeNode->appendChild( $entityDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
                    /* Set the Property Name */
                    $propertyNode->appendChild( $entityDOM->createElement( 'c:key', $property ) );
                    /* Check the Type of the Value */
                    if ( $propertyDetails->isLookup ) {
                        /* Special handling for Lookups - use an AlexaCRM\CRMToolkit\Entity\EntityReference, not the AttributeType */
                        $valueNode = $propertyNode->appendChild( $entityDOM->createElement( 'c:value' ) );

                        if ( $this->propertyValues[ $property ]['Value'] != null ) {
                            $valueNode->setAttribute( 'i:type', 'b:EntityReference' );
                            $valueNode->appendChild( $entityDOM->createElement( 'b:Id', ( $this->propertyValues[ $property ]['Value'] ) ? $this->propertyValues[ $property ]['Value']->ID : "" ) );
                            $valueNode->appendChild( $entityDOM->createElement( 'b:LogicalName', ( $this->propertyValues[ $property ]['Value'] ) ? $this->propertyValues[ $property ]['Value']->logicalname : "" ) );
                            $valueNode->appendChild( $entityDOM->createElement( 'b:Name' ) )->setAttribute( 'i:nil', 'true' );
                        } else {
                            $valueNode->setAttribute( 'i:nil', 'true' );
                        }
                    } else if ( strtolower( $propertyDetails->type ) == "money" ) {

                        $valueNode = $propertyNode->appendChild( $entityDOM->createElement( 'c:value' ) );

                        if ( $this->propertyValues[ $property ]['Value'] ) {
                            $valueNode->setAttribute( 'i:type', 'b:Money' );
                            $valueNode->appendChild( $entityDOM->createElement( 'b:Value', $this->propertyValues[ $property ]['Value'] ) );
                        } else {
                            $valueNode->setAttribute( 'i:nil', 'true' );
                        }
                    } else if ( strtolower( $propertyDetails->type ) == "datetime" ) {

                        $valueNode = $propertyNode->appendChild( $entityDOM->createElement( 'c:value' ) );

                        if ( $this->propertyValues[ $property ]['Value'] ) {
                            $valueNode->setAttribute( 'i:type', 'd:dateTime' );
                            $valueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://www.w3.org/2001/XMLSchema' );
                            $valueNode->appendChild( new DOMText( gmdate( "Y-m-d\TH:i:s\Z", $this->propertyValues[ $property ]['Value'] ) ) );
                        } else {
                            $valueNode->setAttribute( 'i:nil', 'true' );
                        }
                    } else if ( strtolower( $propertyDetails->type ) == "picklist" ) {
                        $valueNode = $propertyNode->appendChild( $entityDOM->createElement( 'c:value' ) );

                        if ( $this->propertyValues[ $property ]['Value'] ) {
                            $valueNode->setAttribute( 'i:type', 'd:OptionSetValue' );
                            $valueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
                            $valueNode->appendChild( $entityDOM->createElement( 'b:Value', $this->propertyValues[ $property ]['Value']->value ) );
                        } else {
                            $valueNode->setAttribute( 'i:nil', 'true' );
                        }
                    } else {
                        /* Determine the Type, Value and XML Namespace for this field */
                        $xmlValue      = $this->propertyValues[ $property ]['Value'];
                        $xmlValueChild = null;
                        $xmlType       = strtolower( $propertyDetails->type );
                        $xmlTypeNS     = 'http://www.w3.org/2001/XMLSchema';
                        /* Special Handing for certain types of field */
                        switch ( strtolower( $propertyDetails->type ) ) {
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
                                $xmlType       = 'OptionSetValue';
                                $xmlTypeNS     = 'http://schemas.microsoft.com/xrm/2011/Contracts';
                                $xmlValue      = null;
                                $xmlValueChild = $entityDOM->createElement( 'b:Value', $this->propertyValues[ $property ]['Value']->value );
                                break;
                            case 'boolean':
                                /* Boolean - Just get the numerical value */
                                $xmlValue = $this->propertyValues[ $property ]['Value']->value? '1' : '0';

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
                                trigger_error( 'No Create/Update handling implemented for type ' . $propertyDetails->type . ' used by field ' . $property, E_USER_WARNING );
                        }
                        /* Now create the XML Node for the Value */
                        $valueNode = $propertyNode->appendChild( $entityDOM->createElement( 'c:value' ) );
                        /* Set the Type of the Value */
                        $valueNode->setAttribute( 'i:type', 'd:' . $xmlType );
                        $valueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', $xmlTypeNS );
                        /* If there is a child node needed, append it */
                        if ( $xmlValueChild != null ) {
                            $valueNode->appendChild( $xmlValueChild );
                        }
                        /* If there is a value, set it */
                        if ( $xmlValue != null ) {
                            $valueNode->appendChild( new DOMText( $xmlValue ) );
                        }
                    }
                }
            }
            /* Entity State */
            $entityNode->appendChild( $entityDOM->createElement( 'b:EntityState' ) )->setAttribute( 'i:nil', 'true' );
            /* Formatted Values */
            $formattedValuesNode = $entityNode->appendChild( $entityDOM->createElement( 'b:FormattedValues' ) );
            $formattedValuesNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );
            /* Entity ID */
            $entityNode->appendChild( $entityDOM->createElement( 'b:Id', $this->ID ) );
            /* Logical Name */
            $entityNode->appendChild( $entityDOM->createElement( 'b:LogicalName', $this->entityLogicalName ) );
            /* Related Entities */
            $relatedEntitiesNode = $entityNode->appendChild( $entityDOM->createElement( 'b:RelatedEntities' ) );
            $relatedEntitiesNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );

            if ( version_compare( $this->client->organizationVersion, '7.0.0', '>' ) ) {
                $rowVersion = $entityNode->appendChild( $entityDOM->createElement( 'b:RowVersion' ) );
                $rowVersion->setAttribute( 'i:nil', 'true' );
            }

            /* Return the root node for the Entity */

            return $entityNode;
        } catch ( Exception $e ) {
            $this->client->logger->error( 'Caught exception while generating Entity DOM', [ 'exception' => $e ] );
        }
    }

    /**
     * Generate an Entity based on a particular Logical Name - will try to be as Strongly Typed as possible
     *
     * @param Client $auth instance of AlexaCRM\CRMToolkit\Client class object
     * @param String $entityLogicalName
     *
     * @return Entity of the specified type, or a generic Entity if no Class exists
     */
    public static function fromLogicalName( Client $auth, $entityLogicalName ) {
        /* Determine which Class we will create */
        $entityClassName = self::getClassName( $entityLogicalName );
        /* If a specific class for this Entity doesn't exist, use the Entity class */
        if ( !class_exists( $entityClassName, true ) ) {
            $entityClassName = 'AlexaCRM\CRMToolkit\Entity';
        }

        /* Create a new instance of the Class */

        return new $entityClassName( $auth, $entityLogicalName );
    }

    /**
     * Generate an Entity from the DOM object that describes its properties
     *
     * @param Client $auth instance of AlexaCRM\CRMToolkit\Client class object
     * @param String $entityLogicalName
     * @param DOMElement $domNode
     *
     * @return Entity of the specified type, with the properties found in the DOMNode
     */
    public static function fromDOM( Client $auth, $entityLogicalName, DOMElement $domNode ) {
        try {
            /* Create a new instance of the appropriate Class */
            $entity = self::fromLogicalName( $auth, $entityLogicalName );

            /* Store values from the main RetrieveResult node */
            $relatedEntitiesNode = null;
            $attributesNode      = null;
            $formattedValuesNode = null;
            $retrievedEntityName = null;
            $entityState         = null;
            /* Loop through the nodes directly beneath the RetrieveResult node */
            foreach ( $domNode->childNodes as $childNode ) {
                switch ( $childNode->localName ) {
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
            if ( $retrievedEntityName != $entityLogicalName ) {
                trigger_error( 'Expected to get a ' . $entityLogicalName . ' but actually received a ' . $retrievedEntityName . ' from the server!', E_USER_WARNING );
            }

            /* Parse the Attributes & FormattedValues to set the properties of the Entity */
            $entity->setAttributesFromDOM( $auth, $attributesNode, $formattedValuesNode );

            /* Before returning the Entity, reset it so all fields are marked unchanged */
            $entity->reset();

            return $entity;
        } catch ( Exception $e ) {
            $auth->logger->error( 'Caught exception while creating an Entity object from DOM', [ 'exception' => $e ] );
        }
    }

    /**
     * @param Client $auth
     * @param DOMElement $attributesNode
     * @param DOMElement $formattedValuesNode
     *
     * @ignore
     */
    private function setAttributesFromDOM( Client $auth, DOMElement $attributesNode, DOMElement $formattedValuesNode ) {
        try {
            /* First, parse out the FormattedValues - these will be required when analysing Attributes */
            $formattedValues = [ ];
            /* Identify the FormattedValues */
            $keyValueNodes = $formattedValuesNode->getElementsByTagName( 'KeyValuePairOfstringstring' );
            /* Add the Formatted Values in the Key/Value Pairs of String/String to the Array */
            self::addFormattedValues( $formattedValues, $keyValueNodes );
            /* Setup formatted values to entity */
            foreach ( $formattedValues as $key => $value ) {
                $this->formattedValues[ $key ] = $value;
            }
            /* Identify the Attributes */
            $keyValueNodes = $attributesNode->getElementsByTagName( 'KeyValuePairOfstringanyType' );
            foreach ( $keyValueNodes as $keyValueNode ) {
                /* Get the Attribute name (key) */
                $attributeKey = $keyValueNode->getElementsByTagName( 'key' )->item( 0 )->textContent;
                /* Check the Value Type */
                $attributeValueType = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'type' );
                /* Strip any Namespace References from the Type */
                $attributeValueType = self::stripNS( $attributeValueType );
                /* Get the basic Text Content of the Attribute */
                $attributeValue = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->textContent;
                /* Handle the Value in an appropriate way */
                switch ( $attributeValueType ) {
                    case 'string':
                    case 'guid':
                        /* String, Guid - just take the attribute text content */
                        $storedValue = $attributeValue;
                        break;
                    case 'dateTime':
                        /* Date/Time - Parse this into a PHP Date/Time */
                        //$storedValue = date("m/d/y", self::parseTime($attributeValue, '%Y-%m-%dT%H:%M:%SZ'));
                        $dateTimeFormat = '%Y-%m-%dT%H:%M:%SZ';
                        if ( $this->attributes[ $attributeKey ]->format === 'DateOnly' ) {
                            $dateTimeFormat = '%Y-%m-%dT%H:%M:%S'; // DateOnly is time- and timezone-agnostic
                        }
                        $storedValue = self::parseTime( $attributeValue, $dateTimeFormat );
                        break;
                    case "BooleanManagedProperty":
                    case 'boolean':
                        /* Boolean - Map "True" to TRUE, all else is FALSE (case insensitive) */
                        $storedValue = ( strtolower( $attributeValue ) == 'true' ? true : false );
                        break;
                    case 'decimal':
                        /* Decimal - Cast the String to a Float */
                        $storedValue = (float) $attributeValue;
                        break;
                    case 'double':
                        /* Decimal - Cast the String to a Float */
                        $storedValue = (float) $attributeValue;
                        break;
                    case 'long':
                        /* Decimal - Cast the String to a Float */
                        $storedValue = (float) $attributeValue;
                        break;
                    case 'int':
                        /* Int - Cast the String to an Int */
                        $storedValue = (int) $attributeValue;
                        break;
                    case 'Money':
                        /* Decimal - Cast the String to a Float */
                        $storedValue = (float) $attributeValue;
                        break;
                    case 'OptionSetValue':
                        /* OptionSetValue - We need the Numerical Value for Updates, Text for Display */
                        $optionSetValue = (int) $attributeValue = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Value' )->item( 0 )->textContent;
                        $storedValue    = new OptionSetValue( $optionSetValue, $formattedValues[ $attributeKey ] );
                        /* Check if we have a matching "xxxName" property, and set that too */
                        if ( array_key_exists( $attributeKey . 'name', $this->attributes ) ) {
                            /* Don't overwrite something that's already set */
                            if ( $this->propertyValues[ $attributeKey . 'name' ]['Value'] == null ) {
                                $this->propertyValues[ $attributeKey . 'name' ]['Value'] = $formattedValues[ $attributeKey ];
                            }
                        }
                        break;
                    case 'base64Binary':
                        $storedValue = $attributeValue;
                        break;
                    case 'EntityReference':
                        /* EntityReference - We need the Id and Type to create a placeholder Entity */
                        $entityReferenceType = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'LogicalName' )->item( 0 )->textContent;
                        $entityReferenceId   = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Id' )->item( 0 )->textContent;
                        /* Also get the Name of the Entity - might be able to store this for View */
                        $entityReferenceName = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Name' )->item( 0 )->textContent;
                        /* Create the Placeholder Entity */
                        $storedValue              = new EntityReference( $entityReferenceType );
                        $storedValue->ID          = $entityReferenceId;
                        $storedValue->displayName = $entityReferenceName;
                        /* Check if we have a matching "xxxName" property, and set that too */
                        if ( array_key_exists( $attributeKey . 'name', $this->attributes ) ) {
                            /* Don't overwrite something that's already set */
                            if ( $this->propertyValues[ $attributeKey . 'name' ]['Value'] == null ) {
                                $this->propertyValues[ $attributeKey . 'name' ]['Value'] = $entityReferenceName;
                            }
                        }
                        break;
                    case 'AliasedValue':
                        /* If there is a "." in the AttributeKey, it's a proper "Entity" alias */
                        /* Otherwise, it's an Alias for an Aggregate Field */
                        if ( strpos( $attributeKey, '.' ) === false ) {
                            /* This is an Aggregate Field alias - do NOT create an Entity */
                            $aliasedFieldName = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'AttributeLogicalName' )->item( 0 )->textContent;
                            /* Create a new Attribute on this Entity for the Alias */
                            $this->localProperties[ $attributeKey ] = Array(
                                'Label'         => 'AliasedValue: ' . $attributeKey,
                                'Description'   => 'Aggregate field with alias ' . $attributeKey . ' based on field ' . $aliasedFieldName,
                                'isCustom'      => true,
                                'isPrimaryId'   => false,
                                'isPrimaryName' => false,
                                'Type'          => 'AliasedValue',
                                'isLookup'      => false,
                                'lookupTypes'   => null,
                                'Create'        => false,
                                'Update'        => false,
                                'Read'          => true,
                                'RequiredLevel' => 'None',
                                'AttributeOf'   => null,
                                'OptionSet'     => null,
                            );
                            $this->propertyValues[ $attributeKey ]  = Array(
                                'Value'   => null,
                                'Changed' => false,
                            );
                            /* Determine the Value for this field */
                            $storedValue = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Value' )->item( 0 )->textContent;
                        } else {
                            /* For an AliasedValue, we need to find the Alias first */
                            list( $aliasName, $aliasedFieldName ) = explode( '.', $attributeKey );
                            /* Get the Entity type that is being Aliased */
                            $aliasEntityName = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'EntityLogicalName' )->item( 0 )->textContent;
                            /* Get the Field of the Entity that is being Aliased */
                            $aliasedFieldName = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'AttributeLogicalName' )->item( 0 )->textContent;
                            /* Next, check if this Alias already has been used */
                            if ( array_key_exists( $aliasName, $this->propertyValues ) ) {
                                /* Get the existing Entity */
                                $storedValue = $this->propertyValues[ $aliasName ]['Value'];
                                /* Check if the existing Entity is NULL */
                                if ( $storedValue == null ) {
                                    /* Alias overlaps with normal field - check this is allowed */
                                    if ( !in_array( $aliasEntityName, $this->attributes[ $aliasName ]->lookupTypes ) ) {
                                        trigger_error( 'Alias ' . $aliasName . ' overlaps and existing field of type ' . implode( ' or ', $this->attributes[ $aliasName ]->lookupTypes )
                                                       . ' but is being set to a ' . $aliasEntityName, E_USER_WARNING );
                                    }
                                    /* Create a new Entity of the appropriate type */
                                    $storedValue = self::fromLogicalName( $auth, $aliasEntityName );
                                } else {
                                    /* Check it's the right type */
                                    if ( $storedValue->logicalName != $aliasEntityName ) {
                                        trigger_error( 'Alias ' . $aliasName . ' was created as a ' . $storedValue->logicalName . ' but is now referenced as a ' . $aliasEntityName . ' in field ' . $attributeKey, E_USER_WARNING );
                                    }
                                }
                            } else {
                                /* Create a new Entity of the appropriate type */
                                $storedValue = self::fromLogicalName( $auth, $aliasEntityName );
                                /* Create a new Attribute on this Entity for the Alias */
                                $this->localProperties[ $aliasName ] = Array(
                                    'Label'         => 'AliasedValue: ' . $aliasName,
                                    'Description'   => 'Related ' . $aliasEntityName . ' with alias ' . $aliasName,
                                    'isCustom'      => true,
                                    'isPrimaryId'   => false,
                                    'isPrimaryName' => false,
                                    'Type'          => 'AliasedValue',
                                    'isLookup'      => true,
                                    'lookupTypes'   => null,
                                    'Create'        => false,
                                    'Update'        => false,
                                    'Read'          => true,
                                    'RequiredLevel' => 'None',
                                    'AttributeOf'   => null,
                                    'OptionSet'     => null,
                                );
                                /* $this->propertyValues[$aliasName] = Array(
                                  'Value' => NULL,
                                  'Changed' => false,
                                  ); */
                            }
                            /* Re-create the DOMElement for just this Attribute */
                            $aliasDoc            = new DOMDocument();
                            $aliasAttributesNode = $aliasDoc->appendChild( $aliasDoc->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts', 'b:Attributes' ) );
                            $aliasAttributeNode  = $aliasAttributesNode->appendChild( $aliasDoc->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts', 'b:KeyValuePairOfstringanyType' ) );
                            $aliasAttributeNode->appendChild( $aliasDoc->createElementNS( 'http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'c:key', $aliasedFieldName ) );
                            $aliasAttributeValueNode = $aliasAttributeNode->appendChild( $aliasDoc->createElementNS( 'http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'c:value' ) );
                            /* Ensure we have all the child nodes of the Value */
                            foreach ( $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Value' )->item( 0 )->childNodes as $child ) {
                                $aliasAttributeValueNode->appendChild( $aliasDoc->importNode( $child, true ) );
                            }
                            /* Ensure we have the Type attribute, with Namespace */
                            $aliasAttributeValueNode->setAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'i:type', $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->getElementsByTagName( 'Value' )->item( 0 )->getAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'type' ) );
                            /* Re-create the DOMElement for this Attribute's FormattedValue */
                            $aliasFormattedValuesNode = $aliasDoc->appendChild( $aliasDoc->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts', 'b:FormattedValues' ) );
                            $aliasFormattedValuesNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );
                            /* Check if there is a formatted value to add */
                            if ( array_key_exists( $attributeKey, $formattedValues ) ) {
                                $aliasFormattedValueNode = $aliasFormattedValuesNode->appendChild( $aliasDoc->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts', 'b:KeyValuePairOfstringstring' ) );
                                $aliasFormattedValueNode->appendChild( $aliasDoc->createElementNS( 'http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'c:key', $aliasedFieldName ) );
                                $aliasFormattedValueNode->appendChild( $aliasDoc->createElementNS( 'http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'c:value', $formattedValues[ $attributeKey ] ) );
                            }
                            /* Now set the DOM values on the Entity */
                            $storedValue->setAttributesFromDOM( $auth, $aliasAttributesNode, $aliasFormattedValuesNode );
                            /* Finally, ensure that this is stored on the Entity using the Alias */
                            $attributeKey = $aliasName;
                        }
                        break;
                    default:
                        trigger_error( 'No parse handling implemented for type ' . $attributeValueType . ' used by field ' . $attributeKey, E_USER_WARNING );
                        $attributeValue = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->C14N();
                        /* Check for a Formatted Value */
                        if ( array_key_exists( $attributeKey, $formattedValues ) ) {
                            $storedValue = Array(
                                'XML'           => $attributeValue,
                                'FormattedText' => $formattedValues[ $attributeKey ]
                            );
                        } else {
                            $storedValue = $attributeValue;
                        }
                }
                /* Bypass __set, and set the Value directly in the Properties array */
                $this->propertyValues[ $attributeKey ]['Value'] = $storedValue;
                /* If we have just set the Primary ID of the Entity, update the ID field if necessary */
                /* Note that "localProperties" (AliasedValues) cannot be a Primary ID */
                if ( array_key_exists( $attributeKey, $this->attributes ) && $this->attributes[ $attributeKey ]->isPrimaryId && $this->ID == null ) {
                    /* Only if the new value is valid */
                    if ( $storedValue != null && $storedValue != self::EmptyGUID ) {
                        $this->ID = $storedValue;
                    }
                }
            }
        } catch ( Exception $e ) {
            $this->client->logger->error( 'Caught exception while setting entity attributes from DOM', [ 'exception' => $e ] );
        }
    }

    public function getPrimaryNameField() {
        return $this->metadata()->primaryNameAttribute;
    }

    public function getPrimaryIdField() {
        return $this->metadata()->primaryIdAttribute;
    }

    /**
     * Get the label for a field
     *
     * @param String $property
     *
     * @return string
     */
    public function getPropertyLabel( $property ) {
        /* Handle dynamic properties... */
        $property = strtolower( $property );
        /* Only return the value if it exists & is readable */
        if ( array_key_exists( $property, $this->attributes ) ) {
            return $this->attributes[ $property ]->label;
        }
        /* Also check for an AliasedValue */
        if ( array_key_exists( $property, $this->localProperties ) ) {
            return $this->localProperties[ $property ]['Label'];
        }

        /* Property doesn't exist, return empty string */

        return '';
    }

    /**
     * Get Formatted value for property
     * if formatted value doesn't exists, returned propertyValue
     *
     * @param string $property
     * @param Int $timezoneOffset offset in minutes to correct DateTime value
     *
     * @return string
     */
    public function getFormattedValue( $property, $timezoneOffset = null ) {
        try {
            /* Handle special fields */
            switch ( strtoupper( $property ) ) {
                case 'ID':
                    return $this->getID();
                    break;
                case 'LOGICALNAME':
                    return $this->entityLogicalName;
                    break;
                case 'DISPLAYNAME':
                    $property = $this->metadata()->primaryNameAttribute;
            }
            /* Handle dynamic properties... */
            $property = strtolower( $property );

            if ( $timezoneOffset != null && array_key_exists( $property, $this->attributes ) && $this->attributes[ $property ]->type == "DateTime" && $this->attributes[ $property ]->isValidForRead === true ) {
                if ( $this->propertyValues[ $property ]['Value'] == null ) {
                    return "";
                } else if ( $this->attributes[ $property ]->format == "DateAndTime" ) {
                    return date( "n/j/Y H:i", $this->propertyValues[ $property ]['Value'] - $timezoneOffset * 60 );
                } else if ( $this->attributes[ $property ]->format == "DateOnly" ) {
                    return date( "n/j/Y", $this->propertyValues[ $property ]['Value'] - $timezoneOffset * 60 );
                }
            }
            /* Only return the value if it exists & is readable */
            if ( array_key_exists( $property, $this->formattedValues ) && $this->attributes[ $property ]->isValidForRead === true ) {
                return $this->formattedValues[ $property ];
            }
            /* Only return the value if it exists & is readable */
            if ( array_key_exists( $property, $this->attributes ) && $this->attributes[ $property ]->isValidForRead === true ) {
                if ( $this->attributes[ $property ]->isLookup && $this->propertyValues[ $property ]['Value'] ) {
                    return $this->propertyValues[ $property ]['Value']->displayname;
                }

                return $this->propertyValues[ $property ]['Value'];
            }
            /* Also check for an AliasedValue */
            if ( array_key_exists( $property, $this->localProperties ) && $this->localProperties[ $property ]['Read'] === true ) {
                return $this->propertyValues[ $property ]['Value'];
            }
            /* Property doesn't exist - standard error */
            $trace = debug_backtrace();
            trigger_error( 'Undefined property via __get(): ' . $property
                           . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );

            return null;
        } catch ( Exception $e ) {
            $this->client->logger->error( 'Caught exception while retrieving a formatted value', [ 'exception' => $e, 'property' => $property ] );
        }
    }

    /**
     * Parse the results of a RetrieveRequest into a useable PHP object
     *
     * @param Client $auth
     * @param String $entityLogicalName
     * @param String $soapResponse
     *
     * @ignore
     */
    private function parseRetrieveResponse( Client $auth, $entityLogicalName, $soapResponse ) {
        /* Load the XML into a DOMDocument */
        $soapResponseDOM = new DOMDocument();
        $soapResponseDOM->loadXML( $soapResponse );
        /* Find the RetrieveResponse */
        $retrieveResponseNode = null;
        foreach ( $soapResponseDOM->getElementsByTagName( 'RetrieveResponse' ) as $node ) {
            $retrieveResponseNode = $node;
            break;
        }
        unset( $node );
        if ( $retrieveResponseNode == null ) {
            throw new Exception( 'Could not find RetrieveResponse node in XML provided' );
        }
        /* Find the RetrieveResult node */
        $retrieveResultNode = null;
        foreach ( $retrieveResponseNode->getElementsByTagName( 'RetrieveResult' ) as $node ) {
            $retrieveResultNode = $node;
            break;
        }
        unset( $node );
        if ( $retrieveResultNode == null ) {
            throw new Exception( 'Could not find RetrieveResult node in XML provided' );
        }
        /* Generate a new Entity from the DOMNode */
        $this->setValuesFromDom( $auth, $entityLogicalName, $retrieveResultNode );
    }

    /**
     * Parse the results of a RetrieveRequest into a useable PHP object
     *
     * @param Client $auth
     * @param String $entityLogicalName
     * @param String $soapResponse
     *
     * @ignore
     */
    private function parseExecuteRetrieveResponse( Client $auth, $entityLogicalName, $soapResponse ) {
        /* Load the XML into a DOMDocument */
        $soapResponseDOM = new DOMDocument();
        $soapResponseDOM->loadXML( $soapResponse );
        /* Find the RetrieveResponse */
        $retrieveResponseNode = null;
        foreach ( $soapResponseDOM->getElementsByTagName( 'ExecuteResponse' ) as $node ) {
            $retrieveResponseNode = $node;
            break;
        }
        unset( $node );
        if ( $retrieveResponseNode == null ) {
            throw new Exception( 'Could not find ExecuteResponse node in XML provided' );
        }
        /**
         * Find the RetrieveResult node
         *
         * @var DOMElement $retrieveResultNode
         */
        $retrieveResultNode = null;
        foreach ( $retrieveResponseNode->getElementsByTagName( 'ExecuteResult' ) as $node ) {
            $retrieveResultNode = $node;
            break;
        }
        unset( $node );
        if ( $retrieveResultNode == null ) {
            throw new Exception( 'Could not find ExecuteResult node in XML provided' );
        }
        /**
         * Find the Results node
         *
         * @var DOMElement $resultsNode
         */
        $resultsNode = null;
        foreach ( $retrieveResultNode->getElementsByTagNameNS( 'http://schemas.microsoft.com/xrm/2011/Contracts', 'Results' ) as $node ) {
            $resultsNode = $node;
            break;
        }
        unset( $node );
        if ( $resultsNode == null ) {
            throw new Exception( 'Could not find Results node in XML provided' );
        }
        /* Find the Results node */
        $valueNode = null;
        foreach ( $resultsNode->getElementsByTagNameNS( 'http://schemas.datacontract.org/2004/07/System.Collections.Generic', 'value' ) as $node ) {
            $valueNode = $node;
            break;
        }
        unset( $node );
        if ( $valueNode == null ) {
            throw new Exception( 'Could not find value node in XML provided' );
        }
        /* Generate a new Entity from the DOMNode */
        $this->setValuesFromDom( $auth, $entityLogicalName, $valueNode );
    }

    /**
     * Generate an Entity from the DOM object that describes its properties
     *
     * @param Client $auth
     * @param String $entityLogicalName
     * @param DOMElement $domNode
     */
    private function setValuesFromDom( Client $auth, $entityLogicalName, DOMElement $domNode ) {
        try {
            /* Store values from the main RetrieveResult node */
            $relatedEntitiesNode = null;
            $attributesNode      = null;
            $formattedValuesNode = null;
            $retrievedEntityName = null;
            $entityState         = null;
            /* Loop through the nodes directly beneath the RetrieveResult node */
            foreach ( $domNode->childNodes as $childNode ) {
                switch ( $childNode->localName ) {
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
                        if ( $this->getID() === self::EmptyGUID ) {
                            $this->setID( $childNode->textContent );
                        }
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
            if ( $retrievedEntityName != $entityLogicalName ) {
                trigger_error( 'Expected to get a ' . $entityLogicalName . ' but actually received a ' . $retrievedEntityName . ' from the server!', E_USER_WARNING );
            }
            /* Log the Entity State - Never seen this used! */
            //AlexaCRM\CRMToolkit\AlexaSDK_Logger::log('Entity <' . $entity->ID . '> has EntityState: ' . $entityState);
            /* Parse the Attributes & FormattedValues to set the properties of the Entity */
            $this->setAttributesFromDOM( $auth, $attributesNode, $formattedValuesNode );
            /* Before returning the Entity, reset it so all fields are marked unchanged */
            $this->reset();
            $this->exists = true;
        } catch ( Exception $e ) {
            $this->client->logger->error( 'Caught exception while settings entity values from DOM', [ 'exception' => $e ] );
        }
    }

    /**
     * Returns a cache key for the volatile entity cache.
     *
     * @return string
     */
    public function getCacheKey() {
        return static::generateCacheKey( $this->entityLogicalName, $this->entityID );
    }

    /**
     * Generates a cache key for the volatile entity cache.
     *
     * @param string $logicalName Entity logical name
     * @param string $id Record ID
     * @param array $columnSet Optional. List of entity fields to retrieve
     *
     * @return string
     */
    public static function generateCacheKey( $logicalName, $id, $columnSet = null ) {
        $columnSetString = '';

        if ( is_array( $columnSet ) && count( $columnSet ) ) {
            sort( $columnSet );
            $columnSetString = '_' . serialize( $columnSet );
        }

        if ( !is_string( $id ) ) {
            $id = serialize( $id );
        }

        return sha1( "{$logicalName}_{$id}{$columnSetString}" );
    }
}
