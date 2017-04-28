<?php
namespace Auwa;
/**
 * User Management
 *
 * @package Auwa \core\classes\
 * @copyright 2015 AuroraN
 */

/**
 * User Object
 *
 * This object extend DefaultModel Class
 *
 * Give methods for user connexion or management
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */ 
class User extends DefaultModel{

	/*
	 * User ID
	 * @var int
	 */
	public $id;
	
	/*
	 * User Login
	 * @var string
	 */
	public $login=false;
	
	/* 
	 * User Password
	 * @var string
	 */
	protected $passwd;
	
	/*
	 * User Mail
	 * @var string
	 */
	protected $mail;

	/*
	 * User Name
	 * @var string
	 */
	protected $name;
	
	/*
	 * User Status
	 * @var string
	 */
	protected $status;
	
	/*
	 * Define if user is enabled or not
	 * @var boolean
	 */ 
	protected $enable;
	
	/*
	 * Random string (Security pass)
	 * @var string
	 */
	protected $rand;		// never use in database

	/*
	 * Session variable used
	 * @var string
	 */
	protected static $sessvar='auth';
	
	/*
	 * DB fiedls definition
	 * @var array
	 */
	protected $dbSchema = array(	'id'		=> 'SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
									'login'		=> 'VARCHAR(255)', 
									'passwd'	=> 'VARCHAR(255)',
									'mail'		=> 'VARCHAR(255)', 
									'name'		=> 'VARCHAR(255)',
									'status'	=> 'VARCHAR(100)', 
									'enable'	=> 'TINYINT'
							);	
	/*
	 * Database driver
	 * @var string
	 */
	protected $dbDriver = "MySQL";
	
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
	
	
	/**
	 * Get an instance
	 * Get user from database
	 * @use Db class
	 *
	 * The random string is set for each instance
	 */
	public function initObject(){		
		$this->rand = base64_encode(openssl_random_pseudo_bytes(10));
	}
	
	/**
	 * Get the user password
	 *
	 * @return	string			Password
	 */
	public function getPasswd(){
		return $this->passwd;
	}
	
	/**
	 * Get the user status
	 *
	 * @return	string			Password
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 *is the user enabled
	 *
	 * @return	boolean			enable
	 */
	public function isEnable(){
		return $this->enable;
	}

	/**
	 * Get the user mail
	 *
	 * @return	string			Mail
	 */
	public function getMail(){
		return $this->mail;
	}

	/**
	 * Get the user identity
	 *
	 * @return	string			name
	 */
	public function getIdentity(){
		return $this->name;
	}

	/**
	 * Get the user password, increase with the random string
	 *
	 * @return	string			Password concatened with the random string
	 */
	private function getSecuredPasswd(){
		return $this->passwd.$this->rand;
	}

	/**
	 * Connect an User
	 *
	 * @param	string		$login		User login
	 * @param	string		$pass		User password
	 *
	 * @return 	boolean					True if user is connected
	 * @return	string					Error occured during connection
	 */
	public static function connect($login, $pass, $coreUser=false){
		//Session::get()->auth = false;
		$sessvar=static::$sessvar;
		$user = self::getByLogin($login);
		if ( !$user || !$user->login) return 'u00';
		if ( $user->getSecuredPasswd() != md5($pass).$user->rand ) return "u01";
		if ( !$user->enable) return "u02";
		$user->passwd='???';
		if ($coreUser && ($user->status !== "CoreUser" && $user->status !== "CoreRoot")) return "u03";
		Session::get()->{$sessvar} = Tools::encryptDatas( serialize($user) );
		return true;
	}

	/**
	 * Disconnect current user
	 */
	public static function disconnect(){
		Session::get()->_remove('auth');
	}

	/**
	 * Get the current connected user (from the Session instance)
	 *
	 * @return	User		User Object
	 */
	public static function getConnected(){
		$sessvar=static::$sessvar;
		if (  Session::get()->keyExists($sessvar)==false ) return false;
		return  unserialize( Tools::uncryptDatas( Session::get()->{$sessvar} ) );
	}
	public static function getMainConnection(){
		$defaultSessionName = file_get_contents(_CORE_DIR_.'session.id');
		if ($defaultSessionName==Session::$name) return self::getConnected();
		if (	isset($_SESSION[$defaultSessionName]) 
				&& isset($_SESSION[$defaultSessionName]['data']) 
				&& isset($_SESSION[$defaultSessionName]['data'][$sessvar])
			)
			return unserialize( Tools::uncryptDatas( $_SESSION[$defaultSessionName]['data'][$sessvar] ) );
		return false;
	}

	/**
	 * Check if connected user is an admin
	 *
	 * @return	boolean		Check result
	 */	
	final public static function isCoreUser(){
		if(__CLASS__!=='Auwa\User') return false;
		if (!defined('_Auwa_CORE_CONNECTED_')){
			$user = self::getMainConnection();
			$isCU =   ( $user ) ? ($user->status == "CoreUser" || $user->status == "CoreRoot") : false;
			define('_Auwa_CORE_CONNECTED_', $isCU);
			define('_Auwa_ROOT_CONNECTED_', $isCU ? ($user->status == "CoreRoot") : false );
			return $isCU;
		} else return _Auwa_CORE_CONNECTED_; // CACHE
		return false;
	}
	
	/** Get a user by it login
	 *
	 * @param	string	$login		Login to search
	 *
	 * @return	User				User requested
	 */
	public static function getByLogin($login){
		return self::getBy("login", $login);
	}
	
	/** Get a user by it mail
	 *
	 * @param	string	$mail		Mail to search
	 *
	 * @return	User				User requested
	 */
	public static function getByMail($mail){
		return self::getBy("mail", $mail);
	}
	
	/**
	 * Create a user (by it ID)
	 * @see DefaultModel::create
	 */
	public static function create($ID){
		return parent::create($ID);
	}
	/**
	 * Remove a user  (by it ID)
	 * @see DefaultModel::create
	 */
	public static function delete($ID){
		return parent::delete($ID);
	}
	
	/**
	 * Change Database Driver for this object
	 *
	 * @param	string	$driver		Driver to use
	 */
	public static function setDb($driver){
		self::$dbForcedDriver = $driver;
	}
	
	/**
	 * Check if an user with this mail already exists
	 *
	 * @param 	string	$mail		Mail to check
	 *
	 * @return 	boolean				Return user existance
	 */
	public static function mailExists($mail){
		$r = self::getByMail( $mail );
		return ($r!== false && $r->exists());
	}
	
	/**
	 * Check if an user with this mail already exists
	 *
	 * @param 	string	$login		Login to check
	 *
	 * @return 	boolean				Return user existance
	 */
	public static function loginExists($login){
		$r = self::getBylogin( $login );
		return ($r!== false && $r->exists());
	}
	
	/**
	 * Check if an user can register : mail and login not in use
	 *
	 * @param 	string	$login		Login to check
	 *
	 * @return 	boolean				Return user existance
	 */
	public static function userCanRegister($login, $mail){
		$sql = "SELECT id FROM ".$this->dbTable." WHERE `mail`=? OR `login`=?";
		$r = Db::get($this->dbDriver)->executeR( $sql, array('login'=>$login, 'mail'=>$mail) );
		return ($r!== false);
	}

	public static function getUserName($id){
		$u = new User($id);
		if (!$u) return "anyone";
		return  $u->name;
 	}
	public static function getUserLogin($id){
		$u = new User($id);
		if (!$u) return "anyone";
		return  $u->login;
 	}
}

?>