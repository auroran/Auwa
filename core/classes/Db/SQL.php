<?php
namespace Auwa;
/**
 * CRUD DAO for SQL database
 *
 * Enable database requests for SQL based server
 *
 * @package MiddleWay \core\classes\Db
 * @copyright 2017 AuroraN
 */
 
/**
 * SQL Class give methods to execute SQL Langage requests on database
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 * 
 * @todo include tools to advanced sql : join .. etc
 */
class SQL extends Db{
	
	/*
	 * Basic prepared requests
	 * @var string
	 */
	protected static $select_request 		= 'SELECT {fields} FROM `{table}`{joinRequest} WHERE {values};';
	protected static $join_resquest		 	= ' INNER JOIN `{table2}` ON `{table1}`.`{join}`=`{table2}`.`{join}`';
	protected static $table_request 		= 'SELECT {fields} FROM `{table}`{joinRequest};';
	protected static $insert_request 		= 'INSERT INTO `{table}` ({fields}) VALUE ({values});';
	protected static $update_request 		= 'UPDATE {table} SET {set} WHERE {values};';
	protected static $remove_request 		= 'DELETE FROM `{table}` WHERE {values};';
	protected static $drop_request 			= 'DROP TABLE `{table}`;';
	protected static $create_request 		= 'CREATE TABLE IF NOT EXISTS `{table}` ({values});';
	protected static $truncate_request 		= 'TRUNCATE `{table}`;';
	
	
	/*
	 * Base object, set by the Contructor
	 * Object
	 */
	protected $base = false;
	/**
	 * Type of driver used by PDO
	 * @var string
	 */
	protected $type = 'mysql'; // default database type 
		
	/**
	 * Request or Query
	 * @var PDOStatement Object
	 */
	public $request = null;
	
	/*
	 * Database driver
	 * @var string
	 */
	protected $dbDriver = 'MySQL';
	
	
	/** 
	 * CONSTRUCTOR
	 *
	 * Get a connection, and store it in SQL::$base
	 */
	public function __construct(){
		try{
			$this->base = new \PDO(
							$this->type.':host='.BASE_SERVER.';dbname='.BASE_NAME.';charset=utf8', 
							BASE_USER, 
							BASE_PASS);
			$this->base->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
   			$this->base->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		} catch(Exception $e) {
			if (Error::$errorReporting >=3 ) trigger_error($e->getMessage());
			trigger_error($this->type." : Connexion impossible.",E_USER_ERROR);

		}
	}
	
	/** 
	 * DESTRUCTOR
	 *
	 * Leave connection
	 */
	public function __destruct(){
		unset( $this->base );
		$this->base = false;		
	}
	
	/**
	 * Prepare the SQL expression.
	 *
	 * @param string $prepared Initial SQL
	 * @param array $input Parameters to inject into SQL
	 *
	 * @use				SQL::$prepared	 	propertie whith value to search
	 *
	 * @return array Prepared SQL on $prepared propertie
	 */
	protected function setPrepared($prepared, $input, $limit=false){
		foreach($input as $key=>$value)
			$prepared = str_replace( '{'.$key.'}', $value, $prepared);	
		if (empty($input['values'])) $prepared = str_replace('WHERE','', $prepared);
		$prepared = str_replace('AND `|','OR `', $prepared);
		$this->prepared = $prepared; 
		if (!empty($this->order) && is_array($this->order)) {
			$order = ' ORDER BY '.implode(', ', $this->order);
			$this->prepared = str_replace(';',$order.';',$this->prepared);
		}
		$join = '';
		if (!empty($this->join)){
			foreach($this->join as $rule){
				$rpl = array(isset($rule[2]) && $rule[2] ? $rule[2] : $input['table'], $rule[1], $rule[0]);
				$join .= str_replace( array('{table1}', '{table2}', '{join}'), $rpl, self::$join_resquest );
			}
		}
		$this->prepared = str_replace( array('{table}','{joinRequest}'), array($input['table'],$join), $this->prepared );
		if ($limit && is_int($limit)) $this->prepared = str_replace(';',' LIMIT '.$limit.';',$this->prepared);
	}
	
