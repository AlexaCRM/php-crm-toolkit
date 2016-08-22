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

namespace AlexaCRM\CRMToolkit\Entity;

use AlexaCRM\CRMToolkit\AbstractClient;
use SimpleXMLElement;

class Attribute {

    /**
     * @var string Entity attribute logical name
     */
    public $logicalName;

    /**
     * @var string User localized attribute label
     */
    public $label;

    /**
     * @var string User localized attribute description
     */
    public $description;

    /**
     * @var string
     */
    public $format;

    /**
     * @var int Max length of string attribute value
     */
    public $maxLength;

    /**
     * @var boolean Is custom attribute
     */
    public $isCustom;

    public $imeMode;

    /**
     * @var boolean is attribute is primary id for entity
     */
    public $isPrimaryId;

    /**
     * @var boolean is attribute is primary name for entity
     */
    public $isPrimaryName;

    /**
     * @var string
     */
    public $type;

    /**
     * @var boolean
     */
    public $isLookup;

    /**
     * @var boolean
     */
    public $isValidForCreate;

    /**
     * @var boolean
     */
    public $isValidForUpdate;

    /**
     * @var boolean
     */
    public $isValidForRead;

    /**
     * @var string
     */
    public $requiredLevel;

    /**
     * @var string
     */
    public $attributeOf;

    /**
     * @var OptionSet
     */
    public $optionSet;

    /**
     * @var array
     */
    public $lookupTypes;

    /**
     * Attribute constructor.
     *
     * @param SimpleXMLElement $attribute
     */
    public function __construct( $attribute ) {
        if ( $attribute instanceof SimpleXMLElement ) {
            $this->constructFromSimpleXMLElement( $attribute );
        } else {
            $attributeArgumentType = is_object( $attribute ) ? get_class( $attribute ) : gettype( $attribute );
            throw new \InvalidArgumentException( "Attribute constructor doesn't support {$attributeArgumentType} as a source of data" );
        }
    }

    public function constructFromSimpleXMLElement( SimpleXMLElement $attribute ) {
        $this->logicalName = strtolower( (string) $attribute->LogicalName );

        $this->label = (string) $attribute->DisplayName->UserLocalizedLabel->Label;

        $this->description = (string) $attribute->Description->UserLocalizedLabel->Label;

        $this->format = (string) $attribute->Format;

        $this->maxLength = (int) $attribute->MaxLength;

        $this->imeMode = (string) $attribute->ImeMode;

        $this->isCustom = ( (string) $attribute->IsCustomAttribute === 'true' );

        $this->isPrimaryId = ( (string) $attribute->IsPrimaryId === 'true' );

        $this->isPrimaryName = ( (string) $attribute->IsPrimaryName === 'true' );

        $this->type = (string) $attribute->AttributeType;

        /* Determine the Type of the Attribute */
        $attributeList = $attribute->attributes();
        $attributeType = AbstractClient::stripNS( (string) $attributeList['type'] );
        /* Handle the special case of Lookup types */
        $this->isLookup = ( $attributeType == 'LookupAttributeMetadata' );
        /* If it's a Lookup, check what Targets are allowed */
        if ( $this->isLookup ) {
            $this->lookupTypes = array();
            /* Add lookup types to Properties, search for target entities */
            foreach ( $attribute->Targets->string as $target ) {
                array_push( $this->lookupTypes, (string) $target );
            }
        } else {
            $this->lookupTypes = null;
        }

        $this->isValidForCreate = ( (string) $attribute->IsValidForCreate === 'true' );

        $this->isValidForUpdate = ( (string) $attribute->IsValidForUpdate === 'true' );

        $this->isValidForRead = ( (string) $attribute->IsValidForRead === 'true' );

        /* Check if this field is mandatory */
        $this->requiredLevel = (string) $attribute->RequiredLevel->Value;

        $this->attributeOf = (string) $attribute->AttributeOf;

        $this->optionSet = (string) $attribute->OptionSet->Name;

        /* If this is an OptionSet, determine the OptionSet details */
        if ( !empty( $attribute->OptionSet ) && !empty( $attribute->OptionSet->Name ) ) {

            $this->optionSet = new OptionSet( $attribute->OptionSet );
        } else {
            /* Not an OptionSet */
            $this->optionSet = null;
        }
    }
}
