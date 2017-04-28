<?php
namespace Auwa;
/**
 * Model for all object which use a yaml file
 *
 * @package Auwa \core\classes\
 * @copyright 2015 AuroraN
 */
 
/**
 * Define the object YamlModel
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */ 
class YamlModel{

	protected $objSchema = array();
	protected $config = null;

	protected $admin=true;
	protected $_folder = 'config/';
	protected $_file;
	protected $_exists = false;
	
	protected $primary=null;
	public $lastResult = true;
	public static $_instance;
	/*
	 * Get an instance of the object
	 *
	 * @param	mixed		$primaty		Primary value to use to find object on table
	 */
	public function __construct( $primary=null ){
		if (isset($primary)) $this->primary = $primary;
		if ($this->file){
			$this->_file = $this->_folder.$this->file;

			$array = \ConfigFile::getConfig($this->_file, $this->admin);
			$this->_exists = isset($array[$primary]);
			if ($this->_exists){
				foreach ($array[$primary] as $key => $value) {
					if (isset($this->objSchema[$key])) $this->{$key} = $value;
				}
				//if (isset($this->primary)) $this->{$this->primary} = $primary;
			}
		}
		$this->initObject();
	}
	
	/**
	 * Object initiator (additionnal contructor)
	 */
	public function initObject(){}
	
	
	/**
	 * Get the Db Sheme of this object
	 *
	 * @return array
	 */
	public function getSchema(){
		return $this->objSchema;
	}
	
	/**
	 * Get the Db Table field name of this object
	 *
	 * @return string
	 */
	public function exists(){
		return $this->_exists;
	}

	
	/**
	 * Set a field and update object
	 *
	 * @param	string	$field		Field to update
	 * @param	mixed	$value		New value
	 */
	public function set($field, $value, $save=true){
		if (isset($this->{$field})) $this->{$field} = $value;
		if ($save) $this->update();	
	}
	
	/**
	 * Update database with object fields values
	 * Data not mentionned into schema will be keep
	 *
	 * @return	boolean/array		Request execution
	 */
	public function update(){
		$c = \ConfigFile::getConfig($this->_file, $this->admin);
		if (!isset($c[$this->primary])) $c[$this->primary] = array();
		foreach ($this->objSchema as $key=>$value)
			$c[$this->primary][$key] = $this->{$key};
		$res = \ConfigFile::setConfig($this->_file, $c, $this->admin);
		$this->lastResult = ($res['error']!=false) ? $res['error'] : true;
		return ($res['error']==false);
	}
	
	/**
	 * Delete entry => not implented yet
	 *
	 * @return	boolean		Request execution status
	 */
	public function remove(){
		$c = \ConfigFile::getConfig($this->_file, $this->admin);
		if( isset($c[$this->primary])){
			unset($c[$this->primary]);
			$res = \ConfigFile::setConfig($this->_file, $c, $this->admin);
			if ($res['error']!=false){
				$this->lastResult = 'Item not deleted (file not writen)';
				return false;
			}
			$this->lastResult = isset($c[$this->primary]) ? 'Item not deleted' : true;
			return !isset($c[$this->primary]);
		}
		return false;
	}
	
	/**
	 * Get a array from object
	 *
	 * @return	array		object fields in an array
	 */
	public function toArray(){
		$r = array();
		foreach ($this->objSchema as $key=>$value)
			$r[ $key ] = $this->{$key};
			
		return $r;	
	}

	public function getBy($field, $value){
		$r = array();
		$list = \ConfigFile::getConfig($this->_file, $this->admin);

		if ($field==$this->primary)
			return isset($list[$field]) ? $list[$field] : array();

		foreach ($list as $key => $v) {
			if (isset($v[$field]) && $v[$field]==$value)
				$r[$key] = $v;
		}
		return $r;
	}
	public function getAll(){
		return \ConfigFile::getConfig($this->_file, $this->admin);
	}


}
YamlModel::$_instance = new YamlModel();
?>