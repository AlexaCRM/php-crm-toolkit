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

use AlexaCRM\CRMToolkit\CacheInterface;
use DOMElement;
use SimpleXMLElement;
use stdClass;

/**
 * Class AlexaCRM\CRMToolkit\Entity\EntityMetadata
 */
class Metadata {

    public $metadataID;

    /**
     * @var string Logical name of the entity
     */
    public $entityLogicalName;

    public $schemaName;

    public $entityDisplayName;

    public $entityDisplayCollectionName;

    public $entityDescription;

    /**
     * Entity attributes collection
     *
     * @var Attribute[]
     */
    public $attributes = [];

    /**
     * Entity keys
     *
     * @var EntityKey[]
     */
    public $keys = [];

    public $optionSets;

    public $objectTypeCode;

    public $primaryIdAttribute;

    public $primaryNameAttribute;

    public $ownershipType;

    public $privileges;

    /**
     * @var Relationship[]
     */
    public $oneToManyRelationships = [];

    /**
     * @var Relationship[]
     */
    public $manyToOneRelationships = [];

    /**
     * @var Relationship[]
     */
    public $manyToManyRelationships = [];

    public $autoCreateAccessTeams;

    public $autoRouteToOwnerQueue;

    public $recurrenceBaseEntityLogicalName;

    public $reportViewName;

    public $introducedVersion;

    public $primaryImageAttribute;

    /**
     * AlexaCRM\CRMToolkit\Entity\EntityMetadata constructor.
     *
     * @param string $entityLogicalName
     * @param stdClass $entityData
     */
    public function __construct( $entityLogicalName, $entityData = null ) {

        if ( $entityData ) {
            if ( $entityData instanceof SimpleXMLElement ) {
                $this->constructFromSimpleXMLElement( $entityLogicalName, $entityData );
            }
            if ( $entityData instanceof DOMElement ) {
                //TODO: Add a construct from DOMElement
            }
        }
    }

    private function constructFromSimpleXMLElement( $entityLogicalName, $entityData ) {
        $this->entityLogicalName = $entityLogicalName;
        /* Set the metadata ID */
        $this->metadataID = (string) $entityData->MetadataId;
        /* Has changed parameter is not supported */
        //$this->hasChanged = (string)$entityData->HasChanged;

        $this->entityDescription = (String) $entityData->Description->LocalizedLabels->LocalizedLabel->Label;

        $this->entityDisplayName = (String) $entityData->DisplayName->LocalizedLabels->LocalizedLabel->Label;

        $this->entityDisplayCollectionName = (String) $entityData->DisplayCollectionName->LocalizedLabels->LocalizedLabel->Label;

        $this->autoCreateAccessTeams = (string) $entityData->AutoCreateAccessTeams;

        $this->autoRouteToOwnerQueue = (string) $entityData->AutoRouteToOwnerQueue;

        $this->objectTypeCode = (string) $entityData->ObjectTypeCode;

        $this->ownershipType                   = (string) $entityData->OwnershipType;
        $this->primaryIdAttribute              = (string) $entityData->PrimaryIdAttribute;
        $this->privileges                      = (string) $entityData->Privileges;
        $this->primaryNameAttribute            = (string) $entityData->PrimaryNameAttribute;
        $this->recurrenceBaseEntityLogicalName = (string) $entityData->RecurrenceBaseEntityLogicalName;
        $this->reportViewName                  = (string) $entityData->ReportViewName;
        $this->schemaName                      = (string) $entityData->SchemaName;
        $this->introducedVersion               = (string) $entityData->IntroducedVersion;
        $this->primaryImageAttribute           = (string) $entityData->PrimaryImageAttribute;

        /* Next, we analyse this data and determine what Properties this Entity has */
        if ( count( $entityData->Attributes[0]->AttributeMetadata ) ) {
            foreach ( $entityData->Attributes[0]->AttributeMetadata as $attribute ) {
                /* Skip the Cortana attribute */
                if ( strtolower( (string) $attribute->LogicalName ) == "cortanaproactiveexperienceenabled" ) {
                    continue;
                }
                $attributeLogicalName = strtolower( (string) $attribute->LogicalName );

                $this->attributes[ $attributeLogicalName ] = new Attribute( $attribute );
            }
        }

        if ( isset( $entityData->Keys ) && count( $entityData->Keys[0]->EntityKeyMetadata ) ) {
            if ( is_null( $this->keys ) ) {
                $this->keys = [ ];
            }

            foreach ( $entityData->Keys[0]->EntityKeyMetadata as $entityKey ) {
                if ( (string) $entityKey->EntityKeyIndexStatus !== 'Active' ) {
                    continue;
                }

                $keyLogicalName = (string) $entityKey->LogicalName;
                $keyDisplayName = (string) $entityKey->DisplayName->LocalizedLabels->LocalizedLabel->Label;
                $keyAttributes  = (array) $entityKey->KeyAttributes->string; // may be array|string

                $this->keys[ $keyLogicalName ] = new EntityKey( $keyLogicalName, $keyDisplayName, $keyAttributes );
            }
        }

        if ( isset( $entityData->OneToManyRelationships ) ) {
            foreach ( $entityData->OneToManyRelationships->OneToManyRelationshipMetadata as $oneToMany ) {
                $this->oneToManyRelationships[ (string) $oneToMany->SchemaName ] = new Relationship( $oneToMany );
            }
        }

        if ( isset( $entityData->ManyToOneRelationships ) ) {
            foreach ( $entityData->ManyToOneRelationships->ManyToOneRelationshipsMetadata as $manyToOne ) {
                $this->manyToOneRelationships[ (string) $manyToOne->SchemaName ] = new Relationship( $manyToOne );
            }
        }

        if ( isset( $entityData->ManyToManyRelationships ) ) {
            foreach ( $entityData->ManyToManyRelationships->ManyToManyRelationshipsMetadata as $manyToMany ) {
                $this->manyToManyRelationships[ (string) $manyToMany->SchemaName ] = new Relationship( $manyToMany );
            }
        }
    }

}
