<?php
namespace Auwa;
/**
 * Model for all object which use a database
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */
 
/**
 * Link an Object to the DAO
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */ 
class DefaultModel{
	
	
	/*
	 * Primary field name
	 * @var string
	 */
	protected $dbPrimary = 'id';
	
	/*
	 * Defines if object corresponds to a row on database
	 * @var boolean
	 */
	protected $dbRecord = false;
	
	/*
	 * Database table (ie Object class)
	 * @var string
	 */
	protected $dbTable;
	
	/*
	 * DB fields definition
	 * @var array
	 */
	protected $dbSchema = array();
	
	/*
	 * Date of creation
	 * @var Time
	 */ 
	public $insert_date;

	/*
	 * Date of creation
	 * @var Time
	 */ 
	public $update_date;
	
	/*
	 * Array of contents (each key is a lang)
	 * @var array
	 */ 
	public $contents;
	
	/*
	 * Set if object is retrieved with it language contents or not
	 * @var boolean
	 */ 
	public $useContents=false;

	/*
	 * Database driver
	 * @var string
	 */
	protected $dbDriver = _DB_DEFAULT_METHOD_;
	
	
	/*
	 * Forced database driver
	 * @var string
	 */
	public static $dbForcedDriver = null;
	
	
	/*
	 * Name of class, used during static call
	 * @var string
	 */
	protected static $class = __CLASS__;
	
	/*
	 * Get an instance of the object
	 *
	 * @param	mixed		$primary		Primary value to use to find object on table / data to inject into object
	 */
	public function __construct( $primary = false ){
		$this->dbTable = strtolower( str_replace('Auwa\\','',get_class($this)) );
		$table = get_class($this);
		if ( !empty($table::$dbForcedDriver) ) $this->dbDriver = $table::$dbForcedDriver;
		if ($primary!==false ){
			$row =!is_array($primary) ? $this->getFromDb( array($this->dbPrimary=>$primary) ) : array($primary); 
			if (is_array($row) && count($row)>0){
				$this->dbRecord = true;
				foreach($this->dbSchema as $field=>$v){
					if (isset( $row[0][$field] )) $this->{$field} = $row[0][$field];
				}
				if ( $this->useContents && isset($row[0]['iso_lang']) ){		// fill with the object's contents
					$this->contents = array();
					$ctable = $table.'Content';
					$c = new $ctable();		// get model object of contents
					foreach (Lang::getEnabledLanguages() as $iso => $l) {
						$this->contents[$iso] = $c->toArray();
					}
					foreach($row as $item){
						foreach($c->dbSchema as $field=>$v){
							$this->contents[$item['iso_lang']][$field] = isset( $item[$field] ) ? $item[$field] : null;
						}
					}
				}
			} // end clause on row
		} 
			
		$this->initObject($primary);
	}
	/**
	 * Object initiator (additionnal contructor)
	 */
	public function initObject(){}
	
	/**
	 * Get the database in use
	 *
	 * @param 	mixed 	$join 	If not false, include the join request
	 * @return 	Object 	$db 	Database Object in use
	 */
	public function getDb($join=false){
		$db = Db::get($this->dbDriver);
		if ($this->useContents || $join) {
			if ($join)
				foreach ($join as $value) {
					$db->addJoin( $value );
				}
			else 
				$db->addJoin( $this->dbPrimary, $this->dbTable.'content' );
		}
		else $db->join = false;
		return $db;
	}
	
	/** 
	 * Get data from a database
	 * 
	 * @param $search
	 * @param $field
	 * @param $limit
	 * @param $join
	 *
	 * @return array 	Array of the result
	 */
	public function getFromDb($search=false, $field=false, $limit=false, $join=array()){
		$db = $this->getDb($join);
		return $db->select($this->dbTable, $search, $field, $limit);
	}

	/**
	 * Get the Db Driver of this object
	 *
	 * @return string
	 */
	public function getDriver(){
		return $this->dbDriver;
	}
	
	/**
	 * Get the Db Sheme of this object
	 *
	 * @return array
	 */
	public function getSchema(){
		return $this->dbSchema;
	}
	
	/**
	 * Get the Db Primary field name of this object
	 *
	 * @return string
	 */
	public function getPrimary(){
		return $this->dbPrimary;
	}
	
	/**
	 * Get the Db Table field name of this object
	 *
	 * @return string
	 */
	public function getTable(){
		return $this->dbTable;
	}
	
	/**
	 * Get the Db Table field name of this object
	 *
	 * @return string
	 */
	public function exists(){
		return $this->dbRecord;
	}
	
	/**
	 * Get all registered objects
	 *
	 * @return	array	$r	Array of Sub-classes
	 */
	final public static function getSubClasses(){
		$r = array();
		$classes=get_declared_classes();
		foreach($classes as $class){
			if(is_subclass_of($class, __CLASS__)){
				$r[] = $class;
			}
		}
		return $r;
	}
	
	/**
	 * Set a field and update object
	 *
	 * @param	string	$field		Field to update
	 * @param	mixed	$value		New value
	 */
	public function set($field, $value, $save=true){
		if ($field==$this->dbPrimary && $value==null) return false;
		$this->{$field} = $value;
		if ($save) return $this->update();	
	}

