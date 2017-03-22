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
use AlexaCRM\CRMToolkit\NullCache;
use AlexaCRM\CRMToolkit\StorageInterface;

class MetadataCollection {

    /**
     * @var Client
     */
    protected static $_instance = null;

    /**
     * @var StorageInterface
     */
    protected $storage = null;

    /**
     * Cached Entity Definitions
     *
     * @var Metadata[] associative array of cached entities
     */
    private $cachedEntityDefinitions = array();

    private $cachedEntitiesList = array();

    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     * @param StorageInterface $storage Persistent metadata storage
     *
     * @return MetadataCollection
     */
    public static function instance( Client $client = null, StorageInterface $storage = null ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $client, $storage );
        }

        return self::$_instance;
    }

    /**
     * AlexaCRM\CRMToolkit\Entity\EntityMetadataCollection constructor.
     *
     * @param Client $client
     * @param StorageInterface $storage
     */
    private function __construct( Client $client, StorageInterface $storage = null ) {
        $this->client = $client;

        if ( !( $storage instanceof StorageInterface ) ) {
            $storage = new NullCache();
        }

        $this->setStorage( $storage );
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
     * Sets the metadata storage.
     *
     * @param StorageInterface $storage
     */
    public function setStorage( StorageInterface $storage ) {
        $this->storage = $storage;
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
        $entitiesListKey = '__entities';

        if ( count( $this->cachedEntitiesList ) ) {
            return $this->cachedEntitiesList;
        }

        if ( $this->storage->exists( $entitiesListKey ) ) {
            $this->cachedEntitiesList = $this->storage->get( $entitiesListKey );

            return $this->cachedEntitiesList;
        }

        // perform CRM request to retrieve all entities' metadata
        $client               = $this->client;
        $entitiesMetadataList = $client->retrieveAllEntities( 'Entity' );

        $entitiesList = [ ];

        foreach ( $entitiesMetadataList as $entityMetadataPlain ) {
            $entityLogicalName = (string) $entityMetadataPlain->LogicalName;
            $entityMetadata    = new Metadata( $entityLogicalName, $entityMetadataPlain );

            // make a list
            $entitiesList[ $entityLogicalName ] = $entityMetadata->entityDisplayName;
        }

        $this->cachedEntitiesList = $entitiesList;

        $this->storage->set( $entitiesListKey, $entitiesList );

        return $this->cachedEntitiesList;
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
        // check memory
        if ( array_key_exists( $entityLogicalName, $this->cachedEntityDefinitions ) ) {
            return $this->cachedEntityDefinitions[ $entityLogicalName ];
        }

        // check storage
        if ( $this->storage->exists( $entityLogicalName ) ) {
            $entityMetadata                                      = $this->storage->get( $entityLogicalName );
            $this->cachedEntityDefinitions[ $entityLogicalName ] = $entityMetadata;

            return $entityMetadata;
        }

        /**
         * Fetch metadata from CRM and store it in memory and storage (if available).
         *
         * If no storage is available, data will be fetched from the CRM on every SDK execution
         * (web request, cli run).
         */
        $client                                              = $this->client;
        $entityObject                                        = $client->retrieveEntity( $entityLogicalName );
        $metadata                                            = new Metadata( $entityLogicalName, $entityObject );
        $this->cachedEntityDefinitions[ $entityLogicalName ] = $metadata;

        $this->storage->set( $entityLogicalName, $metadata );

        return $metadata;
    }

}
