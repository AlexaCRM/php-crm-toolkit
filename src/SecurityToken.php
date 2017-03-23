<?php

namespace AlexaCRM\CRMToolkit;

/**
 * Represents a security token for a CRM web service.
 *
 * @package AlexaCRM\CRMToolkit
 */
class SecurityToken {

    /**
     * @var string
     */
    public $securityToken;

    /**
     * @var string
     */
    public $securityToken0;

    /**
     * @var string
     */
    public $securityToken1;

    /**
     * @var string
     */
    public $binarySecret;

    /**
     * @var string
     */
    public $keyIdentifier;

    /**
     * @var int
     */
    public $expiryTime;

    /**
     * Tells whether the token has expired.
     *
     * @return bool
     */
    public function hasExpired() {
        return $this->expiryTime < time();
    }

}
