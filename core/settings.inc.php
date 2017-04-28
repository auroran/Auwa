<?php
/*==============================================================
	AuroraN - ©2016 Grégory Gaudin
================================================================	
	AUWA - DÉFINITION DES CONSTANTES SYSTÈMES
==============================================================*/
	// chargement du parser des fichiers de configuration


	class Definer{
		public static $config;
		public static $infos;
		public static $translations;
		
		public static function setAfterRewritting(){
			if (!is_array(self::$config)) self::$config = ConfigFile::getConfig('config/config');

			if (isset($_GET['session'])) define('_SESSION_ID_', $_GET['session']);

			$langs = \ConfigFile::getConfig('config/lang');
			$multi_lang = !empty($_GET['multilang']) ? $_GET['multilang'] : self::$config['multi_lang'];
			$current_lang = isset($_GET['lang']) 
					? $_GET['lang']  : self::$config['default_lang'];
			define('_MULTI_LANG_', $multi_lang);
			define('_CURRENT_LANG_', $current_lang );
			define('_CURRENT_LOCALE_', $langs[_CURRENT_LANG_]['locale'] );
			define('_DATE_DISPLAY_', $langs[_CURRENT_LANG_]['date']);
			define('_DATE_FORMAT_', $langs[_CURRENT_LANG_]['dateformat']);
			if (!defined('_THEME_'))
				define('_THEME_',isset($_GET['theme']) ? $_GET['theme'] : self::$config['defaultTheme'] );

			if (_THEME_!==null && _THEME_!==''){
				define('_THEME_DIR_', _ROOT_DIR_.'themes/'._THEME_.'/');
				define('_IMG_THEME_DIR_', _THEME_DIR_.'img/');
				define('_CSS_THEME_DIR_', _THEME_DIR_.'css/');
				define('_JS_THEME_DIR_', _THEME_DIR_.'js/');
				define('_TPL_THEME_DIR_', _THEME_DIR_.'templates/');
			} else {
				define('_THEME_DIR_', _TPL_DIR_);
				define('_IMG_THEME_DIR_',_IMG_DIR_);
				define('_CSS_THEME_DIR_', _CSS_DIR_);
				define('_JS_THEME_DIR_', _JS_DIR_);
				define('_TPL_THEME_DIR_', _TPL_DIR_);
			}

			define('_IMG_THEME_URL_', 	str_replace(_ROOT_DIR_, '', _IMG_THEME_DIR_));
			define('_JS_THEME_URL_', 		str_replace(_ROOT_DIR_, '', _JS_THEME_DIR_));
			define('_CSS_THEME_URL_', 	str_replace(_ROOT_DIR_, '', _CSS_THEME_DIR_));

			$currentCtrl = !empty($_GET['AuwaController']) ? $_GET['AuwaController'] : _DEFAULT_CTRL_;
			define('_CURRENT_CTRL_', $currentCtrl);
			define('_CURRENT_RULE_', isset($_GET['urlRule']) ? $_GET['urlRule']: null);
			$i = 	ConfigFile::getConfig('config/infos');
			$r = 	ConfigFile::getConfig('config/rewriting');
			Auwa\Session::get()->title 	=	isset($i['Infos']['Title'][_CURRENT_CTRL_]) ? $i['Infos']['Title'][_CURRENT_CTRL_][_CURRENT_LANG_] : $i['Infos']['Title']['default'][_CURRENT_LANG_];
			if (isset($_GET['Auwa_title_replacement']) && isset($r[$_GET['Auwa_title_replacement']]['title'][_CURRENT_LANG_])){
				Auwa\Session::get()->title = $r[ $_GET['Auwa_title_replacement'] ]['title'][_CURRENT_LANG_];
			}
			// set locales
			\Locale::setDefault(_CURRENT_LOCALE_);
			setlocale(LC_TIME, _CURRENT_LOCALE_);
		}
		/**
	   * Is HTTPS?
	   *
	   * Determines if the application is accessed via an encrypted
	   * (HTTPS) connection.
	   *
	   * @return  bool
	   */
		function isHttps()  {
			$force = isset(self::$config['forceHttps']) ? self::$config['forceHttps']  :false;
			if ($force){
				if ( is_array($force) && isset($force[str_replace('www.','',$_SERVER['HTTP_HOST']).'\//']) ) 
					$force = $force[str_replace('www.','',$_SERVER['HTTP_HOST'])];
			}
			$https = false;
			if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on'){
			  $https = true;
			} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
			  $https = true;
			} elseif (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && $_SERVER['HTTP_FRONT_END_HTTPS'] === 'on') {
			  $https = true;
			}
			$actual_link = $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];

			if (!$https && $force) {
				header('location: https://'.$actual_link ); 
			}
			return $https;
		}
	}

	// Directories access
	define('_ROOT_DIR_', str_replace('core/','',$corePath));
	if(!defined('_CORE_DIR_')) {
		define('_CORE_DIR_', _ROOT_DIR_.'core/');
		define('_CORE_CTRL_DIR_', _CORE_DIR_.'controllers/');
		define('_CORE_CLASSES_DIR_', _CORE_DIR_.'classes/');
		define('_CORE_JS_DIR_', _CORE_DIR_.'js/');
		define('_CORE_CSS_DIR_', _CORE_DIR_.'css/');
		define('_CORE_FW_DIR_', _CORE_DIR_.'frameworks/');
		define('_SYS_MOD_DIR_', _CORE_DIR_.'modules/');
	}

	require_once(_CORE_CLASSES_DIR_.'configfile.php');

	// chargement des fichiers de configuration principaux
	Definer::$config = ConfigFile::getConfig('config/config');
	Definer::$infos = 	ConfigFile::getConfig('config/infos');
	Definer::$translations = ConfigFile::getConfig('config/translations');

	define('_MAIN_CONFIG_', serialize(Definer::$config));
	define('_INFOS_', serialize(Definer::$infos));
	define('_DEFAULT_LANG_', Definer::$config['default_lang']);
	define('_CLASSES_DIR_', _ROOT_DIR_.'classes/');
	define('_CTRL_DIR_', _ROOT_DIR_.'controllers/');
	define('_MOD_DIR_', _ROOT_DIR_.'modules/');
	define('_TPL_DIR_', _ROOT_DIR_.'templates/');
	define('_DATA_DIR_', _ROOT_DIR_.'data/');
	define('_FW_DIR_', _ROOT_DIR_.'frameworks/');
	define('_IMG_DIR_', _ROOT_DIR_.'img/');
	define('_CSS_DIR_', _ROOT_DIR_.'css/');
	define('_JS_DIR_', _ROOT_DIR_.'js/');
	define('_CACHE_DIR_', _ROOT_DIR_.'cache/');
	define('_MULTI_LANG_INIT_', Definer::$config['multi_lang']);

	define('_HEAD_TITLE_BASE_', Definer::$infos['Infos']['HeadTitle']['Base']);
	// chemin local
	define("_LOCAL_DIR_", isset(Definer::$config['localDir']) ? Definer::$config['localDir'] : '');
	define("_DB_METHODS_", Definer::$config['database']['methods']);
	define("_DB_DEFAULT_METHOD_", Definer::$config['database']['default']);
	// Nom de la base ASK et son acces
	define("BASE_NAME",Definer::$config['database']['basename']);
	define("BASE_USER",Definer::$config['database']['username']);
	define("BASE_PASS",Definer::$config['database']['password']);
	define("BASE_SERVER",Definer::$config['database']['server']);
	// Modes de developpements
	define("_DEVMODE_", Definer::$config['devMode']);
	define("_DEV_VERSION_", Definer::$config['devVersion']);
	define("_LOGS_",Definer::$config['logs']);
	// Type de menu
	define("_DEFAULT_MENU_NAME_", Definer::$config['menu']['default']['name']);
	define("_DEFAULT_MENU_CLASS_", Definer::$config['menu']['default']['class']);
	$df_ctrl = 'Auwa';
	$df_theme = false;
	$http_host = '';
	if(isset(Definer::$config['controller']) && !empty(Definer::$config['controller']) && Definer::$config['controller']!==false){
		foreach (Definer::$config['controller'] as $key=>$ctrl) {
			$search = '/^'.str_replace('www.','',$_SERVER['HTTP_HOST']).'\//';
			$attempt = str_replace('www.','',$key);
			if (preg_match($search, str_replace($_SERVER['REQUEST_SCHEME'], '', $attempt))){
				$http_host = $key;
				$df_ctrl = $ctrl;
			}
		}
	}
	if(!isset($_POST['theme']) && isset(Definer::$config['defaultTheme']) && !empty(Definer::$config['defaultTheme']) && Definer::$config['defaultTheme']!==false){
		//echo str_replace('www.','',$_SERVER['HTTP_HOST']).'\//'; die();
		foreach (Definer::$config['defaultTheme'] as $key=>$theme) {
			//echo "=> ".str_replace('www.','',$key);
			$search = '/^'.str_replace('www.','',$_SERVER['HTTP_HOST']).'\//';
			$attempt = str_replace('www.','',$key);
			if (preg_match( $search, str_replace($_SERVER['REQUEST_SCHEME'], '', $attempt) )){
				$http_host = $key;
				$df_theme = $theme;
			}
		}
	}
	define("_HOST_", $http_host);
	define("_URL_", $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]);
	define("_DEFAULT_CTRL_", $df_ctrl);
	if ($df_theme) $_GET['theme']= $df_theme;
	/*
	 * Use file Cache 
	 */
	$useCache = false;
	if(isset(Definer::$config['useCache']))
		if (!empty(Definer::$config['useCache']) ) 
				$useCache = Definer::$config['useCache'];
	define("_USE_CACHE_", $useCache);

	/*
	 * Use the minimizer for  CSS 
	 */
	$minimizeCode = false;
	if(isset(Definer::$config['minimizeCode']))
		if (!empty(Definer::$config['minimizeCode']) && Definer::$config['minimizeCode']!==false) 
				$minimizeCode = Definer::$config['minimizeCode'];
	define("_MINZ_CODE_", $minimizeCode);

	/*
	 * Url
	 */
	$url = $_SERVER['HTTP_HOST'].'/'.Definer::$config['localDir'];
	$url =  preg_replace('/\/\/$/','/',$url);
	define('_BASE_URL_', $url);
	define('_PROTOCOL_', \Definer::isHttps() ? 'https://':'http://');
	define('_IMG_URL_', 	'img/');
	define('_JS_URL_', 		'js/');
	define('_CSS_URL_', 	'css/');
	define('_DATA_URL_', 	'data/');
	define('_FW_URL_', 		'frameworks/');

	// Session name
	define("_SESSION_NAME_", (defined('_SESSION_ID_') ? _SESSION_ID_: \Definer::$config['sessionID']));

	if (_DEVMODE_){
		error_reporting(E_ERROR);
		ini_set('display_errors', 'on');
	} else
		ini_set('display_errors', 0);
		
	if (_LOGS_){
		ini_set('log_errors', 1);
		ini_set('error_log', _CORE_DIR_.'logs/'.date('d-m-Y__H:i').'.txt');		// save log into a file
	} else 
		ini_set('log_errors', 0);


?>
