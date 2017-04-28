<?php
namespace Auwa;
/**
 * Module implementation
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */
 
 
/**
 * Give methods to load and use module on Middle Way
 *
 * All modules should extend this class
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class Module{
	public $name = null;
	public $session;
	public $auwa;
	public $author = null;
	public $templatePath = null;
	public $autoControllerLoad = array();
	public $DbMethod = 'default';
	public $modType;
	public $path="unkwnow";
	public $rewriteRules = array();
	public $e = false;
	
	private static $depR; // dependencies cache

	public static $loaded=array();

	/**
	 * Module load
	 *
	 * @param string	$name		Name of the module
	 * @param string	$param		Call parameters, separed by #
	 *
	 * Values of $param
	 *	- call : 	true | false | ø				for call a controller if defined
	 *	- system :	true | ø						for a system module	
	 *	- ghost :	true | ø						juste for inclusion and module methods use
	 *	- mode :	default | header | script | ø	type of content of Controller to retrive
	 *
	 *	ex : "call:true#mode:header"
	 *
	 * A module can be launched manualy even if it is not registered for this Auwa Controller
	 *
	 * @return Module object		If module is correctly loaded
	 * @return Error object			if an error occured, return an error (and exit this function)
	 *
	 */ 
	public static function load($name=false, $param='call:true'){
		if (!$name) return new Error('Aucun nom de module saisi...');
		/*
		 * Parameters checking
		 */
		 $p = self::parseParams($param);
		$modDir = (isset($p['system'])) ? _SYS_MOD_DIR_ : _MOD_DIR_;
		$classType = ($modDir==_SYS_MOD_DIR_) ? 'Admin' : '';
		$modType = (isset($p['system'])) ? 'system' : 'default';
		
		if ($name!=='login' && $modType=='system' && !User::isCoreUser() )
				return new Error('Seul un utilisateur autorisé peut acceder à ce module');
		// Class name 
		$className = ($name).'Module'.$classType;
		
		include_once($modDir."$name/module.php");
			
		/* 
		 * Check dependencies if module never loaded or previous launch has fail
		 */
		if (! isset(Session::get()->modulesLoaded[$name.$classType]) or !Session::get()->modulesLoaded[$name.$classType] ){ 
			Session::get()->_set('modulesLoaded', $name.$classType,  false);			
			$deps = self::checkDependencies($name, $modDir);
			if ( Check::isError($deps)) return $deps;
			if( $deps===false ) 
				return new Error('Dependences non satisfaites pour <code>'.$name.'</code> : '.self::getMissingDependencies($name,$modDir));
		}
		
		/* 
		 * Check integrity if module class doesn't exists
		 */
		if (!class_exists($className)){
			Session::get()->_set('modulesLoaded', $name.$classType,  false);
			if(! is_dir($modDir.$name)) 
				return new Error('Module <code>'.$name.'</code> non existant...');
			if(! is_file($modDir.$name."/module.php")) 
				return new Error('Module <code>'.$name.'</code> : fichier <code>module.php</code> absent...');
			return new Error('Module <code>'.$name.'</code> incompatible ou défectueux');
		}
			
		/*
		 * Launch module, all seens to be OK
		 */			
		$m = new $className();
		$m->auwa = Auwa::get();
		$m->path = $modDir."$name/";
		$m->path = ($modType=='default') ? "modules/$name/" : "core/modules/$name/"; //WHY THIS LINE ??
		$m->modType = $modType;
		$m->init($p);
		if (!isset(self::$loaded[$name.$classType]))
			Rewriting::$rewriteRules = array_merge( Rewriting::$rewriteRules, $m->rewriteRules );

		Session::get()->_set('modulesLoaded', $name.$classType,  true);
			
		if ( Check::isError($m->e) ) 
			if ( $m->e->hasError() ) return $m->e;
		return $m;
	}

	/**
	 * Automatic load of module 
	 *
	 * @param 	string	$param		@see Module::load
	 *
	 * Only module associed to the called Auwa Controller (or to all) 
	 * will be laod
	 *
	 * @return array	lsit of module loaded
	 */	
	public static function autoLoad($param=false){
		$controller = Tools::getValue('AuwaController') 
						? Tools::getValue('AuwaController') 
						: _DEFAULT_CTRL_;
		$modules = \ConfigFile::getConfig('config/modules');
		if ( !isset($modules['autoLoad']) or !is_array($modules['autoLoad'])) return false;

		foreach ($modules['autoLoad'] as $key => $module) {
			if ( 	(bool)preg_match('/'.$controller.',/', preg_replace('/ /','',$module).',') 
					|| (bool)preg_match('/all/', $module)){
				$e =self::load($key, $param);
				if (Check::isError($e)) $e->displayErrors();
			} 
		}
		return Session::get()->modulesLoaded;
	}

	
	
	/**
	 * Register this module into a Hook
	 *
	 * @use Module::$name to set module name
	 * @see Hook::registerHook
	 *
	 * @param 	string	$name	Name of the Hook
	 */
	public function registerHook($name=false){
		if (! isset($name) ) return;
		Hook::registerHook($name, $this->name);
	}

	
	/**
	 * Parse parameters string 
	 *
	 * @param 	string	$param	@see Module::load
	 *
	 * @return 	array	$p		Array of each parameter value
	 *
	 */
	public static function parseParams($param=null){
		if ($param==null) return null;
		// lecture des paramètres de chargement
		$params = explode('#', $param);
		foreach ($params as $key => $value) {
			$temp = explode(':', $value);
			$p[trim($temp[0])] = trim($temp[1]);
		}
		return $p;
	}

	/**
	 * Initialize module
	 * 
	 * @param	array $p	@see Module::parseParams
	 *
	 * Controller and ajax calls were effectued by this method
	 */
	public function init( $p=array() ){
		if ($this->DbMethod=='default') $this->DbMethod=_DB_DEFAULT_METHOD_;
		$this->path = (isset($p['system'])) ? _SYS_MOD_DIR_.$this->name : _MOD_DIR_.$this->name;
		$this->session = Session::get();
		$ghost = (isset($p['ghost'])) ? ( ($p['ghost']=='true') ? true: false ) : false; 
		if ($ghost) return;													// a ghost call launch nothing
		$this->e = new Error();
		$callController = (isset($p['call'])) ? ( ($p['call']=='true') ? true: false ) : true; 
		$mode = (isset($p['mode'])) ? $p['mode'] : 'default';
		if ($mode == 'ghost') $callController=false;
		if ($callController) {
			foreach ($this->autoControllerLoad as $key => $name)		// read all auto lauch controller
				$this->e->merge( $this->callController($mode, $name) );
			$this->e->merge( $this->callController($mode) );				// call a specific controller
		}
	}

	/**
	 * Get Rewrite rules for this module
	 *
	 * @todo All
	 */ 
	public function getRewriteRules(){
		return true;
	}
	
	/**
	 * Load a template
	 *
	 * @param	string	$template		Name of the template
	 * @param	boolean	$hook			True if template is for a Hook
	 *
	 * @See Tools::includeTpl for more details
	 *
	 * @return 	Error	$_eInc			Error Object (can by empty)
	 */
	public function includeTemplate($template=null, $hook=true){
		$_eInc = new Error();
		$sub = ($hook) ? '/views/hook/' : '/views/'; // subdir in module template path
		if ($template==null) $template = $this->name;
		if (!$this->template) $this->template = new Template();
		$this->template->link($template, $this->path.$sub);
		return $this->template->display();
	}

	/**
	 * Set value of a template variable into Auwa instance
	 *
	 * @param	string or array		$name		Name of the variable
	 * @param	mixed				$value		Value of the variable
	 */
	public function setVar($name, $value=null){
		if(!is_array($name))
			$name=array( $name=>$value );
		foreach ($name as $key => $value)
			$this->auwa->setVar($key, $value);
	}
	
	/**
	 * Get value of a template variable from Auwa instance
	 *
	 * @param	string		$name		Name of the variable
	 *
	 * @return	mixed		$value		Value of the variable
	 */
	public function getVar($name){
		return $this->auwa->getVar($name);
	}
	
	/**
	 * Get the name of Controller called by url
	 *
	 * The name is retrieved from a request variable, set by the url parser
	 *
	 * @return string		name of the controller
	 */
	public static function controllerCalled(){
		return ( Tools::getValue('controller') );
	}

	/**
	 * Call a controller
	 *
	 * @param 	string		$mode		Launch mode
	 * @param	string		$name		Name of the controller, if use @use Module::controllerCalled
	 *
	 *						Mode :	default, default mode, launch all template
	 *								header, launch only css styles
	 *								script, launch only js scripts
	 *
	 * @return 	Error		$e			Error during controller launch
	 */
	public function callController($mode='default', $name=null){
		$e = new Error();
		if ($name ==null) $name = self::controllerCalled();
		if (!$name) return $e;
		if (is_file( $this->path.'/controllers/'.$name.'.php') ){
			switch ($this->modType) {
				case 'system':
					$e = CoreController::call( $name, $mode, $this->path.'/controllers/', $this->name);
					break;
				case 'app':
					$e = AppController::call( $name, $mode, $this->path.'/controllers/', $this->name);
					break;
				default:
					$e = Controller::call( $name, $mode, $this->path.'/controllers/', $this->name);
					break;
			}
		}
		else $e->addError($name.' : Ce contrôleur n\'existe pas' );
		
		return $e;
	}
	
	/**
	 * Get the full name of a module
	 *
	 * @return string	Full name of the module
	 */
	public function getFullName(){
		$c = ConfigFile::getConfig( $this->path.'/'.'module', false );
		return isset($c['name']) ? $c['name'] : $name;
	}


	/**
	 * Get the config file for a module
	 *
	 * @param	string	$name		Name of the module
	 * @param	string	$modDir		Directory of the module
	 *
	 * @return	array				Array of each config parameters
	 */
	public static function getModuleConfig($name,$modDir=_MOD_DIR_){
		if (!is_file( $modDir.$name.'/'.'module.yml') ) return false;
		return \ConfigFile::getConfig( $modDir.$name.'/'.'module', false );
	}
	
	/**
	 * Get full dependencies array for a module, including dependencies of dependences
	 *
	 * @param	string	$name		Name of the module
	 * @param	string	$modDir		Directory of the module
	 *
	 * @return	array				Array of each dependencies
	 */
	 
	public static function getFullDependencies($name,$modDir=_MOD_DIR_){
		$deps=array();
		if ( isset( self::$depR[trim($name)] ) ) return self::$depR[trim($name)];
		$conf = self::getModuleConfig($name,$modDir);
		self::$depR[trim($name)]=array();
		if (isset($conf["dependencies"])){
			$dependencies = explode(',',$conf["dependencies"]);
			self::$depR[trim($name)]=$dependencies;
			$deps = $dependencies;
			foreach($dependencies as $key=>$dep){
				$isIn = ( isset( self::$depR[trim($dep)] ) );
				if (!$isIn){
					$res= self::getFullDependencies(trim($dep), $modDir);
					self::$depR[trim($dep)]=$res;
					if ( !empty($res) ) $deps = array_merge($deps, $res );
				} else $deps= array_merge($deps, self::$depR[trim($dep)]) ;
			}
		}
		return $deps;
	}
	
	/**
	 * Retrieve missing dependencies for a module
	 *
	 * @param	string	$name		Name of the module
	 * @param	string	$modDir		Directory of the module
	 *
	 * @return	string				String list of all missing modules or deficient module notification
	 */
	public static function getMissingDependencies($name,$modDir=_MOD_DIR_){
		$deps = self::getFullDependencies($name,$modDir);
		$miss = "";
		foreach($deps as $key=>$dep){
			if (!is_dir($modDir.trim($dep)) && !is_file($modDir.trim($dep)."/module.php") && !is_file($modDir.trim($dep)."/moduleUI.php")) {
				if ($miss!="") $miss .=", ";
				$miss .=$dep;
			}
		}
		return empty($miss) ? 'Un des modules ne peut être chargé' : $miss;
	}
	/**
	 * Check dependencies for a module
	 *
	 * @param	string	$name		Name of the module
	 * @param	string	$modDir		Directory of the module
	 *
	 * @return	array				Array of each dependencies
	 */
	public static function checkDependencies($name, $modDir=_MOD_DIR_){
		$e = new Error;
		static $depR=array();
		$autoLoaded = array();
		$log = "";
		if (!array_key_exists($name, $depR)){
			$depR[]=$name;
		}
		if ( isset( self::$depR[trim($name)] ) ) $list = self::$depR[trim($name)];
		else { 
			$config = self::getModuleConfig($name,$modDir); // array or false
			if (!$config) {
				$modDir = str_replace(_ROOT_DIR_, '', $modDir );
				$e->addError("Fichier de configuration absent (<code>$modDir$name/module.yml</code>)");
				return $e;
			}
			if ( !isset($config['dependencies']) )
				return true;
			if (! empty($config['dependencies']))
				$list = explode(',', $config['dependencies'] );
		}
		
		if (isset($list)){
			foreach($list as $key=>$dep){
				$isIn = in_array(trim($dep), self::$loaded);
				if ( !isset(self::$loaded[$name.$classType]) )
					if (Check::isError( self::load(trim($dep)) )) return false;
			}
		}
		if (!isset($config)) return true;
		return ( $config['dependencies'] );
		
	}	
}
?>
