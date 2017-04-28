<?php
namespace Auwa;
/**
 * Error Management
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */
 

/**
 * Fatal Error Manager
 */
function fatalErrorManager(){
	if (! is_array($e = error_get_last()) ) return;
	$type = isset($e['type']) ? $e['type'] : 0;
	$message = isset($e['message']) ? $e['message'] : '';
	$fichier = isset($e['file']) ? $e['file'] : '';
	$ligne = isset($e['line']) ? $e['line'] : '';
	$infos = unserialize(_INFOS_);
	switch ($type){
		case E_ERROR:
		case E_PARSE:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_USER_ERROR:
			include( _CORE_DIR_.'views/_error.php');
			break;
	}
}
register_shutdown_function('Auwa\fatalErrorManager');


/**
 * Give methods to create and display many Errors
 *
 * Error objets are instance of this class
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class Error{
	
	/** 
	 * Level of error reporting in applicatio
	 * Override constant _DEV_MOD_ effect
	 * @var int
	 */
	 
	public static $errorReporting = 3;
	/**
	 * List of errors
	 * @var array
	 */
	public $e = array();
	
	
	public function __construct($val=null, $type="warning"){
		if ($val!==null){
			$this->e = array();
			if (!is_array($val)){										// $val is a single description
				$this->e[] = array('desc'=>$val, 'type'=>$type);
			} else {
				if ( isset($val['errors']) || isset($val['error']) )	// $val is an function return (array with offset error(s))
					$this->genErrors($val);
				else {
					foreach ($val as $key => $v) 						// $val is a list of errors
						$this->addError($v);
				}
			}
		}
	}
	
	public function __destruct(){
		$this->clear();
	}
	
	/**
	 * Clear all errors on object
	 *
	 */
	public function clear(){
		$this->e = array();
	}
	
	/**
	 * Merge an error Object into this
	 *
	 * @param 	Error 		$ei		Error to merge
	 * @use 				Error::e
	 *
	 */
	public function merge($ei){
		if ( $ei->hasError() ){
			$in = ( $this->hasError() ) ? $in = $this->e : array();
			$this->e = array_merge($in, $ei->e);
		}
	}
	
	/**
	 * Add an error into list
	 *
	 * @param 		string	$n		Error description to insert
	 * @param		string	$t		Type of the error
	 *
	 * Increase list with an array [description, type] build from $n and $t
	 *
	 * @use 				Error:e
	 */
	public function addError( $n, $t="warning" ){
		if (!is_string($n)) return;
		$this->e[] = array('desc'=>$n, 'type'=>$t);
	}
	
	/**
	 * Genere an list of errors from 'error' or 'errors' offset of an array
	 * 
	 * @param		array	$n		Array, many function return an array witch include an error offset
	 *
	 * Increase list with an array [description, type] for each row of $n
	 *
	 * @use 				Error:e
	 */
	public function genErrors($n=array()){
		if (!is_array($n)) return;
		
		$err = array();
		if (isset($n['error']) ) $err = $n['error'];
		if (isset($n['errors']) ) $err = array_merge($err, $n['errors']);	// some functions use offset 'errors'
		
		if (count($err)==0) return;	// no error found
		
		foreach ($err as $key => $v) {
			if (!is_array($v)) $v = array('desc'=>$v, 'type'=>'warning'); // in case of only error description is present
			$this->e[] = $v;
		}
	}
	
	/**
	 * Display all error of object
	 *
	 * @param 	boolean		$return		if true, return a buffer of errors displaying
	 *
	 * @use 				Error:e
	 *
	 * @return	string		$e_content	buffer of errors displaying
	 */
	public function displayErrors($return=false){
		if ($return) ob_start();					// start buffer
		if (!$this->hasError() ) return null;
		foreach ($this->e as $key => $err) {
			switch ($err['type']) {					// dispatch from error type
				case 'error':
					self::displayError( $err['desc'] );
					break;
				case 'info':
				case 'infos':
				case 'information':
					self::displayInfo( $err['desc'] );
					break;
				case 'success':
					self::displaySuccess( $err['desc'] );
					break;
				
				default:
					self::displayWarning( $err['desc'] );
					break;
			}
		}
		if ($return){
			$e_content = ob_get_contents(); 		// get buffer
			ob_end_clean();							// close buffer
			return $e_content; 						// return buffer
		}
	}

	/**
	 * Display all error description
	 *
	 * @return	array		$r	array which contains all errors
	 */
	public function getErrorMsg(){
		if (!$this->hasError() ) return array();
		$r = array();
		foreach ($this->e as $key => $err) {
			$r[] = $err['desc'];
		}
		return $r;
	}
	/**
	 * Display an error
	 *
	 * @param	string	$error	Description of the error
	 * @param	string	$type	Type of the error
	 * 
	 * Try to call a Hook to override default display
	 *
	 * Generic function, can display all type of error
	 */
	public static function displayError($error, $type="danger"){
		if ( empty($error) || !is_string($error) ) return;
		$r = Hook::exec('displayError', $error);
		if (!$r) echo '<p class="alert alert-'.$type.' error col-xs-12">'.$error.'</p>';
	}
	
	/**
	 * Display a warning error
	 *
	 * Try to call a Hook to override default display
	 *
	 * @param	string	$error	Description of the error
	 */
	public static function displayWarning($error){
		if ( empty($error) || !is_string($error) ) return;
		$r = Hook::exec('displayWarning', $error);
		if (!$r) echo '<p class="alert alert-warning warning col-xs-12">'.$error.'</p>';
	}
	
	/**
	 * Display a information
	 *
	 * Try to call a Hook to override default display
	 *
	 * @param	string	$info	Description of the information
	 */
	public static function displayInfo($info){
		if ( empty($info) || !is_string($info) ) return;
		$r = Hook::exec('displayInfo', $info);
		if (!$r) echo '<p class="alert alert-info info col-xs-12">'.$info.'</p>';
	}
	
	/**
	 * Display a succes result
	 *
	 * Try to call a Hook to override default display
	 *
	 * @param	string	$info	Description of the information
	 */
	public static function displaySuccess($info){
		if ( empty($info) || !is_string($info) ) return;
		$r = Hook::exec('displayInfo', $info);
		if (!$r) echo '<p class="alert alert-success info col-xs-12">'.$info.'</p>';
	}

	/**
	 * Check if Object contains errors
	 *
	 * @return	boolean		False if Error::$e propertie is empty, else True
	 */
	public function hasError(){
		return !empty($this->e);
	}
	
	/**
	 * Check if Object contains errors
	 *
	 * OBSOLETE, @use Error::hasError instead
	 *
	 * @return	boolean		False if Error::$e propertie is empty, else True
	 */
	public function hasErrors(){
		$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$desc =  (isset($debug[1])) 
			? 	" &nbsp;&nbsp;&nbsp;&nbsp;<i>> ".
				$debug[1]['class']."::".$debug[1]['function'].
				", on ".$debug[1]['file'].
				" (".$debug[1]['line'].")</i><br>" 
			: '<br>';		
		if (_DEVMODE_) $this->displayError("Obsolete function | Use Error::hasError() instead $desc", "warning");
		return $this->hasError();
	}
	
	/**
	 * Get array of errors
	 *
	 * @return	array	Error::$e	Array of errors
	 */
	public function get(){
		return $this->e;
	}

	
	/**
	 * Hide all errors
	 */
	public static function hideAll(){
		self::$errorReporting = 0;
		ini_set('display_errors',0);
		error_reporting(0);
	}
	/**
	 * Display one or many types of errors
	 *
	 * @param	int	$error_reporting	Error level to display
	 */
	public static function report($error_reporting=false){
		if (!$error_reporting) 
			$error_reporting = _DEVMOD_ ? 3 : 0;
			
		if ($error_reporting<1) return;
		self::$errorReporting = $error_reporting;
		switch ($error_reporting){
			case 4 : error_reporting(E_ALL & ~E_STRICT); break;			// all errors
			case 3 : error_reporting(E_ERROR & ~E_STRICT); break;		// just fatal errors
			case 2 : error_reporting(E_WARNING & ~E_STRICT); break;		// just warnings
			case 1 : error_reporting(E_NOTICE) & ~E_STRICT; break;		// just notices
		}
	}

	public static function getErrMsg( $errCode){
		switch ($errCode) {
			case 'u00':
				return Tools::translateForAll("Utilisateur inconnu", 'errors');
				break;
			case 'u01':
				return Tools::translateForAll('Mauvais mot de passe', 'errors');
				break;
			case 'u02':
				return Tools::translateForAll('Votre compte n\'est pas activé', 'errors');
				break;
			case 'u03':
				return Tools::translateForAll('Vous n\'êtes pas autorisé à vous connecter', 'errors');
				break;


			case 'c00':
				return Tools::translateForAll('Ce contrôleur n\'existe pas', 'errors');
				break;
			case 'p00':
				return Tools::translateForAll('Page non trouvée', 'errors');
				break;
			
			default:
				return Tools::translateForAll('Erreur inconnue', 'errors'). " : $errCode";
				break;
		}
	}
}

?>