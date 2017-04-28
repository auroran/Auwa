<?php
namespace Auwa;
/**
 * Languages Management
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */

/**
 * Language Object
 *
 * Give methods for languages use and management
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */ 
class Lang extends YamlModel{
	
	/*
	 * Lang ID
	 * @var int
	 */
	public $id_lang;

	/*
	 * ISO Code
	 * @var string
	 */

	public $iso_code;
	/*
	 * ISO
	 * @var string
	 */
	public $iso;
	
	/* 
	 * User Password
	 * @var string
	 */
	protected $name;
	
	/*
	 * Define if the lang is enabled or not
	 * @var boolean
	 */
	protected $enable=true;
	
	
	protected $file = 'lang';

	/*
	 * DB fiedls definition
	 * @var array
	 */
	protected $primary = 'iso';
	protected $objSchema = array(	
									'name'		=> 'String', 
									'locale'	=> 'String',
									'flag'		=> 'String',
									'date'		=> 'String',
									'dateformat'	=> 'String',
									'enable'	=> 'boolean'
							);	
	
	
	/**
	 * Get a lang object by its ISO
	 *
	 * @param 	string 	$iso 	ISO of the lang
	 * @return 	array 			Content of the requested object
	 */
	public static function get($iso){
		$o = new Lang($iso);
		return $o->toArray();
	}

 	/**
 	 * Get all enabled languages
 	 *
 	 * @return 	array 		list of all language
 	 */
	public static function getEnabledLanguages(){
		$l= (self::$_instance);
		if (_MULTI_LANG_)
			return $l->getBy('enable', true);
		$r = new Lang(_CURRENT_LANG_);
		return ($r->enable) ? array(_CURRENT_LANG_=>$r->toArray()) : array();
	}

	/**
	 * Get a lang object by its ISO
	 *
	 * @param 	string 	$iso 	ISO of the lang
	 * @return 	array 			Content of the requested object
	 *
	 * Alias of get with a different method
	 */
	public static function getByIso($iso){
		$l= (self::$_instance);
		return $l->getBy('iso', $iso);
	}

	/**
	 * Get the id of a lang by its ISO
	 *
	 * @param 	string 	$iso 	ISO of the lang
	 * @return 	string 			ID field of the entry
	 *
	 */
	public static function getIdByIso($iso){
		$l = self::get($iso);
		return $l['id_lang'];
	}

	/**
	 * Get the ISO Code of a lang by its ISO
	 *
	 * @param 	string 	$iso 	ISO of the lang
	 * @return 	string 			IISO Code field of the entry
	 *
	 */
	public static function getIsoCode($iso){
		$o = new Lang($iso);
		$e = explode('.', $o->locale);
		return $e[0];
	}
}
Lang::$_instance = new Lang();


/**
 * Translation object
 *
 * Give methods for translation
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */ 
class Translation extends YamlModel{
	public $category;
	public $controller;
	public $contents = array();
	protected static $_translations;
	private static $_forceCtrl=false;
	protected $file = 'translations';
	protected $primary = 'category';
	protected $objSchema = array(	
								'controller' => 'String',
								'contents'	=> 'Array',
						);	

	public static function getTranslation($txt, $c, $iso_lang=_CURRENT_LANG_, $ctrl = _CURRENT_CTRL_){
		if (self::$_forceCtrl && $ctrl!==false) $ctrl = self::$_forceCtrl;
		$t = new Translation( ($ctrl ? 'Z_'.$ctrl : '').$c );
		$t->controller = $ctrl;
		$var = md5($txt);
		if ( !isset( $t->contents[$var]) ){
			$t->contents[$var] = array();
			foreach (Lang::getEnabledLanguages() as $iso => $l) {
				$t->contents[$var][$iso] = $txt;
			}
			$t->update();
		} elseif ( !isset( $t->contents[$var][$iso_lang]) ){
			$t->contents[$var][$iso_lang] = $txt;
			$t->update();
		}
		return $t->contents[$var][$iso_lang];
	}

	private static function fileTranslations( $file ){
		$fcontents = file_get_contents($file);
		$e = array(
			array( '/\$this->t\(([^)]+)\'\)/', true ),
			array( '/\$this->tfa\(([^)]+)\'\)/', false ),
			array( '#Tools\:\:translateForAll\(([^)]+)\'\)#', false ),
			array( '#Tools\:\:translate\(([^)]+\'\)\;#', true),
			array( '#{t:([^\}]+)}#', true ),
			array( '#{T:([^\}]+)}#',  false ),
		);
		foreach ($e as $key => $exp) {
			preg_match_all($exp[0], $fcontents, $at);
			foreach ($at[0] as $t) {
				if (empty($t)) continue;
				$v = explode(',', $t);
				$txt = str_replace( array('{t:','{T:', 'Tools::translate(','Tools::translateForAll(','$this->tfa(', '$this->t(', '"'), '', trim($v[0]));
				$txt = preg_replace( array('/^\'/','/\'$/'), '', trim($txt));
				$cat = str_replace( array('}',');', ')',"'"), '', trim($v[1]));
				self::getTranslation($txt, $cat, _CURRENT_LANG_, $exp[1] ? self::$_forceCtrl  : false);
			}			
		}
	}

	private static function applyToDir($path){
		foreach ( scandir($path) as $key => $tpl ) {
			if ($tpl!='.' && $tpl!='..' && is_dir($path.$tpl)) {
				self::applyToDir($path.$tpl.'/');
			}
			if (!preg_match('/\.tpl$/', $tpl)) continue;
			self::fileTranslations( $path.$tpl);
		}
	}

	
	public static function checkTranslations(){
		$_old = \ConfigFile::getConfig('config/translations'); // save old translations
		\ConfigFile::setConfig('config/translations', array(), true); // make new one

		self::fileTranslations( _ROOT_DIR_.'core/classes/error.php' );
		foreach (AuwaController::getCtrls() as $key => $ctrl) {
			self::$_forceCtrl = $ctrl;
			self::fileTranslations( _ROOT_DIR_.'controllers/'.$ctrl.'.php' );
			//templates  checking
			self::applyToDir( _TPL_DIR_ );
			//theme templates checking
			self::applyToDir( _THEME_DIR_.'templates/'  );
			//modules templates checking
			$modules = \ConfigFile::getConfig('config/modules');
			foreach ($modules['autoLoad'] as $name => $mctrl) {
				if ($mctrl==$ctrl || $mctrl=='all') self::applyToDir( _MOD_DIR_.$name.'/views/'  );
			}
		}
		self::$_forceCtrl = false;
		// purge translation about non-used vars
		$_new =  \ConfigFile::getConfig('config/translations');
		foreach ($_new as $primary => $obj) {
			foreach ($_new[$primary]['contents'] as $var => $array) {
				foreach ($_new[$primary]['contents'][$var] as $iso => $translation) {
					if ( isset($_old[$primary]['contents'][$var][$iso]) ) { // si cette langue n'est pas trouvée dans le nouveau
						$_new[$primary]['contents'][$var][$iso] = $_old[$primary]['contents'][$var][$iso];
					}
				}
			}
		}
		\ConfigFile::setConfig('config/translations', $_new, true); // save new one
	}
}
?>