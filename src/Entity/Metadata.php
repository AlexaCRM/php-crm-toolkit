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
 *
 * @property Attribute[] $attributes Entity attributes collection
 * @property EntityKey[] $keys Entity keys
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
	 * @var Attribute[]
	 */
	private $attributesCollection = null;

	/**
	 * @var EntityKey[]
	 */
	private $keysCollection = null;

	public $optionSets;

	public $objectTypeCode;

	public $primaryIdAttribute;

	public $primaryNameAttribute;

	public $ownershipType;

	public $privileges;

	/**
	 * @var Relationship[]
	 */
	public $oneToManyRelationships = array();

	/**
	 * @var Relationship[]
	 */
	public $manyToOneRelationships = array();

	/**
	 * @var Relationship[]
	 */
	public $manyToManyRelationships = array();

	/*
	public $autoCreateAccessTeams;
	public $autoRouteToOwnerQueue;
	public $recurrenceBaseEntityLogicalName;
	public $reportViewName;
	public $introducedVersion;*/
	public $primaryImageAttribute;

	private static $cachePrefix = "phpcrmtoolkit_";

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

	/**
	 * @param $fieldName
	 *
	 * @return mixed
	 */
	public function __get( $fieldName ) {
		if ( $fieldName === 'attributes' ) {
			return $this->getAttributes();
		} elseif ( $fieldName === 'keys' ) {
			return $this->getKeys();
		} else {
			$trace = debug_backtrace();
			trigger_error( 'Undefined property via __get(): ' . $fieldName
			               . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );

			return null;
		}
	}

	/**
	 * Get Entity attributes
	 *
	 * @return Attribute[]
	 */
	private function getAttributes() {
		if ( !$this->isAttributesCollectionRetrieved() ) {
			$this->retrieveAttributesFilter();
		}

		return $this->attributesCollection;
	}

	private function getKeys() {
		if ( !$this->isKeysCollectionRetrieved() ) {
			$this->retrieveAttributesFilter();
		}

		return $this->keysCollection;
	}

	/**
	 * Check whether attributes collection has been retrieved from CRM
	 *
	 * @return bool
	 */
	private function isAttributesCollectionRetrieved() {
		if ( is_null( $this->attributesCollection ) ) {
			$attributesFromCache = $this->retrieveAttributesFromCache();
			if ( !is_array( $attributesFromCache ) ) {
				return false;
			}

			$indexBasedAttributesArray = array_values( $attributesFromCache );
			$sampleAttribute           = array_shift( $indexBasedAttributesArray );
			if ( $sampleAttribute instanceof Attribute ) {
				$this->attributesCollection = $attributesFromCache;

				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 * Check whether keys collection has been retrieved from CRM
	 *
	 * @return bool
	 */
	private function isKeysCollectionRetrieved() {
		if ( is_null( $this->keysCollection ) ) {
			$keysFromCache = $this->retrieveKeysFromCache();
			if ( !is_array( $keysFromCache ) ) {
				return false;
			}

			$indexBasedKeysArray = array_values( $keysFromCache );
			$sampleEntityKey = array_shift( $indexBasedKeysArray );
			if ( $sampleEntityKey instanceof EntityKey ) {
				$this->keysCollection = $keysFromCache;

				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 * Retrieve Entity metadata using 'Attributes' entity filter (includes attributes, keys)
	 */
	private function retrieveAttributesFilter() {
		$client            = MetadataCollection::instance()->getClient();
		$entityLogicalName = $this->entityLogicalName;
		$entityData        = $client->retrieveEntity( $entityLogicalName, null, 'Attributes' );

		$this->pushAttributes( $entityData );
		$this->pushKeys( $entityData );

		// cache attributes
		if ( $this->isCacheEnabled() ) {
			$attributesCacheKey = $this->getAttributesCacheKey();
			$keysCacheKey = $this->getKeysCacheKey();

			$cache    = $this->getCache();
			$cache->set( $attributesCacheKey, $this->attributesCollection );
			$cache->set( $keysCacheKey, $this->keysCollection );
		}
	}

	/**
	 * Retrieve attributes from cache
	 *
	 * @return mixed
	 */
	private function retrieveAttributesFromCache() {
		$cacheKey = $this->getAttributesCacheKey();

		$attributes = null;

		if ( $this->isCacheEnabled() ) {
			$attributes = $this->getCache()->get( $cacheKey );
		}

		return $attributes;
	}

	/**
	 * Retrieve keys from cache
	 *
	 * @return mixed
	 */
	private function retrieveKeysFromCache() {
		$cacheKey = $this->getKeysCacheKey();

		$keys = null;

		if ( $this->isCacheEnabled() ) {
			$keys = $this->getCache()->get( $cacheKey );
		}

		return $keys;
	}

	/**
	 * CRM Toolkit Cache accessor
	 *
	 * @return CacheInterface
	 */
	private function getCache() {
		return MetadataCollection::instance()->getCache();
	}

	/**
	 * @return bool
	 */
	private function isCacheEnabled() {
		return MetadataCollection::instance()->getClient()->isCacheEnabled();
	}

	/**
	 * Get attributes cache key for this entity
	 *
	 * @return string
	 */
	private function getAttributesCacheKey() {
		return self::$cachePrefix . 'metadata_' . $this->entityLogicalName . '_attributes';
	}

	/**
	 * Get Metadata.Keys cache key for this entity
	 *
	 * @return string
	 */
	private function getKeysCacheKey() {
		return self::$cachePrefix . 'metadata_' . $this->entityLogicalName . '_keys';
	}

	private function pushAttributes( $entityData ) {
		foreach ( $entityData->Attributes[0]->AttributeMetadata as $attribute ) {
			/* Skip the Cortana attribute */
			if ( strtolower( (string) $attribute->LogicalName ) == "cortanaproactiveexperienceenabled" ) {
				continue;
			}
			$attributeLogicalName = strtolower( (string) $attribute->LogicalName );

			$this->attributesCollection[ $attributeLogicalName ] = new Attribute( $attribute );
		}
	}

	private function pushKeys( $entityData ) {
		if ( is_null( $this->keysCollection ) ) {
			$this->keysCollection = [];
		}

		foreach ( $entityData->Keys[0]->EntityKeyMetadata as $entityKey ) {
			if ( (string)$entityKey->EntityKeyIndexStatus !== 'Active' ) {
				continue;
			}

			$keyLogicalName = (string)$entityKey->LogicalName;
			$keyDisplayName = (string)$entityKey->DisplayName->LocalizedLabels->LocalizedLabel->Label;
			$keyAttributes = (array)$entityKey->KeyAttributes->string; // may be array|string

			$this->keysCollection[$keyLogicalName] = new EntityKey( $keyLogicalName, $keyDisplayName, $keyAttributes );
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
			$this->pushAttributes( $entityData );
		}

		if ( count( $entityData->Keys[0]->EntityKeyMetadata ) ) {
			$this->pushKeys( $entityData );
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
