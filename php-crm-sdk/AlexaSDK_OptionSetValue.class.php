<?php
/**
 * AlexaSDK_OptionSetValue.php
 * 
 * This file defines AlexaSDK_OptionSetValue class 
 * object for option sets that is used for working with entities
 * 
 * @author alexacrm.com.au
 * @version 1.0
 * @package AlexaSDK
 */


/**
 * This class represents ket/value (or label/value) object for Entities selectboxes and boolean
 */
class AlexaSDK_OptionSetValue extends AlexaSDK_Abstract {
	/** 
         * Value of the option set element
         * 
         * @var string $value (0,1,2,3 for selects and 0,1 for boolean type)
         */
	protected $value = NULL;
	/** 
         * Label of the option set element
         * 
         * Text description of the option set
         * 
         * @var string $value (0,1,2,3 for selects and 0,1 for boolean type)
         */
	protected $label = NULL;
	
	/**
	 * Create a new OptionSetValue
	 * 
	 * @param Int $_value the Value of the Option
	 * @param String $_label the Label of the Option
	 */
	public function __construct($_value, $_label) {
		/* Store the details */
		$this->value = $_value;
		$this->label = $_label;
	}
	
	/**
	 * Handle the retrieval of properties 
	 * 
	 * @param String $property
	 */
	public function __get($property) {
		/* Allow case-insensitive fields */
		switch (strtolower($property)) {
			case 'value':
				return $this->value;
				break;
			case 'label':
				return $this->label;
		}
		
		/* Property doesn't exist - standard error */
		$trace = debug_backtrace();
		trigger_error('Undefined property via __get(): ' . $property
				. ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
				E_USER_NOTICE);
		return NULL;
	}
	
	/**
         * Returns label of the option set value
         * 
	 * @return String Label of the option set value
	 */
	public function __toString() {
		return (string)$this->label;
	}
}