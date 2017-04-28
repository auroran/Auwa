<?php
namespace Auwa;
/**
 * Database DAO Model
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */

/**
 * Database Interface
 *
 * Defines all the methods that the database should be used for abstract
 *
 * @see SQL Class documentation for use on SQL database
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
interface DbI{
	/**
	 * Implement SELECT request method
	 * @param	string	$table		table
	 * @param	array	$where		search array
	 *
	 * @return	array				Array of result
	 */
	public function select($table, $where);
	
	/**
	 * Implement UPDATE request method
	 * @param	string	$table		table
	 * @param	array	$where		search array
	 * @param	array	$set		replacement values array
	 *
	 * @return	boolean				boolean about request success
	 */
	public function update($table, $where, $set);
	
	/**
	 * Implement INSERT request method
	 * @param	string	$table		table
	 * @param	array	$data		data array
	 *
	 * @return	boolean				boolean about request success
	 */
	public function insert($table, $data, $specialParameter);
	
	/**
	 * Implement DELETE request method
	 * @param	string	$table		table
	 * @param	array	$where		search array
	 *
	 * @return	boolean				boolean about request success
	 */
	public function remove($table, $where);
	
	/**
	 * Implement DROP request method
	 * @param	string	$table		table
	 *
	 * @return	boolean				boolean about request success
	 */
	public function drop($table);
	
	/**
	 * Implement TRUNCATE request method
	 * @param	string	$table		table
	 *
	 * @return	boolean				boolean about request success
	 */
	public function createTable($table, $fields, $specialParameter);
	
	/**
	 * Implement DELETE request method
	 * @param	string	$table		table
	 * @param	array	$where		search array
	 *
	 * @return	boolean				boolean about request success
	 */
	public function truncate($table);
	
	 
	/**
	 * Parse where clause.
	 *
	 * @param array 	$input 			Parameters to inject into Command
	 *
	 * @return array 	$where 			Array of different parts of where clause
	 */
	public function whereParse($where);
	
	
	/**
	 * Implement the execution request method
	 * @param	string	$table		table
	 * @param	array	$where		search array
	 *
	 * This function SHOULD NOT be used directly
	 * This method returned different type of variable, 
	 * this type depends of data base class used.
	 * 
	 * @see DbI::executeS
	 * @see DbI::executeR
	 *
	 * @return	mixed				request result
	 */
	public function execute($cmd);
	
	/**
	 * Implement the execution request method, in write mode
	 * @param	string	$cmd		sql command
	 * @param	array	$value		value to inject into request
	 *
	 * This function can be used for SELECT request, but the result
	 * will be a boolean, no data could be get by this method
	 *
	 * @return	boolean				request result status
	 */
	public function executeS($cmd, $value);
	
	/**
	 * Implement the execution request method, in read mode
	 * @param	string	$cmd		sql command
	 * @param	array	$value		value to inject into request
	 *
	 * This function should be used for SELECT request ONLY
	 *
	 * @return	boolean				request result status
	 */
	public function executeR($cmd, $value);
}

// abtract class will give implementation and set common methods

/**
 * Abstract class 
 * 
 * All Db class should to extends this class,
 * theses classes require declare methods defined in interface DbI
 *
 * All databases can be abstracted by this way
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
abstract class Db implements DbI{

	/*
	 * Databases regitered and enabled
	 * @var array
	 */
	public static $dbList=array();
	
	/*
	 * Databas class name 
	 * @var string
	 */
	protected $cn;
	
	/**
	 * Prepared (or pseudo-prepared) Request
	 * @var string
	 */
	public $prepared = null;
	
	/**
	 * Request or Query
	 * @var Object
	 */
	public $request = null;
	
	/**
	 * Values use to fill prepared request
	 * @var array
	 */
	protected $values = array();
	
	
	/**
	 * Values use to order the result
	 * @var array
	 */
	public $order = array();
	
	/**
	 * Values use to join the table of object's contents
	 * @var boolean | array(table2, field)
	 */
	public $join = false;
	
	/**
	 * Record and initialize a database
	 *
	 * @param	string		$cn		Name of database
	 * 
	 * 	The database name should be the same than the class name
	 *
	 * @return	boolean				Result of record
	 */	 
	public static function init($cn){
		$cn = str_replace('Auwa\\','', $cn);
		// in case of override of class or other base class
		if ( file_exists(_CORE_CLASSES_DIR_.'Db/'.$cn.'.php') ) {
			include_once(_CORE_CLASSES_DIR_.'Db/'.$cn.'.php');
			return true;
		} else {
			$cl = 'Auwa\\'.$cn;
			if ( class_exists($cl) ) return true;
		}
		return false;
	}
	
	/**
	 * Get an instance of a database
	 *
	 * If no database is enabled and if no instance were build, an new instance is returned
	 *
	 * @param	array	$option		Array of option send to database constructor
	 * @param	string	$cn			Database driver name (ie class name)
	 *
	 * @return 	Object				Database object 
	 */
	public static function get($cn=_DB_DEFAULT_METHOD_){
		$cn = str_replace('Auwa\\','', $cn);
		if ( !isset(self::$dbList[$cn]) or !self::$dbList[$cn] ) 
			trigger_error("ERREUR : $cn n'est pas disponible actuellement.",E_USER_ERROR);
		if ( self::$dbList[$cn] === true ){
			$cl = 'Auwa\\'.$cn;
			self::$dbList[$cn] = new $cl();	
		} 		
		return self::$dbList[$cn];
	}	

	
	/**
	 * Fetch method to abstract related database methods
	 *
	 * @param	Object	$result			Result of request executed on a database
	 *
	 * @return	array	$result_array	Array build from result rows
	 */
	public static function fetchAll( $result ){
		if ( empty($result) || !is_object($result) ) return false;
		if ( ! method_exists($result, 'fetch') ) return false;
		$result_array= array();
		while( $row = $result->fetch() )
			if (is_array($row)) $result_array[] = $row;
		return $result_array;
	}
	
}

/*
 * SQL databases support
 */
include_once(_CORE_CLASSES_DIR_.'Db/SQL.php');

?>