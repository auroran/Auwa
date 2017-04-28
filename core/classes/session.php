<?php
namespace Auwa;
/**
 * Define Session use and tools
 *
 * @package Auwa \core\classes\
 * @copyright 2015 AuroraN
 */
 
/**
 * Session Object
 * 
 * A Session is a iamge of PHP session and sset many usefull variables
 *
 * A Session Instance is called by each Controller and Module
 *
 * The instance is stored into static class variable : Session::$instance
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 *
 */

class Session{

	/*
	 * Name of the session index
	 * @var string
	 */
	public static $name =null;
	
	/* 
	 * Current instance of Session
	 * @var Session
	 */
	protected static $instance;
	
	/*
	 * Title of the site
	 * @var string
	 */
	public $title;

	/*
	 * Publisher of the site
	 * @var string
	 */
	public $publisher;
	
	/*
	 * Title displayed by the navigator
	 * @var string
	 */	 
	public $headTitle;
	
	/*
	 * Name of the company
	 * @var string
	 */
	public $company;
	
	/* 
	 * Name of the site webmaster
	 * @var string
	 */
	public $webmaster;
	
	/*
	 * Contacts mail (webmaster and standart contact)
	 * @var array
	 */
	protected $mail;
	
	/*
	 * Mail settings (Host, auth, port...)
	 * @var array
	 */
	protected $mailSettings;
	
	/*
	 * Middle Way version
	 * @var string
	 */
	public $AuwaVersion;

	/**
	 * Array of all variables set into session
	 * @var array
	 */
	protected $_content;


	/**
	 * Build the session instance
	 * Get data from current session, and set site data
	 */
	public function __construct(){
		// on récupère la session
		if (empty(self::$name))
			self::$name = _SESSION_NAME_;

		$session = self::getSession();
		$i = unserialize(_INFOS_);
		$c = unserialize(_MAIN_CONFIG_);

		// mise à jour
		$this->title 			=	"Title";
		$this->publisher 		=	$i['Infos']['Publisher'];
		$this->headTitle 		=	$i['Infos']['HeadTitle']['Default'];
		$this->webmaster 		= 	$i['Infos']['WebmasterName'];
		$this->mail 			= 	array(
										'contact' => $i['Infos']['ContactMail'],
										'webmaster' => $i['Infos']['WebmasterMail']);
		$this->mailSettings 	= 	$c['MailSettings'];
		$this->company 			=	$i['Company'];
		$this->_content 		= 	&$session['data'];
		$this->AuwaVersion 		=	'Not Defined';
	}

	/** 
	 * Get the currante session or create it with a base sheme
	 *
	 * @return	Session		A Session instance
	 */
	public function getSession(){
		if (!isset($_SESSION[self::$name]))
			self::setSessionSheme();
		return $_SESSION[self::$name];
	}

	/** 
	 * Get mails
	 * @param 	String 		$t 	To get a specific mail
	 * @return	Array			Array of both mail
	 */
	public function getMails($t=null){
		if ( !empty($t) )
			return (isset($this->mail[$t])) ? $this->mail[$t] : 'notFound';
		return $this->mail; 
	}

	/** 
	 * Get mail settings
	 * @return	Array			Array of each settings
	 */
	public function getMailSettings(){
		return $this->mailSettings; 
	}

	/** 
	 * Use a base sheme to create a session
	 */
	public static function setSessionSheme($launch=false){
		if (empty($_SESSION[self::$name])){
			$_SESSION[self::$name]=array();
			$_SESSION[self::$name]['data'] = array();
		}
		if ($launch) self::get(true);
	}

	/**
	 * Get a instance of Session if exits, or build it.
	 *
	 * @return Session		Instance of session
	 */
	public static function get($force=false){
		if (!isset(self::$instance) || $force){
			self::$instance = new Session();
		}
		return self::$instance;
	}

	/** 
	 * Get directly a session variable from instance (magic method)
	 *
	 * @param	string 		$key	Name of the variable = offset in Session::$_content	array
	 *
	 * @return	mixed				Value of the variable
	 */
	public function __get($key){
		return isset($this->_content[$key]) ? $this->_content[$key] : false;
	}

	
	/** 
	 * Set directly a session variable from instance (magic method)
	 *
	 * @param	string 		$key	Name of the variable = offset in Session::$_content	array
	 * @param	mixed		$value	Value of the variable
	 */
	public function __set($key, $value){
		$this->_content[$key] = $value;
		$_SESSION[self::$name]['data'][$key] = $value;
	}
	
	// définie la variable $var[key] avec la valeur $value
	
	/** 
	 * Set an offset of a session variable from instance
	 *
	 * @param	string 		$var	Name of the variable = offset in Session::$_content	array
	 * @param	string 		$key	Name of the variable offset
	 * @param	mixed		$value	Value of the variable
	 *
	 * 		$var is an offset of Session::$_content, ie a session variable
	 * 			This variable must be an array
	 *			(All template variable are offset of this type of session variable = 'TplVars')
	 *
	 * 		$key an offset of this session variable
	 */
	public function _set($var, $key, $value=null){
		if (!isset($this->_content[$var])) return;
		if (!is_array($this->_content[$var])) return;
		$this->_content[$var][$key] = $value;
		$_SESSION[self::$name]['data'][$var][$key] = $value;
	}
	
	/** 
	 * Get an offset of a session variable from instance
	 *
	 * @param	string 		$var	Name of the variable = offset in Session::$_content	array
	 * @param	string 		$key	Name of the variable offset
	 *
	 * 		$var is an offset of Session::$_content, ie a session variable
	 * 			This variable must be an array
	 *			(All template variable are offset of this type of session variable = 'TplVars')
	 *
	 * 		$key an offset of this session variable
	 *
	 * @return	mixed		$value	Value of the variable
	 */
	public function _get($var, $key){
		if (!isset($this->_content[$var])) return false;
		if (!is_array($this->_content[$var])) return false;
		return $this->_content[$var][$key];
	}
	
	/** 
	 * Erase an offset of a session variable from instance
	 *
	 * @param	string 		$var	Name of the variable = offset in Session::$_content	array
	 *
	 */
	public function _remove($var){
		if (!$this->keyExists($var)) return;
		$this->_content[$var]==null;
		$_SESSION[self::$name]['data'][$var] = null;
		unset($this->_content[$var]);
		unset($_SESSION[self::$name]['data'][$var]);
	}
	
	/**
	 * Check if a session variable exists
	 *
	 * @param	string	$key		Name of the session variable
	 *
	 * @return	boolean				True if variable exists, else False
	 */
	public function keyExists($key){
		return ( isset($this->_content[$key]) );
	}

	/**
	 * Erase session and session instance
	 */
	public static function _unset(){
		$_SESSION[self::$name] = null;
		unset($_SESSION[self::$name]);
		self::$instance = null;
	}
}
?>