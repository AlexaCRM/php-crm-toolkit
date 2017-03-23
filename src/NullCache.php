<?php

namespace AlexaCRM\CRMToolkit;

/**
 * A dummy cache that implements CacheInterface.
 *
 * @package AlexaCRM\CRMToolkit
 */
class NullCache implements CacheInterface {

    /**
     * Retrieves a value from cache by key
     *
     * @param string $key Cache item key
     * @param mixed $default Default value if not found
     *
     * @return mixed
     */
    public function get( $key, $default = null ) {
        return $default;
    }

    /**
     * Saves a value in cache by key
     *
     * @param string $key Cache item key
     * @param mixed $value Cache item value
     * @param int $expiresAfter
     *
     * @return void
     */
    public function set( $key, $value, $expiresAfter = null ) {}

    /**
     * Checks whether given cache key exists and is valid
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists( $key ) {
        return false;
    }

    /**
     * Purges cache storage
     *
     * This may be performed for the SDK only if proper tagging is
     * implemented by the consuming software.
     *
     * @return void
     */
    public function cleanup() {}

    /**
     * Deletes the key from the storage.
     *
     * @param string $key Cache item key
     *
     * @return void
     */
    public function delete( $key ) {}
}
