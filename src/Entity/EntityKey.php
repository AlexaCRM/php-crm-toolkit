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

class EntityKey {

    /**
     * EntityKey display name
     *
     * @var string
     */
    public $displayName = '';

    /**
     * Key attributes of the EntityKey
     * List of strings
     *
     * @var array|string
     */
    public $keyAttributes = [ ];

    /**
     * Logical name of the EntityKey
     *
     * @var string
     */
    public $logicalName = '';

    /**
     * EntityKey constructor.
     *
     * @param string $logicalName
     * @param string $displayName
     * @param array|string $keyAttributes
     */
    public function __construct( $logicalName, $displayName, $keyAttributes ) {
        $this->logicalName   = $logicalName;
        $this->displayName   = $displayName;
        $this->keyAttributes = $keyAttributes;
    }

    /**
     * @return array|string
     */
    public function getKeyAttributes() {
        if ( is_array( $this->keyAttributes ) && count( $this->keyAttributes ) === 1 ) {
            return $this->keyAttributes[0];
        } else {
            return $this->keyAttributes;
        }
    }
}
