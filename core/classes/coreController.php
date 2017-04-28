<?php
namespace Auwa;
/**
 * Admin Controller 
 *
 * @package MiddleWay \core\classes\
 * @copyright 2017 AuroraN
 */
 
/**
 * Admin Controller class
 * This class extends the base controller class
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class CoreController extends AppController{

	

	/**
	 * header use boolean
	 * @var boolean
	 */
	public $useHeader=true;

	public $pageSpecifiClass=false;


	/*
	 * Panel default definition
	 * @var array
	 */
	public static $defaultPanel=array();

	
	/**
	 * Get an CoreController instance
	 *
	 * @see Controller::__contruct
	 */
	public function __construct($mod=null, $name=false, $load='default'){
		if ( Tools::getValue('coreController')!=='CoreLogin' && !User::isCoreUser() && get_class($this)!=='AuwaCoreController')
			trigger_error("ONLY AN <i>CORE USER</i> CAN USE AN <i>CoreController</i>",  E_USER_ERROR);
		AppController::__construct($mod, $name, $load, 'system');
		self::$defaultTemplatePath = _CORE_DIR_.'views/';
		$this->ctrlType = 'system';
		$this->setHeader();
		$this->templatePath = ( isset($this->module) )? _CORE_DIR_.'modules/'.$this->module.'/views/' : self::$defaultTemplatePath;

	}
	
	/**
	 * Call a controller instance, static method
	 * Use this method when your controller iis not from a module
	 * 		or when you will set module affiliation later
	 *
	 * @param	string		$name		Name of the controller
	 * @param	string		$mode		Launch mode
	 *
	 * @return	Error		$e			Error object (can be empty)
	 */ 
	public static function call($name, $mode="default", $path=null, $mod=null){
		return parent::call($name, $mode, $path==null ? _CORE_CTRL_DIR_ : $path, $mod);
	}

}
?>