	/**
	 * Set fields from data array and update object
	 *
	 * @param	Array	$data		data array
	 */
	public function setValues($data, $save=true){
		foreach ($data as $field => $value) {
			$this->set($field, $value, false);
		}
		if ($save) {
			return $this->update();	
		}
	}
	
	/**
	 * Update database with object fields values
	 *
	 * @return	boolean/array		Request execution
	 */
	public function update($saveContents = false){
		$values = array();
		$primary = $this->{$this->dbPrimary};
		$insert  = !$this->dbRecord;
		foreach ($this->dbSchema as $key=>$value)
			$values[$key] = $this->{$key};
			
		if ( isset($this->dbSchema['insert_date']) ){
			if ($insert)	$values['insert_date']= date('Y-m-d H:m:s');
			if ( isset($this->dbSchema['update_date']) ) $values['update_date'] = date('Y-m-d H:m:s');
		}
		$res=  (!$insert )
					? Db::get( $this->dbDriver)->update( $this->dbTable, array($this->dbPrimary=>$primary), $values )
					: Db::get( $this->dbDriver)->insert( $this->dbTable, $values );
		
		foreach ($res as $key=>$value)
			$this->{$key} = $value;	
		if ($insert) $this->{$this->dbPrimary} = Db::get( $this->dbDriver)->lastInsertId(); // only for MysQL !

		//save contents field if requested
		if ($this->useContents && $saveContents){
			$cl = get_class($this).'Content';
			$sample = new $cl;
			foreach ($this->contents as $key => $values) {
				$c = new $cl( (int)$values[$sample->dbPrimary] );
				$values[$this->dbPrimary] = $this->{$this->dbPrimary};
				$r = $c->setValues($values);
			}
		}
		return $res;
	}
	
	/**
	 * Delete object entry on table
	 *
	 * @return	boolean		Request execution status
	 */
	public function remove(){
		if ($this->useContents) {
			$r = Db::get( $this->dbDriver)->remove( $this->dbTable.'content', array($this->dbPrimary=>$this->{$this->dbPrimary}) );
			if ($r===false) return $r;
		}
		return Db::get( $this->dbDriver)->remove( $this->dbTable, array($this->dbPrimary=>$this->{$this->dbPrimary}) );
	}
	
	/**
	 * Get a array from object
	 *
	 * @return	array		object fields in an array
	 */
	public function toArray(){
		$r = array();
		foreach ($this->dbSchema as $key=>$value)
			$r[ $key ] = $this->{$key};
		
		if ($this->useContents) $r['contents'] = $this->contents;
		return $r;	
	}
	
	/**
	 * Get an instance of a specific object (with it primary key)
	 *
	 * @param	string		$primaty		Primary value to use to find object on table
	 *
	 * @return	Object		Instance a requested object
	 */
	public static function get($primary=false){
		$cl= static::$class;
		return new $cl($primary);
	}
	
	/**
	 * Get an instance of a specific object (with one information about it)
	 *
	 * @param	string		$field		Field to use to find object on table
	 * @param	string		$value		Value to search
	 *
	 * @return	Object		Instance a requested object
	 */
	public static function getBy($field, $value){
		$search = array();
		if (is_array($field) && is_array($value)){
			if (count($field) != count($value)) return false;
			foreach ($field as $key => $f)
				$search[$f] = $value[$key];	
		} else
			$search[$field] = $value;	
		$get = self::getCollection($search, false, 1);
		return isset($get[0]) ? $get[0] : false;
	}
	
	/**
	 * Get an array of all entries which corresponds to the search values
	 *
	 * All contents of a entries for an object corresponds to a row !
	 *
	 * @param	string		$field		Field to use to find object on table
	 * @param	string		$value		Value to search
	 *
	 * @return	Array		All entries on databalse for this object, array of arrays
	 */
	public static function getAll($search=false, $fields=false, $limit=false, $join=array()){
		$obj = self::get(false);
		$r = $obj->getFromDb($search, $fields, $limit, $join);
		return $r===false ? array() : $r;
	}
	
	/**
	 * Get a collection of all entries which corresponds to the search values, result ordered
	 *
	 * @param	string		$cl		Class of the object
	 * @param	string		$value		Value to search
	 *
	 * @return	Array		All entries on databalse for this object, array of objects
	 */
	public static function getCollection($search=false, $fields=false, $limit=false, $join=array()){
		return self::dispatchData( self::getAll($search, $fields, $limit, $join) );
	}

	/**
	 * Get a array of all entries which corresponds to the search values, result ordered
	 *
	 * @param	string		$cl		Class of the object
	 * @param	string		$value		Value to search
	 *
	 * @return	Array		All entries on databalse for this object, array of array
	 */
	public static function getCollectionAsArray($search=false, $fields=false, $limit=false, $join=array()){
		return self::dispatchData( self::getAll($search, $fields, $limit, $join), true );
	}
	
