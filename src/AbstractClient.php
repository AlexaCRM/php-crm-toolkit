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
 * Created in HQSoftware by Pavel Selitskas
 * Date: 7/26/2016
 * Time: 12:13 PM
 */
namespace AlexaCRM\CRMToolkit;

use DOMNodeList;

/**
 * Base class for most SDK classes, contains common methods and subsclasses includes
 */
abstract class AbstractClient implements ClientInterface {

	/**
	 * Internal details
	 *
	 * @var bool $debugMode if TRUE will outputs debug information, default FALSE
	 */
	protected static $debugMode = false;

	/**
	 * Limits the maximum execution time
	 *
	 * @var int
	 */
	protected static $timeLimit = 240;

	/**
	 * Enables or disables logging
	 *
	 * @var bool
	 */
	public static $enableLogs = false;

	/**
	 * List of recognised SOAP Faults that can be returned by MS Dynamics CRM
	 *
	 * @var array $SOAPFaultActions List of SOAP Fault actions that returned from Dyanmics CRM
	 */
	public static $SOAPFaultActions = Array(
		'http://www.w3.org/2005/08/addressing/soap/fault',
		'http://schemas.microsoft.com/net/2005/12/windowscommunicationfoundation/dispatcher/fault',
		'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/ExecuteOrganizationServiceFaultFault',
		'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/CreateOrganizationServiceFaultFault',
		'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/RetrieveOrganizationServiceFaultFault',
		'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/UpdateOrganizationServiceFaultFault',
		'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/DeleteOrganizationServiceFaultFault',
		'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/RetrieveMultipleOrganizationServiceFaultFault',
	);

	/**
	 * Utility function to strip any Namespace from an XML attribute value
	 *
	 * @param String $attributeValue attribute value that contains namespace attribute
	 *
	 * @return String Attribute Value without the Namespace
	 * @ignore
	 */
	public static function stripNS( $attributeValue ) {
		return preg_replace( '/[a-zA-Z]+:([a-zA-Z]+)/', '$1', $attributeValue );
	}

	/**
	 * Get the current time, as required in XML format
	 *
	 * @return string
	 */
	protected static function getCurrentTime() {
		return substr( gmdate( 'c' ), 0, - 6 ) . ".00";
	}

	/**
	 * Get an appropriate expiry time for the XML requests, as required in XML format
	 *
	 * @ignore
	 */
	protected static function getExpiryTime() {
		return substr( gmdate( 'c', strtotime( '+5 minutes' ) ), 0, - 6 ) . ".00";
	}

	/**
	 * Get an uuid for the XML requests message id, as required in XML format
	 *
	 * @param string $namespace
	 *
	 * @return string
	 */
	public static function getUuid( $namespace = '' ) {
		static $guid = '';
		$uid  = uniqid( "", true );
		$data = [
			$namespace,
			array_key_exists( 'REQUEST_TIME_FLOAT', $_SERVER )? $_SERVER['REQUEST_TIME_FLOAT'] : $_SERVER['REQUEST_TIME'],
		];
		$requestDependentData = [];
		if ( php_sapi_name() !== 'cli' ) {
			$requestDependentData = [
				$_SERVER['HTTP_USER_AGENT'],
				$_SERVER['REMOTE_ADDR'],
				$_SERVER['REMOTE_PORT']
			];
		} else {
			$requestDependentData = [
				$_SERVER['PWD'],
			];
		}
		$data = array_merge( $data, $requestDependentData );

		$hash = strtoupper( hash( 'ripemd128', $uid . $guid . md5( implode( '', $data ) ) ) );
		$guid = implode( '-', array(
			substr( $hash, 0, 8 ),
			substr( $hash, 8, 4 ),
			substr( $hash, 12, 4 ),
			substr( $hash, 16, 4 ),
			substr( $hash, 20, 12 ),
		) );

		return $guid;
	}

	/**
	 * Checks whether given GUID is well-formed
	 *
	 * @param string $guid
	 *
	 * @return bool TRUE if guid is valid, FALSE otherwise
	 */
	public static function isGuid( $guid ) {
		return ( preg_match( '/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', strtoupper( $guid ) ) == 1 );
	}

	/**
	 * Enable or Disable DEBUG for the Class
	 *
	 * @param Boolean $_debugMode
	 */
	public static function setDebug( $_debugMode ) {
		self::$debugMode = $_debugMode;
	}

	public static function setLogging( $_enableLogs ) {
		self::$enableLogs = $_enableLogs;
	}

	public static function getDebugMode() {
		return self::$debugMode;
	}

	/**
	 * Set the maximum script execution time
	 *
	 * @param Integer $_timeLimit
	 */
	public static function setTimeLimit( $_timeLimit ) {
		self::$timeLimit = $_timeLimit;
	}

	/**
	 * Utility function to get the appropriate Class name for a particular Entity.
	 * Note that the class may not actually exist - this function just returns
	 * the name of the class, which can then be used in a class_exists test.
	 * The class name is normally AlexaSDK_Entity_Name_Capitalised,
	 * e.g. AlexaSDK_Incident, or AlexaSDK_Account
	 *
	 * @param  String $entityLogicalName
	 *
	 * @return String the name of the class
	 * @ignore
	 */
	public static function getClassName( $entityLogicalName ) {
		/* Since EntityLogicalNames are usually in lowercase, we capitalize each word */
		$capitalisedEntityName = self::capitalizeEntityName( $entityLogicalName );
		$className             = 'AlexaCRM\\CRMToolkit\\Entity\\' . $capitalisedEntityName . 'Entity';

		/* Return the generated class name */

		return $className;
	}

