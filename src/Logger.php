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

use Exception;

/**
 * Logs the messages
 */
class Logger {

	/**
	 * Log the message or full exception stack trace
	 *
	 * @param string $message The message to log
	 * @param Exception $exception the object of Exception class to write stack trace
	 *
	 * @throws Exception
	 */
	public static function log( $message, $exception = null ) {
		// nop
	}

}
