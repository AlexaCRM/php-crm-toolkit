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

use AlexaCRM\CRMToolkit\Client;

class MetadataCollection {

	/**
	 * @var Client
	 */
	protected static $_instance = null;

	/**
	 * Cached Entity Definitions
	 *
	 * @var Metadata[] associative array of cached entities
	 */
	private $cachedEntityDefinitions = array();

	private $cachedEntitiesList = array();

	private static $cachePrefix = "phpcrmtoolkit_";

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @param Client $client
	 *
	 * @return MetadataCollection
	 */
	public static function instance( $client = null ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $client );
		}

		return self::$_instance;
	}

	/**
	 * AlexaCRM\CRMToolkit\Entity\EntityMetadataCollection constructor.
	 *
	 * @param Client $client
	 */
	private function __construct( Client $client ) {
		$this->client = $client;
	}

	/**
	 * @return Client
	 */
	public function getClient() {
		return $this->client;
	}

	/**
	 * Get specified Entity definition (metadata)
	 *
	 * @param string $entityLogicalName
	 *
	 * @return Metadata
	 */
	public function __get( $entityLogicalName ) {
		return $this->getEntityDefinition( $entityLogicalName );
	}

	/**
	 * Get specified Entity definition (metadata)
	 *
	 * @param string $entityLogicalName
	 *
	 * @return Metadata
	 */
	public function getEntityDefinition( $entityLogicalName ) {
		$entityLogicalName = strtolower( $entityLogicalName );

		return $this->retrieveMetadata( $entityLogicalName );
	}

	/**
	 * Retrieves a list of entities logicalName -> displayName
	 *
	 * @return array
	 */
	public function getEntitiesList() {
		return $this->retrieveEntitiesList();
	}

	private function retrieveEntitiesList() {
		$cacheKey = self::$cachePrefix . 'metadata_entities_list';
		$isCacheEnabled = $this->getClient()->isCacheEnabled();

		if ( count( $this->cachedEntitiesList ) ) {
			return $this->cachedEntitiesList;
		} elseif ( !count( $this->cachedEntitiesList ) && $isCacheEnabled
		     && $this->getCache()->exists( $cacheKey ) ) { // entities list exists
			$this->cachedEntitiesList = $this->getCache()->get( $cacheKey );
		} else { // entities list is not loaded
			// perform CRM request to retrieve all entities' metadata
			$client               = $this->client;
			$entitiesMetadataList = $client->retrieveAllEntities( 'Entity Privileges Relationships' );

			$entitiesList = array();

			foreach ( $entitiesMetadataList as $entityMetadataPlain ) {
				$entityLogicalName = (string) $entityMetadataPlain->LogicalName;
				$entityMetadata    = new Metadata( $entityLogicalName, $entityMetadataPlain );

				// cache entity definition
				$this->setCachedEntityDefinition( $entityMetadata );

				// make a list
				$entitiesList[ $entityLogicalName ] = $entityMetadata->entityDisplayName;
			}

			$this->cachedEntitiesList = $entitiesList;

			if ( $isCacheEnabled ) {
				$this->getCache()->set( $cacheKey, $entitiesList );
			}
		}

		return $this->cachedEntitiesList;
	}

	/**
	 * Check if an Entity Definition has been cached
	 *
	 * @param String $entityLogicalName Logical Name of the entity to check for in the Cache
	 *
	 * @return boolean true if this Entity has been cached
	 */
	private function isEntityDefinitionCached( $entityLogicalName ) {
		/* Check if this entityLogicalName is in the Cache */
		if ( array_key_exists( $entityLogicalName, $this->cachedEntityDefinitions )
		     && $this->cachedEntityDefinitions[ $entityLogicalName ] instanceof Metadata
		) {
			return true;
		} elseif ( $this->getClient()->isCacheEnabled() ) {
			$cacheKey       = $this->getCacheKey( $entityLogicalName );
			$entityMetadata = $this->getCache()->get( $cacheKey );
			if ( $entityMetadata instanceof Metadata ) {
				// Store metadata in the collection cache
				// to avoid multiple cache queries
				$this->cachedEntityDefinitions[ $entityLogicalName ] = $entityMetadata;

				return true;
			}
		}

		return false;
	}

	/**
	 * Cache the definition of an Entity
	 *
	 * @param Metadata $entityMetadata
	 */
	private function setCachedEntityDefinition( Metadata $entityMetadata ) {
		$entityLogicalName = $entityMetadata->entityLogicalName;

		if ( $this->getClient()->isCacheEnabled() ) {
			$cacheKey = $this->getCacheKey( $entityLogicalName );
			$this->getCache()->set( $cacheKey, $entityMetadata );
		}

		$this->cachedEntityDefinitions[ $entityLogicalName ] = $entityMetadata;
	}

	/**
	 * @param $entityLogicalName
	 *
	 * @return mixed|null
	 */
	private function getCachedEntityDefinition( $entityLogicalName ) {
		$entityDefinition = null;

		if ( $this->isEntityDefinitionCached( $entityLogicalName ) ) {
			$entityDefinition = $this->cachedEntityDefinitions[ $entityLogicalName ];
		}

		return $entityDefinition;
	}

	/**
	 * Retrieve Entity Metadata via CRM
	 *
	 * @param string $entityLogicalName
	 * @param bool $force Bypass cache if true
	 *
	 * @return Metadata
	 */
	private function retrieveMetadata( $entityLogicalName, $force = false ) {
		$isEntityCached = $this->isEntityDefinitionCached( $entityLogicalName );
		if ( !$force && $isEntityCached ) {
			$metadata = $this->getCachedEntityDefinition( $entityLogicalName );
		} else {
			$client       = $this->client;
			$entityObject = $client->retrieveEntity( $entityLogicalName, null, 'Entity Privileges Relationships' );
			$metadata     = new Metadata( $entityLogicalName, $entityObject );
			$this->setCachedEntityDefinition( $metadata );
		}

		return $metadata;
	}

	/**
	 * CRM Toolkit Cache accessor
	 *
	 * @return \AlexaCRM\CRMToolkit\CacheInterface
	 */
	public function getCache() {
		return $this->getClient()->cache;
	}

	/**
	 * Get the cache key for a corresponding Entity logical name
	 *
	 * @param string $entityLogicalName
	 *
	 * @return string
	 */
	private function getCacheKey( $entityLogicalName ) {
		return self::$cachePrefix . 'metadata_' . $entityLogicalName;
	}

}