	/**
	 * Update each object inside a collection
	 *
	 * @param	Array		$collection		array of same objects
	 */
	public static function updateCollection( $collection ){
		foreach($collection as $obj){
			$obj->update();
		}
	}
	
	/**
	 * Transform an db request result array into a collection
	 *
	 * @param	Array		$rows		db request result
	 * @param	string		$cl			Class of the object
	 *
	 * @return	Array		All entries on databalse for this object, array of objects/array
	 */
	public static function dispatchData($rows, $toArray=false, $fielCheck=false, $fieldValue=false){
		if ($rows===false || empty($rows)) return array();
		$cl= static::$class;
		$r = array();
		$k = 0;
		$map = array();
		foreach($rows as $key=>$row){
			$o = new $cl($row);
			$id = (int)$o->{$o->dbPrimary};
			if ($fielCheck && $o->{$fielCheck}!==$fieldValue) continue;
			if (!array_key_exists($id, $map)){
				$map[$id] = $k;
				$r[ $map[$id] ] = !$toArray ? $o : $o->toArray();
				$k++;
			} 
			else { // retrieve other language of the same object
				if (is_object($r[ $map[$id] ])) $r[ $map[$id] ]->contents[$row['iso_lang']] = $o->contents[$row['iso_lang']]; 
				else $r[ $map[$id] ]['contents'][$row['iso_lang']] = $o->contents[$row['iso_lang']];
			}
		}
		return $r;
	}
	
	/**
	 * Create an Object and this entry on table from array
	 *
	 * @param	array		$values		Array of each fields values (each key represents a field)
	 *
	 *	The primary key (field) should not be present on the array !
	 *
	 * @return	Object		Instance the new object
	 */
	public static function create( $values ){
		$new = self::get(false);;
		foreach( $new->dbSchema as $key=>$value)
			if ( isset($values[$key]) )	$new->{$key} = $values[$key];		
		$r = $new->update();
		if ($r==false) return false;
		return $new;
	}
	
	/**
	 * Delete an object(whith it primary key)
	 *
	 * @param	string		$primary		Primary value to use to find object on table
	 *
	 * @use	DefaultModel::getBy
	 *
	 * @return	Object		Instance a requested object
	 */
	public static function delete( $primary ){
		$obj = self::get($primary);
		return $obj->remove();
	}
	
	/**
	 * Delete an object (with one information about it)
	 *
	 * @param	string		$field		Field to use to find object on table
	 * @param	string		$value		Value to search
	 *
	 * @use	DefaultModel::getBy
	 *
	 * @return	Object		Instance a requested object
	 */
	public static function deleteBy( $field, $value ){
		$obj = self::get();
		return Db::get( $obj->dbDriver)->remove( $obj->dbTable, array($field=>$value) );
	}
	
	/**
	 * Test the existance of the table // no check yet
	 *
	 * @return	boolean		Operation success
	 */
	public static function tableExists($cl=false, $isObj=false){
		$cl= self::$class;
		$obj = ($isObj) ? $cl : new $cl();
		$r = Db::get( $obj->dbDriver)->tableExists( $obj->dbTable );
		return $r!==false;
	}
	
	/**
	 * Create the table // no check yet
	 *
	 * @return	boolean		Operation success
	 */
	public static function createTable(){
		$obj = self::get();
		return Db::get( $obj->dbDriver)->createTable( $obj->dbTable , $obj->dbSchema);
	}
	
	
	/**
	 * Delete the table // no check yet
	 *
	 * @return	boolean		Operation success
	 */
	public static function removeTable(){
		$obj = self::get();
		return Db::get( $obj->dbDriver )->drop( $obj->dbTable, $obj->dbSchema);
	}
	
	/**
	 * Empty the table // no check yet
	 *
	 * @return	boolean		Operation success 
	 */
	public static function eraseTable(){
		$obj = self::get();;
		return Db::get( $obj->dbDriver)->truncate( $obj->dbTable );
	}
	
	
	/**
	 * Change Database Driver for an object
	 *
	 * @param	string	$driver		Driver to use
	 */
	public static function _setDb($driver, $cl){
		if (!class_exists($cl)) return;
		$cl::$dbForcedDriver = $driver;
	}
	
	/**
	 * Load a class (from ROOT/classes/)
	 *
	 * @param	string	$cl		Class to import
	 */
	public static function loadObject($cl){
		$file = _CLASSES_DIR_.strtolower($cl).'.php';
		if (!file_exists($file)) return false;
		include_once($file);
		if (! class_exists($cl)) return false;
		return new $cl;
	}


	/**
	 * Get the last entrie inserted for an object
	 *
	 * @return	boolean		If the dbSchema doesn't contains any insert_date field
	 * @return  Object 		If the operation success 
	 */
    public static function getLastInserted(){
		$obj = self::get();
		if (!isset($obj->dbSchema['insert_date'])) return false;
    	$r = Db::get( $obj->getDriver() )->select($obj->getTable(), array('insert_date'=> array(date('Y-m-d H:m:s'),'<', 'DESC')), false,  1 );
    	$id = isset($r[0]) ? $r[0][$obj->getPrimary()] : null;
    	return new $cl($id);
    }
}
?>