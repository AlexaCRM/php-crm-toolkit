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
use AlexaCRM\CRMToolkit\KeyAttributes;
use Exception;

class EntityReference extends AbstractClient {

    /**
     * Logical name of the entity for Dynamics to refer to
     *
     * @var string
     */
    protected $entityLogicalName = null;

    /**
     * The ID of the Entity
     *
     * @var String corresponds GUID structure in Dynamics CRM
     */
    protected $entityID = null;

    /**
     * entityName, display name of Entity object record
     *
     * @var String
     */
    protected $displayName = null;

    protected $keyAttributes = array();

    public function __construct( $entityLogicalName, $IDorKeyAttributes = null ) {

        $this->entityLogicalName = $entityLogicalName;

        if ( $IDorKeyAttributes != null ) {
            /* Set EntityValues if specified Entity ID */
            if ( is_string( $IDorKeyAttributes ) && self::isGuid( $IDorKeyAttributes ) ) {
                /* Set the ID of Entity record */
                $this->setID( $IDorKeyAttributes );
            } else if ( $IDorKeyAttributes instanceof KeyAttributes ) {
                /* Set the keyAttributes array */
                $this->keyAttributes = $IDorKeyAttributes;
            }
        }
    }

    public function __get( $property ) {
        switch ( strtoupper( $property ) ) {
            case 'ID':
                return $this->getID();
                break;
            case 'LOGICALNAME':
                return $this->entityLogicalName;
                break;
            case 'DISPLAYNAME':
                return $this->displayName;
                break;
        }
    }

    public function __set( $property, $value ) {
        switch ( strtoupper( $property ) ) {
            case 'ID':
                $this->setID( $value );

                return;
            case 'DISPLAYNAME':
                $this->displayName = $value;
        }
    }

    public function __isset( $property ) {
        /* Handle special fields */
        switch ( strtoupper( $property ) ) {
            case 'ID':
                return ( $this->entityID != null );
                break;
            case 'LOGICALNAME':
                return true;
                break;
            case 'DISPLAYNAME':
                return ( $this->displayName != null );
        }
    }

    public function __toString() {
        return ( $this->displayName != null ) ? $this->displayName : "";
    }

    /**
     * Private utility function to get the ID field; enforces NULL --> EmptyGUID
     *
     * @ignore
     * @return String GUID if it's existing record, empty GUID otherwise
     */
    protected function getID() {
        if ( $this->entityID == null ) {
            return self::EmptyGUID;
        } else {
            return $this->entityID;
        }
    }

    /**
     * Private utility function to set the ID field; enforces "Set Once" logic
     *
     * @param String $value
     *
     * @throws Exception if the ID is already set
     * @return void
     */
    protected function setID( $value ) {
        /* Only allow setting the ID once */
        if ( $this->entityID != null ) {
            throw new Exception( 'Cannot change the ID of an Entity' );
        }
        $this->entityID = $value;
    }

    public function getFormattedValue( $property, $timezoneOffset = null ) {
        /* Handle special fields */
        switch ( strtoupper( $property ) ) {
            case 'ID':
                return $this->getID();
                break;
            case 'LOGICALNAME':
                return $this->entityLogicalName;
                break;
            case 'DISPLAYNAME':
                return $this->displayName;
                break;
        }

        return null;
    }

}
