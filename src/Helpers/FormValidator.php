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

/**
 * Validates fields by regexes and can sanatize them. Uses PHP filter_var built-in functions and extra regexes
 *
 * @package AlexaSDK
 * @subpackage Helpers
 */

namespace AlexaCRM\CRMToolkit\Helpers;

/**
 * Validates fields by regexes and can sanatize them. Uses PHP filter_var built-in functions and extra regexes
 *
 * @ignore
 */
class FormValidator {

	/**
	 * Regular expressions for validation
	 *
	 * @ignore
	 * @var array regular expressions
	 */
	public static $regexes = Array(
		'date'        => "[0-9]{1,2}\/[0-9]{1,2}\/[0-9][0-9]",
		'amount'      => "^[-]?[0-9]+\$",
		'number'      => "^[-]?[0-9,]+\$",
		'alfanum'     => "^[0-9a-zA-Z ,.-_\\s\?\!]+\$",
		'not_empty'   => "[a-z0-9A-Z]+",
		'words'       => "^[A-Za-z]+[A-Za-z \\s]*\$",
		'phone'       => "^[0-9]{10,11}\$",
		'zipcode'     => "^[1-9][0-9]{3}[a-zA-Z]{2}\$",
		'plate'       => "^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}\$",
		'price'       => "^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?\$",
		'float'       => "/^-?(?:\d+|\d*\.\d+)$/",
		'timezone'    => "/^(Z|[+-](?:2[0-3]|[01]?[0-9])(?::?(?:[0-5]?[0-9]))?)$/",
		'2digitopt'   => "^\d+(\,\d{2})?\$",
		'2digitforce' => "^\d+\,\d\d\$",
		'anything'    => "^[\d\D]{1,}\$"
	);
	/**
	 * @ignore
	 */
	private $validations, $sanatations, $mandatories, $errors, $corrects, $fields;

	/**
	 * @ignore
	 */
	public function __construct( $validations = array(), $mandatories = array(), $sanatations = array() ) {
		$this->validations = $validations;
		$this->sanatations = $sanatations;
		$this->mandatories = $mandatories;
		$this->errors      = array();
		$this->corrects    = array();
	}

	/**
	 * Validates an array of items (if needed) and returns true or false
	 *
	 * @param array $items
	 *
	 * @ignore
	 * @return Boolean True if valid, False if not
	 */
	public function validate( $items ) {

		$this->fields = $items;
		$haveFailures = false;

		foreach ( $items as $key => $val ) {

			if ( ( strlen( $val ) == 0 || array_search( $key, $this->validations ) === false ) && array_search( $key, $this->mandatories ) === false ) {
				$this->corrects[] = $key;
				continue;
			}

			$result = self::validateItem( $val, $this->validations[ $key ] );

			if ( $result === false ) {
				$haveFailures = true;
				$this->addError( $key, $this->validations[ $key ] );
			} else {
				$this->corrects[] = $key;
			}
		}

		return ( !$haveFailures );
	}

	/**
	 * Adds unvalidated class to those elements that are not validated. Removes them from classes that are.
	 *
	 * @ignore
	 * @return string
	 */
	public function getScript() {
		$output = '';

		if ( !empty( $this->errors ) ) {
			$errors = array();
			foreach ( $this->errors as $key => $val ) {
				$errors[] = "'INPUT[name={$key}]'";
			}

			$output = '$$(' . implode( ',', $errors ) . ').addClass("unvalidated");';
			$output .= "alert('there are errors in the form');"; // or your nice validation here
		}

		if ( !empty( $this->corrects ) ) {
			$corrects = array();
			foreach ( $this->corrects as $key ) {
				$corrects[] = "'INPUT[name={$key}]'";
			}
			$output .= '$$(' . implode( ',', $corrects ) . ').removeClass("unvalidated");';
		}
		$output = "<script type='text/javascript'>{$output} </script>";

		return ( $output );
	}

	/**
	 * Sanitizes an array of items according to the $this->sanitations
	 * sanitations will be standard of type string, but can also be specified.
	 * For ease of use, this syntax is accepted:
	 * $sanatations = array('fieldname', 'otherfieldname'=>'float');
	 *
	 * @return array
	 * @ignore
	 */
	public function sanatize( $items ) {

		foreach ( $items as $key => $val ) {

			if ( array_search( $key, $this->sanatations ) === false && !array_key_exists( $key, $this->sanatations ) ) {
				continue;
			}
			$items[ $key ] = self::sanatizeItem( $val, $this->validations[ $key ] );
		}

		return ( $items );
	}

	/**
	 * Adds an error to the errors array.
	 *
	 * @ignore
	 */
	private function addError( $field, $type = 'string' ) {

		$this->errors[ $field ] = $type;
	}

	/**
	 * Sanatize a single var according to $type.
	 * Allows for static calling to allow simple sanatization
	 *
	 * @ignore
	 */
	public static function sanatizeItem( $var, $type ) {

		$flags = null;
		switch ( $type ) {

			case 'url':
				$filter = FILTER_SANITIZE_URL;
				break;
			case 'int':
				$filter = FILTER_SANITIZE_NUMBER_INT;
				break;
			case 'float':
				$filter = FILTER_SANITIZE_NUMBER_FLOAT;
				$flags  = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
				break;
			case 'email':
				$var    = substr( $var, 0, 254 );
				$filter = FILTER_SANITIZE_EMAIL;
				break;
			case 'string':
			default:
				$filter = FILTER_SANITIZE_STRING;
				$flags  = FILTER_FLAG_NO_ENCODE_QUOTES;
				break;
		}

		$output = filter_var( $var, $filter, $flags );

		return ( $output );
	}

	/**
	 * Validates a single var according to $type.
	 * Allows for static calling to allow simple validation.
	 *
	 * @ignore
	 */
	public static function validateItem( $var, $type ) {

		if ( array_key_exists( $type, self::$regexes ) ) {
			$returnValue = filter_var( $var, FILTER_VALIDATE_REGEXP, array( "options" => array( "regexp" => '!' . self::$regexes[ $type ] . '!i' ) ) ) !== false;

			return ( $returnValue );
		}

		$filter = false;

		switch ( $type ) {
			case 'email':
				$var    = substr( $var, 0, 254 );
				$filter = FILTER_VALIDATE_EMAIL;
				break;
			case 'int':
				$filter = FILTER_VALIDATE_INT;
				break;
			case 'boolean':
				$filter = FILTER_VALIDATE_BOOLEAN;
				break;
			case 'ip':
				$filter = FILTER_VALIDATE_IP;
				break;
			case 'url':
				$filter = FILTER_VALIDATE_URL;
				break;
		}

		return ( $filter === false ) ? false : filter_var( $var, $filter ) !== false ? true : false;
	}

}
