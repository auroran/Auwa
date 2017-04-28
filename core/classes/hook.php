<?php
namespace Auwa;
/**
 * Define how Midde Way use Hook
 *
 * @package Auwa \core\classes\
 * @copyright 2015 AuroraN
 */
 
/**
 * Hook Static Tools Methods
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */ 
class Hook{

	/**
	 * Initialize Hook variable into Session
	 */
	public static function init(){
		if (  !Session::get()->keyExists('hooks')  ) 
			Session::get()->hooks = array();
	}

	/**
	 * Execute a Hook module methods
	 *
	 * @param	string		$name		Name of the methods (without 'hook_')
	 * @param	string		$params		Parameter send to this method
	 *
	 * @return 	boolean					True if one (or more) methods are executed, else False
	 */
	public static function exec( $name, $params=null ){
		$modules = self::getHook($name);
		$r = false;
		foreach($modules as $m){
			$r = true;
			$cn = $m.'Module';
			$fn = 'hook_'.$name;
			$m =  Module::load($m, 'ghost:true');
			if ( Check::isError($m) or $m -> $fn($params)===false) $r = false;
		}
		return $r;
	}

	/**
	 * Check if an hook has been registered by one or many modules
	 *
	 * @param 	string		$name		Name of the hook
	 *
	 * @return	boolean					True if hook is registered
	 */
	public static function hookExists($name){
		$name=trim($name);
		$h = Session::get()->hooks;
		$r = isset( $h[$name] ); 
		if ($r){
			$h = $h[$name];
			if ( !is_array($h) ){
				unset($h[$name]);
				Session::get()->hook = $h;
				return false;
			}
		}
		return $r;
	}
	
	/**
	 * Check if a module has registered this hook
	 *
	 * @param 	string		$name		Name of the hook
	 * @param	string		$module		Name of the module
	 *
	 * @rturn 	boolean					Result of check
	 */
	public static function isOnHook($name, $module){
		$r = self::createHook( $name );
		$h = Session::get()->hooks;
		return in_array($module, $h[$name]);
	}
	
	/** Create a hook entry on session variable
	 *  	= First registration of this hook
	 *
	 * @param	string		$name		Name of the new hook
	 */
	public static function createHook( $name ){
		if ( self::hookExists($name) ) return;
		Session::get()->_set('hooks', $name, array() );
	}
	
	/** Get the hook entry from session variable
	 *
	 * @param 	string		$name		Name of the hook
	 *
	 * @return	array					Array of each module which are registered this hook
	 */
	public static function getHook( $name ){
		if ( self::hookExists($name) ) return Session::get()->hooks[$name];
		return array();
		
	}
	
	/** 
	 *Registered a hook for a module
	 *
	 * @param	string		$name		Name of the hook
	 * @param	string		$m			Name of the module
	 *
	 */ 
	public static function registerHook( $name, $m=null ){
		if ( isset($m) ) {
			$r = self::isOnHook($name, $m);
			if (!$r) {
				$h = Session::get()->hooks;
				$h[$name][] = $m;
				$h = Session::get()->_set('hooks', $name, $h[$name] );
			}
		} else 
			self::createHook( $name );

	}
}
?>