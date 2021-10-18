<?php
/**
 * Copyright 2018 AlexaCRM
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
 * associated documentation files (the "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

namespace AlexaCRM\CRMToolkit\Auth;


/**
 * Represents a Bearer token issued by an OAuth2 token endpoint.
 */
class Token {

    /**
     * Token type, usually `Bearer`.
     */
    public $type = null;

    public $expiresIn = null;

    public $expiresOn = null;

    public $notBefore = null;

    /**
     * Resource URI for which the token was granted.
     */
    public $resource = null;

    /**
     * Token value.
     */
    public $token = null;

    /**
     * Constructs a new Token object from a JSON received from an OAuth2 token endpoint.
     *
     * @param string $json
     *
     * @return Token
     */
    public static function createFromJson( $json ) {
        try {
	        $tokenArray = json_decode( $json, true );
        } catch ( \InvalidArgumentException $e ) {
            return new Token();
        }

        $token = new Token();
        $token->type = $tokenArray['token_type'] ? $tokenArray['token_type'] : null;
        $token->expiresIn = isset( $tokenArray['expires_in'] ) ? (int)$tokenArray['expires_in'] : null;
        $token->expiresOn = isset( $tokenArray['expires_on'] ) ? (int)$tokenArray['expires_on'] : null;
        $token->notBefore = isset( $tokenArray['not_before'] ) ? (int)$tokenArray['not_before'] : null;
        $token->resource = $tokenArray['resource'] ? $tokenArray['resource'] : null;
        $token->token = $tokenArray['access_token'] ? $tokenArray['access_token'] : null;

        return $token;
    }

    /**
     * Tells whether the token is not expired.
     *
     * @param int|null $time Specify time to check the token against. Default is current time.
     *
     * @return bool
     */
    public function isValid( $time = null ) {
        if ( $time === null ) {
            $time = time();
        }

        return ( $time >= $this->notBefore && $time < $this->expiresOn );
    }

}

