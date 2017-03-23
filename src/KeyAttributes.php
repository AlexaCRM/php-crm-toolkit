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

class KeyAttributes {

    private $keys = [];

    public function add( $key, $attribute ) {
        $this->keys[ $key ] = $attribute;
    }

    public function count() {
        return count( $this->keyAttributes );
    }

    public function getKeys() {
        return $this->keys;
    }

    public function __get( $name ) {
        if ( strtolower( $name ) == "keys" ) {
            return $this->keys;
        }
    }

}