	public function addJoin($input, $table1=false, $table2=false){
		if (!$this->join) $this->join = array();
		if ($table1===false && !is_array($input)) return;
		$this->join[] = is_array($input) ? $input : array($input, $table1, $table2);
	}
	/**
	 * Parse where clause.
	 *
	 * @param array 	$input 			Parameters to inject into SQL
	 *
	 * @use				SQL::values	 	propertie whith value to search
	 *
	 * @return array 	$where 			Array of different parto of where clause
	 */
	public function whereParse($where){
		$opEnabled = array('=','>','<','>=','<=','<>','LIKE');
		$this->order = array();
		foreach ($where as $key => $value) {
			if ($key==='order' && is_array($value)){
				 $ot = isset($value[2]) ? $value[2] : '{table}';
				 $this->order[] = "`$ot`.`".$value[0].'` '.$value[1];
				 unset($where[$key]);
				 continue;
			}
			$op = '=';
			$f =$key;
			$t = '{table}';
			if (is_array($value)){		// if the clause is complex : field=>array(value,operator)
				$op = ( isset($value[1]) && in_array($value[1], $opEnabled) ) 
							? $value[1] : '=';
				$f =$key;
				if (isset($value[2])) $this->order[] = "`{table}`.`$f` ".$value[2];
				if (isset($value[3])) $t = $value[3];
				// case of associate array
				if (isset($value['op']) && in_array($value['op'], $opEnabled)) $op = $value['op'];
				if (isset($value['table'])) $t = $value['table'];
				if (isset($value['order'])) $this->order[] = "`$t`.`$f` ".$value['order'];
				$value = isset($value['val']) ? $value['val'] : $value[0];
			}
			$this->values[] = $value;
			$where[$key] = "`$t`.`$f` $op ?";
		}
		return $where;
	}
	
	/**
	 * Make a SELECT request and execute it.
	 *
	 * @param string 	$table 			Name of the SQL Table
	 * @param array 	$where 			Values to search
	 * @param array 	$fields 		List of fields to retrieve
	 *
	 * @use		 		SQL::$values 	Propertie to an empty array
	 * @use		 		SQL::executeR	Prepare and execute an prepared request.
	 *
	 * @See SQL::whereParse for know different formats that $where can use
	 *
	 * @return Object 					Result of SQL::executeR method
	 */
	public function select($table, $where=false, $fields=false, $limit=false){
		$this->values = array();
		$this->order =array();
		$fields = ( $fields==false ) ? '*' : ( is_array($fields) ? implode(', ',$fields) : $fields );
		
		if ($where) { 	// search specific values
			$where = implode(' AND ', $this->whereParse($where, $table) );
 			$this->setPrepared( self::$select_request, array(
										'fields'	=>$fields,
										'values'	=>$where,
										'table'		=>$table,
									), $limit);
		} else {		// get all table
			$this->setPrepared(self::$table_request, array(
												'fields'	=>$fields,
												'table'		=>$table
											));
		}
		//if ($this->join) { echo "<br>".PHP_EOL.$this->prepared.PHP_EOL.'<br><br>'.PHP_EOL; }
		return $this->executeR();
	}

