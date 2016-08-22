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

use SimpleXMLElement;

class Relationship {

    public $metadataId;

    /**
     *
     */
    public $schemaName;

    public $relationshipType;

    public $isCustomRelationship;

    public $referencedAttribute;

    public $referencedEntity;

    public $referencingAttribute;

    public $referencingEntity;

    public function __construct( $relationshipData ) {
        if ( $relationshipData instanceof SimpleXMLElement ) {
            $this->constructFromSimpleXMLElement( $relationshipData );
        }
    }

    public function constructFromSimpleXMLElement( $relationshipData ) {

        $this->metadataId = (string) $relationshipData->MetadataId;

        $this->schemaName = (string) $relationshipData->SchemaName;

        $this->relationshipType = (string) $relationshipData->RelationshipType;

        $this->isCustomRelationship = (string) $relationshipData->IsCustomRelationship;
        $this->referencedAttribute  = (string) $relationshipData->ReferencedAttribute;
        $this->referencedEntity     = (string) $relationshipData->ReferencedEntity;
        $this->referencingAttribute = (string) $relationshipData->ReferencingAttribute;
        $this->referencingEntity    = (string) $relationshipData->ReferencingEntity;
    }

}