	/**
	 * Utility function to capitalize the Entity Name according to the following rules:
	 * 1. The first letter of each word in the Entity Name is capitalized
	 * 2. Words are glued together, thus giving a CamelCase
	 *
	 * @param string $entityLogicalName as it is stored in the CRM
	 *
	 * @return string the Entity Name as it would be in a PHP Class name
	 * @ignore
	 */
	private static function capitalizeEntityName( $entityLogicalName ) {
		/* User-defined Entities generally have underscore separated names
		 * e.g. mycompany_special_item
		 * We capitalize this as MycompanySpecialItem
		 */
		$words = explode( '_', $entityLogicalName );
		foreach ( $words as $key => $word ) {
			$words[ $key ] = ucwords( strtolower( $word ) );
		}
		$capitalisedEntityName = implode( '', $words );

		/* Return the capitalised name */

		return $capitalisedEntityName;
	}

	/**
	 * Utility function to parse time from XML - includes handling Windows systems with no strptime
	 *
	 * @param String $timestamp
	 * @param String $formatString
	 *
	 * @return integer PHP Timestamp
	 * @ignore
	 */
	protected static function parseTime( $timestamp, $formatString ) {
		/* Quick solution: use strptime */
		if ( function_exists( "strptime" ) == true ) {
			$time_array = strptime( $timestamp, $formatString );
		} else {
			$masks = Array(
				'%d' => '(?P<d>[0-9]{2})',
				'%m' => '(?P<m>[0-9]{2})',
				'%Y' => '(?P<Y>[0-9]{4})',
				'%H' => '(?P<H>[0-9]{2})',
				'%M' => '(?P<M>[0-9]{2})',
				'%S' => '(?P<S>[0-9]{2})',
				// usw..
			);
			$rexep = "#" . strtr( preg_quote( $formatString ), $masks ) . "#";
			if ( !preg_match( $rexep, $timestamp, $out ) ) {
				return false;
			}
			$time_array = Array(
				"tm_sec"  => (int) $out['S'],
				"tm_min"  => (int) $out['M'],
				"tm_hour" => (int) $out['H'],
				"tm_mday" => (int) $out['d'],
				"tm_mon"  => $out['m'] ? $out['m'] - 1 : 0,
				"tm_year" => $out['Y'] > 1900 ? $out['Y'] - 1900 : 0,
			);
		}
		$phpTimestamp = gmmktime( $time_array['tm_hour'], $time_array['tm_min'], $time_array['tm_sec'], $time_array['tm_mon'] + 1, $time_array['tm_mday'], 1900 + $time_array['tm_year'] );

		return $phpTimestamp;
	}

	/**
	 * Add a list of Formatted Values to an Array of Attributes, using appropriate handling
	 * avoiding over-writing existing attributes already in the array
	 * Optionally specify an Array of sub-keys, and a particular sub-key
	 * - If provided, each sub-key in the Array will be created as an Object attribute,
	 *   and the value will be set on the specified sub-key only (e.g. (New, Old) / New)
	 *
	 * @ignore
	 *
	 * @param array $targetArray
	 * @param DOMNodeList $keyValueNodes
	 * @param array $keys
	 * @param null $key1
	 */
	protected static function addFormattedValues( array &$targetArray, DOMNodeList $keyValueNodes, array $keys = null, $key1 = null ) {
		foreach ( $keyValueNodes as $keyValueNode ) {
			/* Get the Attribute name (key) */
			$attributeKey   = $keyValueNode->getElementsByTagName( 'key' )->item( 0 )->textContent;
			$attributeValue = $keyValueNode->getElementsByTagName( 'value' )->item( 0 )->textContent;
			/* If we are working normally, just store the data in the array */
			if ( $keys == null ) {
				/* Assume that if there is a duplicate, it's an un-formatted version of this */
				if ( array_key_exists( $attributeKey, $targetArray ) ) {
					$targetArray[ $attributeKey ] = (Object) Array(
						'Value'          => $targetArray[ $attributeKey ],
						'FormattedValue' => $attributeValue
					);
				} else {
					$targetArray[ $attributeKey ] = $attributeValue;
				}
			} else {
				/* Store the data in the array for this AuditRecord's properties */
				if ( array_key_exists( $attributeKey, $targetArray ) ) {
					/* We assume it's already a "good" Object, and just set this key */
					if ( isset( $targetArray[ $attributeKey ]->$key1 ) ) {
						/* It's already set, so add the Formatted version */
						$targetArray[ $attributeKey ]->$key1 = (Object) Array(
							'Value'          => $targetArray[ $attributeKey ]->$key1,
							'FormattedValue' => $attributeValue
						);
					} else {
						/* It's not already set, so just set this as a value */
						$targetArray[ $attributeKey ]->$key1 = $attributeValue;
					}
				} else {
					/* We need to create the Object */
					$obj = (Object) Array();
					foreach ( $keys as $k ) {
						$obj->$k = null;
					}
					/* And set the particular property */
					$obj->$key1 = $attributeValue;
					/* And store the Object in the target Array */
					$targetArray[ $attributeKey ] = $obj;
				}
			}
		}
	}

	/**
	 * Debug function. Outputs variable wrapped in html "pre" tags
	 *
	 * @param Mixed $variable Variable to be outputted using var_dump function
	 */
	public static function dump( $variable ) {
		echo "<pre>";
		if ( is_string( $variable ) ) {
			var_dump( htmlentities( $variable ) );
		} else {
			var_dump( $variable );
		}
		echo "</pre>";
	}

}
