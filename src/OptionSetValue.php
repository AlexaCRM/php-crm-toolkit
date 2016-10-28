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

/**
 * AlexaCRM\CRMToolkit\AlexaSDK_OptionSetValue.php
 * This file defines AlexaCRM\CRMToolkit\AlexaSDK_OptionSetValue class
 * object for option sets that is used for working with entities
 *
 * @author alexacrm.com
 * @version 1.0
 * @package AlexaSDK
 */
namespace AlexaCRM\CRMToolkit;

/**
 * This class represents key/value (or label/value) object for Entities selectboxes and boolean
 */
class OptionSetValue {

    /**
     * Value of the option set element
     *
     * @var string $value (0,1,2,3 for selects and 0,1 for boolean type)
     */
    protected $value = null;

    /**
     * Label of the option set element
     * Text description of the option set
     *
     * @var string $value (0,1,2,3 for selects and 0,1 for boolean type)
     */
    protected $label = null;

    /**
     * Create a new OptionSetValue
     *
     * @param Int $_value the Value of the Option
     * @param String $_label the Label of the Option
     */
    public function __construct( $_value, $_label ) {
        /* Store the details */
        $this->value = $_value;
        $this->label = $_label;
    }

    /**
     * Handle the retrieval of properties
     *
     * @param String $property
     */
    public function __get( $property ) {
        /* Allow case-insensitive fields */
        switch ( strtolower( $property ) ) {
            case 'value':
                return $this->value;
                break;
            case 'label':
                return $this->label;
        }

        /* Property doesn't exist - standard error */
        $trace = debug_backtrace();
        trigger_error( 'Undefined property via __get(): ' . $property
                       . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );

        return null;
    }

    /**
     * Returns label of the option set value
     *
     * @return String Label of the option set value
     */
    public function __toString() {
        return (string) $this->label;
    }

}
