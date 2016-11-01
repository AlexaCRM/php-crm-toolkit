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

namespace AlexaCRM\CRMToolkit;

/**
 * Describes methods for logging.
 *
 * Severity levels per RFC 5424.
 *
 * @package AlexaCRM\CRMToolkit
 */
interface LoggerInterface {

    /**
     * Emergency: system is unusable.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function emergency( $message, $context );

    /**
     * Alert: action must be taken immediately.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function alert( $message, $context );

    /**
     * Critical: critical conditions.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function critical( $message, $context );

    /**
     * Error: error conditions.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function error( $message, $context );

    /**
     * Warning: warning conditions.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function warning( $message, $context );

    /**
     * Notice: normal but significant condition.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function notice( $message, $context );

    /**
     * Informational: informational messages.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function info( $message, $context );

    /**
     * Debug: debug-level messages.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function debug( $message, $context );

}
