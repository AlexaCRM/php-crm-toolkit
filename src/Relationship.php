<?php

namespace AlexaCRM\CRMToolkit;

/**
 * Represents a relationship between two entities.
 */
class Relationship {

    /**
     * Specifies that the entity is the referencing entity.
     */
    const ROLE_REFERENCING = 'Referencing';

    /**
     * Specifies that the entity is the referenced entity.
     */
    const ROLE_REFERENCED = 'Referenced';

    /**
     * Entity role: referenced or referencing.
     *
     * @var string
     *
     * @see Relationship::ROLE_REFERENCED, Relationship::ROLE_REFERENCING
     */
    public $PrimaryEntityRole;

    /**
     * Name of the relationship.
     *
     * @var string
     */
    public $SchemaName;

    /**
     * Initializes a new instance of the Relationship class.
     *
     * @param string $schemaName
     */
    public function __construct( $schemaName = null ) {
        $this->SchemaName = $schemaName;
    }

}
