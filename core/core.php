<?php
/**
 * CORE OF AUWA
 *
 * Load all required classes and controllers
 *
 * @package Auwa \core\classes\
 * @copyright 2015 AuroraN
 */
namespace Auwa;

// WARNING :  session.auto_start must be set to Off

// ▼ Session Start
session_start();
ini_set('AddDefaultCharset', 'checkControllerLaunch');

// ▼ Core Path
$corePath = dirname(__FILE__).'/';

date_default_timezone_set('Europe/Paris');
// ▼ Settings File
require_once($corePath."settings.inc.php");

// ▼ Hard maintenance
if (\Definer::$config['maintenance']>1){
	include(_TPL_DIR_.'maintenance.tpl');
	die();
};
// ▼ Time Zone
date_default_timezone_set(\Definer::$config['timezone']);

// reinitialize session
//$_SESSION[_SESSION_NAME_]=null;

// ▼ Classes
require(_CORE_CLASSES_DIR_.'auwa.php');
Auwa::loadPhp( '
	error,
	tools');
// ▼ Soft maintenance
if (\Definer::$config['maintenance']==1 && (!isset($is_core) || !$is_core)){
	$defaultSessionName = \Definer::$config['sessionID'];
	$u = Tools::uncryptDatas( $_SESSION[_SESSION_NAME_]['data']['coreAccess'] );
	if ($u!=md5(fileatime(_CORE_DIR_.'core.php'))){
		$headtitle = \Definer::$infos['Infos']['Title']['default'][_DEFAULT_LANG_];
		$title = \Definer::$infos['Infos']['Title']['default'][_DEFAULT_LANG_];
		include(_TPL_DIR_.'maintenance.tpl');
		die();
	}
}
Auwa::loadPhp( '
	session,
	database,
	hook,
	template,
	module,
	controller,
	appController,
	checking,
	defaultmodel,
	yamlmodel,
	lang,
	user,
	page,
	menu,
	link,
	rewriting', 
	'coreClass', true );

// ▼ Auwa Controllers, Base
Auwa::loadPhp( 'Auwa', 'controller', true);
Auwa::loadPhp( 'AuwaApp', 'controller', true);
// URL rewriting
if(!isset($no_rw)){
	$url =  (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI']: null;
	($url) ? Rewriting::parse( $url ) : null;
	\Definer::setAfterRewritting();
	if (!isset($is_core)) Tools::retrievePostData();
}
Auwa::$requestMode = empty($_POST) ? 'GET' : 'POST';
Session::get()->AuwaVersion = "v1-beta.2";
// set settings for templates
Template::$compilationMode = (int)\Definer::$config['recompileTpl'];
Template::$expirationTime = (int)\Definer::$config['tplExpiration'];
Template::$cacheDirectory = isset($is_core) ? _CORE_DIR_."cache/" : _CACHE_DIR_.'views/';

/*===============================================
	Initialize databases
===============================================*/

$dbMethods = explode(',', str_replace(' ','',_DB_METHODS_));
$dbList = array();
foreach($dbMethods as $method)
	Db::$dbList[$method] = Db::init( $method );

Session::get()->_set('core', 'Db', array());

/*===============================================
	Db Driver Overrides
===============================================*/
if (isset($mainConfig['objectDbDriver']) && is_array($mainConfig['objectDbDriver']))
	foreach($mainConfig['objectDbDriver'] as $key=>$value)
		DefaultModel::_setDb($value, 'Auwa\\'.$key);

/*===============================================
	Initialize module gestion
===============================================*/

if (!Session::get()->modulesLoaded){
	Session::get()->modulesLoaded=array();
}

endCore:
Hook::init();