	/**
	 * Make a UPDATE request and execute it.
	 *
	 * @param string 	$table 			Name of the SQL Table
	 * @param array 	$where 			Values to search and update
	 * @param array 	$set 			Values to set
	 *
	 * @See SQL::whereParse for know different formats that $where can use
	 *
	 * @use		 		SQL::$values 	Propertie to an empty array
	 * @use		 		SQL::executeS	Prepare and execute an prepared request.
	 *
	 * @return Object 					Result of SQL::executeS method
	 */
	public function update($table, $where, $set, $fields=false){
		$this->values = array();
		$fields = ( empty($fields) ) ? '*' : is_array($fields) ? implode(', ',$fields) : $fields;
		foreach ($set as $key => $value) {
			$this->values[] = $value;
			$set[$key] = '`'.$key.'`=?';
		}
		$where = implode(' AND ', $this->whereParse($where) );
		$this->setPrepared(self::$update_request, array(
									'fields'	=>$fields,
									'set'		=>implode(',', $set),
									'table'		=>$table,
									'values'	=>$where
								));
		return $this->executeS();
	}
	
	/**
	 * Make a INSERT request and execute it.
	 *
	 * @param string 	$table 			Name of the SQL Table
	 * @param array 	$data 			Values to insert
	 *
	 * @use		 		SQL::$values 	Propertie to an empty array
	 * @use		 		SQL::executeS	Prepare and execute an prepared request.
	 *
	 * @return Object 					Result of SQL::executeS method
	 */
	public function insert($table, $data, $specialParameter=null){
		$fields = array();
		$this->values = array();
		foreach ($data as $key => $value) {
			$fields[] = $key;
			$this->values[] = $value;
		}
		$set = implode(',', array_fill(0, count($data), '?'));
		$this->setPrepared(self::$insert_request, array(
											'fields'	=>implode(',',$fields),
											'table'		=>$table,
											'values'	=>$set,
										));
		return $this->executeS();
	}
	
	/**
	 * Make a DELETE request and execute it.
	 *
	 * @param string 	$table 			Name of the SQL Table
	 * @param array 	$where 			Values to search and delete
	 *
	 * @See SQL::whereParse for know different formats that $where can use
	 *
	 * @use		 		SQL::$values 	Propertie to an empty array
	 * @use		 		SQL::executeS	Prepare and execute an prepared request.
	 *
	 * @return Object 					Result of SQL::executeS method
	 */
	public function remove($table, $where){
		$this->values = array();
		$where = implode( ' AND ', $this->whereParse($where) );
		$this->setPrepared(self::$remove_request, array(
											'values'	=>$where,
											'table'		=>$table
										));
		return $this->executeS();
	}
	
	/**
	 * Make a DROP request and execute it.
	 *
	 * @param string 	$table 			Name of the SQL Table
	 *
	 * @use		 		SQL::executeS	Prepare and execute an prepared request.
	 *
	 * @return Object 					Result of SQL::executeS method
	 */
	public function drop($table){
		$this->setPrepared(self::$drop_request, array(
											'table'	=>$table
										));
		return $this->executeS();
	}
	
	/**
	 * Make a CREATE TABLE request and execute it.
	 *
	 * @param string 	$table 			Name of the SQL Table
	 * @param array 	$fields 		Fields of the Table
	 * @param array 	$type	 		Settings of each field (type, size, primary...)
	 *
	 * @use		 		SQL::executeS	Prepare and execute an prepared request.
	 *
	 * @return Object 					Result of SQL::executeS method
	 */
	public function createTable($table, $fields, $types=array() ){
		$values=array();
		if (empty($fields) or !is_array($fields)) return false;
		foreach($fields as $key=>$value)
			$values[] = ( !isset($types[$key]) ? $key.' ' : '').$value.( isset($types[$key]) ? ' '.$types[$key] : '');
		
		$this->setPrepared(self::$create_request, array(
											'values'	=>implode(', ',$values),
											'table'		=>$table
										));
		return $this->executeS();
	}
	
	/**
	 * Make a TRUNCATE request and execute it.
	 *
	 * @param string 	$table 			Name of the SQL Table
	 *
	 * @use		 		SQL::executeS	Prepare and execute an prepared request.
	 *
	 * @return Object 					Result of SQL::executeS method
	 */
	public function truncate($table){
		$this->setPrepared(self::$truncate_request, array(
											'table'	=>$table
										));
		return $this->executeS();
	}
	
