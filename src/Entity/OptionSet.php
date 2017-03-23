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

/**
 * Represents an OptionSet from CRM.
 *
 * @package AlexaCRM\CRMToolkit\Entity
 */
class OptionSet {

    public $name;

    public $type;

    public $displayName;

    public $description;

    public $options;

    public $isGlobal;

    /**
     * OptionSet constructor.
     *
     * @param \SimpleXMLElement $optionSetNode
     */
    public function __construct( $optionSetNode ) {
        /* Determine the Name of the OptionSet */
        $this->name = (string) $optionSetNode->Name;

        $this->isGlobal = ( $optionSetNode->IsGlobal == 'true' );

        /* Determine the Type of the OptionSet */
        $this->type = (string) $optionSetNode->OptionSetType;

        $this->description = (string) $optionSetNode->Description->UserLocalizedLabel->Label;

        $this->displayName = (string) $optionSetNode->Description->UserLocalizedLabel->Label;

        /* Array to store the Options for this OptionSet */
        $optionSetValues = [];

        switch ( $this->type ) {
            case 'Boolean':
                $optionSetValues = array_merge( $optionSetValues, [
                    (int) $optionSetNode->FalseOption->Value => (string) $optionSetNode->FalseOption->Label->UserLocalizedLabel->Label[0],
                    (int) $optionSetNode->TrueOption->Value => (string) $optionSetNode->TrueOption->Label->UserLocalizedLabel->Label[0],
                ] );
                break;
            case 'State':
            case 'Status':
            case 'Picklist':
                /* Loop through the available Options */
                foreach ( $optionSetNode->Options->OptionMetadata as $option ) {
                    /* Parse the Option */
                    $value = (int) $option->Value;
                    $label = (string) $option->Label->UserLocalizedLabel->Label[0];
                    /* Check for duplicated Values */
                    if ( array_key_exists( $value, $optionSetValues ) ) {
                        trigger_error( 'Option ' . $label . ' of OptionSet ' . $this->name . ' has the same Value as another Option in this Set', E_USER_WARNING );
                    } else {
                        /* Store the Option */
                        $optionSetValues[ $value ] = $label;
                    }
                }
                break;
            default:
                /* If we're using Default, Warn user that the OptionSet handling is not defined */
                trigger_error( 'No OptionSet handling implemented for Type ' . $this->type, E_USER_WARNING );
        }

        $this->options = $optionSetValues;
    }

}
