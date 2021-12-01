<?php
/**
 * Copyright (c) 2021 AlexaCRM.
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
 * Represents key/value (or label/value) collection object for multi-select option sets.
 */
class OptionSetValueCollection {

    /**
     * @var array
     */
    public $value = null;

    /**
     * @var string
     */
    public $label = null;

    /**
     * @param array|null $value
     */
    public function __construct( $value, $label ) {
        $this->value = $value;
        $this->label = $label;
    }

    /**
     * Returns label of the option set value
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->label;
    }

}