	/**
	 * Check if a table exists in the current database.
	 *
	 * @param PDO $pdo PDO instance connected to a database.
	 * @param string $table Table to search for.
	 * @return bool TRUE if table exists, FALSE if no table found.
	 */
	public function tableExists($table) {
		    // Try a select statement against the table
		    // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
		    try {
		        $result = $this->base->query("SELECT 1 FROM $table LIMIT 1");
		    } catch (\PDOException $e) {
		        // We got an exception == table not found
		        return false;
		    }
		    return $result !== false;
	}
	
	/**
	 * Prepare and execute a prepared request.
	 *
	 * @param string 	$sql			SQL Command, not prepared | if possible, don't use this option
	 *
	 * @return boolean	$r				Result of sql execution
	 */
	public function execute($sql=false){
		if ($sql) 		$this->prepared = $sql;
		$r = false;
		if (!$this->base) return false;
		try {
			$this->request = $this->base->prepare( $this->prepared );
			$r = $this->request->execute( $this->values);
			$this->join = false;
			if (!$r) trigger_error($this->request->errorInfo());
			return $r;
		} catch (\PDOException $e) {
			return false;
		} catch (\Exception $e) {
		  return false;
		}
	}
	
	/**
	 * Launch SQL Execution in write mode.
	 *
	 * @param string 	$sql			SQL Command, prepared or not
	 * @param array 	$values			Values to use in prepared SQL Command
	 *
	 * @use		 		SQL::execute	Prepare and execute an prepared request.
	 *
	 * @return boolean	$res			Result of sql execution
	 */
	public function executeS($sql=false, $values=array()){
		if ($sql) 		$this->prepared = $sql;
		if (!empty($values))	$this->values = $values;
		$res = $this->execute();

		return $res;
	}

	/**
	 * Launch SQL Execution in read mode.
	 *
	 * @param string 	$sql		SQL Command, prepared or not
	 * @param array 	$values		Values to use in prepared SQL Command
	 *
	 * @use		 		SQL::execute	Prepare and execute an prepared request.
	 *
	 * @return array	$results	All rows witch match with the request
	 */
	public function executeR($sql=false, $values=array()){
		if ($sql) 		$this->prepared = $sql;
		if (!empty($values))	$this->values = $values;
		$status = $this->execute();
		if ($status===false) return false;
		$this->request->setFetchMode(\PDO::FETCH_ASSOC);
		$results = array();
		foreach($this->request as $vals)
			$results[] = $vals;
		$this->request->closeCursor();
		
		return $results;
	}

	/**
	 * Get the fields list of a table
	 *
	 * @param string 	$table		Table
	 *
	 * @return array	$fields		Array of fields
	 */
	public function getFields($table){
		$this->prepared = "SHOW FIELDS FROM $table";
		$r = $this->executeR();
		if (!$r) return false;
		$fields = array();
		foreach($r as $info){
			$desc = array( strtoupper($info['Type']) ); 
			if (!empty($info['Extra'])) $desc[] = strtoupper($info['Extra']);
			if ($info['Key']=="PRI") $desc[] = 'PRIMARY KEY';
			$fields[ $info['Field'] ] = implode(' ',$desc);
		}
		
		return $fields;
	}
}


/**
 * Basic and native support of MySQL
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class MySQL extends SQL{
	protected $type = 'mysql';
	public function lastInsertId(){
		return $this->base->lastInsertId();
	}
}

/**
 * Basic and native support of Oracle
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class Oracle extends SQL{
	protected $type = 'oci';
}

/**
 * Basic and native support of PostgreSQL
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class PostgreSQL extends SQL{
	protected $type = 'pgsql';
}

/**
 * Basic and native support of SqLite
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class SqLite extends SQL{
	protected $type = 'sqlite';
}

?>