<?php
namespace Auwa;
/**
 * Tools for test and santize variables
 *
 * @package Auwa \core\classes\
 * @copyright 2016 AuroraN
 */

/**
 * Give tools about variables, static methods
 *
 * - Test and validation
 * - Sanitizer
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class Check{
	/**
	 * Check if input is an Error Object
	 *
	 * @param	mixed	$input	Input data to test
	 *
	 * @return boolean			True if $input is an Error, else False
	 */
	public static function isError($input){
		if (!is_object($input)) return false;
		return ( get_class($input)=='Auwa\Error' );
	}
	
	/**
	 * Check if input is a valid mail string
	 *
	 * @param	string		$input		Mail to test
	 * @param	boolean		$getVar		Get string from a request variable
	 *
	 * @return boolean					True if $input is a valid mail, else False 
	 */
	public static function isMail($input=false, $getVar=false){
		if ($getVar===true) $input = Tools::getValue($input);
		return filter_var($input, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Sanitize data
	 *
	 * @param	mixed		$input		Input data to sanitize
	 * @param	string		$type		Type of sanitizer to use, String by default
	 * @param	boolean		$getVar		Get data from a request variable
	 *
	 * @return  mixed					Sanitized data
	 */
	public static function sanitize($input=false, $type='String', $getVar=false){
		if ($getVar===true) $input = Tools::getValue($input);
		if (is_array($input)) {
			foreach ($input as $key => $value) {
				$input[$key] = self::sanitize($value);
			}
			return $input;
		}
		switch ($type) {
			case 'String':
				if (!is_string($input)) return false;
				return filter_var((string)$input, FILTER_SANITIZE_STRING);
				break;
			
			case 'Mail':
				if (!is_string($input)) return false;
				return filter_var((string)$input, FILTER_SANITIZE_EMAIL);
				break;

			case 'Float':
				if (!is_float($input)) return false;
				return filter_var((float)$input, FILTER_SANITIZE_NUMBER_FLOAT);
				break;

			case 'Int':
				if (!is_int($input)) return false;
				return filter_var((int)$input, FILTER_SANITIZE_NUMBER_INT);
				break;

			case 'Url':
				if (!is_string($input)) return false;
				return filter_var((string)$input, FILTER_SANITIZE_ENCODED);
				break;

			default:
				if (!is_string($input)) return false;
				// remove extrem white space from a string var by default
				return trim((string)$input );
				break;
		}
	}
}
?>