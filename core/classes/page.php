<?php
namespace Auwa;
/**
 * Page Management
 *
 * @package Auwa \classes\
 * @copyright 2017 AuroraN
 */

/**
 * Page Object
 *
 * This object extends DefaultModel Class
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */ 
class Page extends DefaultModel{

	/*
	 * Page ID
	 * @var int
	 */
	public $id_page;

	/*
	 * Page type ID
	 * @var int
	 */
	public $id_type=0;

	/* 
	 * Custrom Css file for this page
	 * @var string
	 */
	public $css;
	
	/* 
	 * Custrom Js file for this page
	 * @var string
	 */
	public $js;

	/*
	 * Define if page is enabled or not
	 * @var boolean
	 */ 
	public $enable;
	
	/*
	 * This object is retrieved with it language contents
	 * @var boolean
	 */ 
	public $useContents=true;

	/*
	 * Primary field name
	 * @var string
	 */
	protected $dbPrimary = 'id_page';

	/*
	 * DB fiedls definition
	 * @var array
	 */
	protected $dbSchema = array(	'id_page'		=> 'SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
									'id_type'		=> 'SMALLINT',
									'css'			=> 'VARCHAR(255)',
									'js'			=> 'VARCHAR(255)',
									'enable'		=> 'TINYINT', 
									'insert_date'	=> 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP', 
									'update_date'	=> 'DATETIME NULL DEFAULT NULL',
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

	public function initObject($id=false){
		if (!$this->id_page) return;
		$this->retrieveContents();
	}
	
    public function retrieveContents(){
    	// initialize all language fields
    	foreach (Lang::getEnabledLanguages() as $key=>$lang) {
    		$this->contents[ $key ] = (array) new PageContent();
    	}
    	PageContent::getContentsByIdPage( $this->id_page, $this->contents );
    }

    public function getContents(){
    	return $this->contents;
    }
    public function getContentByIsoLang($iso_lang){
    	return (isset($this->contents[$iso_lang])) ? $this->contents[$iso_lang]['html'] : null;
    }
	
    public function update(){
    	$this->update_date = time();
        $res1 = parent::update();

    	return $res1;
    }

	public static function create($value=null){
		return parent::create($value, __CLASS__);
	}
	
	/**
	 * Change Database Driver for this object
	 *
	 * @param	string	$driver		Driver to use
	 */
	public static function setDb($driver){
		self::$dbForcedDriver = $driver;
	}


    public static function getAllPages(){
    	return static::getAll();
    }

    public static function getPageContents( $id_page ){
    	$page = new Page( $id_page );
    	return $page->getContents();
    }
    public static function getPageContentByIsoLang( $id_page, $iso_lang=_CURRENT_LANG_, $force=false){
    	$page = new Page( $id_page );
    	if (!$force && !$page->enable) return self::getPageContentByRewrite( 'wip', true );
    	return $page->getContentByIsoLang( $iso_lang);
    }

    public static function getTypeName($id_type){
    	switch ($id_type) {
    		case 2:
    			return 'Élément';
    			break;
    		case 1:
    			return 'Page Accueil';
    			break;
    		
    		default:
    			return 'Page';
    			break;
    	}
    }
    public static function getHomePage($iso_lang=_CURRENT_LANG_, $ctrl=_CURRENT_CTRL_){
		$search = array( 
			'id_type'=>1,
			'controller'=>array(
					'val'=>$ctrl,
					'table'=>'pagecontent'
				),
		);
		$rows = self::getCollection($search);
		if (isset($rows[0])) return $rows[0];
		return false;
    }
    public static function getHomeRewrite($iso_lang=_CURRENT_LANG_, $ctrl=_CURRENT_CTRL_){
		$search = array( 
			'id_type'=>1,
			'controller'=>array(
					'val'=>$ctrl,
					'table'=>'pagecontent'
				),
		);
		$rows = self::getCollection($search, false, 1);
		if (isset($rows[0])) return $rows[0]->contents[$iso_lang]['rewrite'];
		return false;
    }
    public static function getPageRewrite($rewrite, $iso_lang=_CURRENT_LANG_, $ctrl=_CURRENT_CTRL_){
    	$p = self::getByRewrite( $rewrite, $ctrl );
		if ($p) return $p->contents[$iso_lang]['rewrite'];
		return false;
    }
     public static function getLangFromRewrite($rewrite, $ctrl=_CURRENT_CTRL_){
    	$r = PageContent::getByRewrite($rewrite, $ctrl, 1);
    	if ($r && isset($r['iso_lang'])) return $r['iso_lang'];
    	return _CURRENT_LANG_;
    }

    public static function getPageContentByRewrite( $rewrite, $force=false, $iso_lang=_CURRENT_LANG_, $ctrl=_CURRENT_CTRL_ ){
    	$p = self::getByRewrite( $rewrite, $ctrl );
    	if ($p===false) return false;
    	if (!$p->enable && !$force) {
    		$p =  self::getByRewrite( 'wip', $ctrl);
    	}
    	if (!$p) return false;

		$t = new Template($rewrite);
		$t->type="page";
		$t->fill($p->contents[$iso_lang]['html'], $p->update_date);
		return $t->render();
    }

    public static function getByRewrite( $rewrite, $ctrl=false, $iso_lang=_CURRENT_LANG_){
    	$search = array(
			'rewrite'=>array(
					'val'=>$rewrite,
					'table'=>'pagecontent'
				),
		);
		if ($ctrl)
			$search['controller'] = array(
					'val'=>$ctrl,
					'table'=>'pagecontent'
				);
		$rows = self::getCollection($search);
		if ($rows[0]) return $rows[0];
		return false;
    }

}


class PageContent extends DefaultModel{
	public $id_content;
	public $id_page;
	public $iso_lang;
	public $rewrite;
	public $controller = _DEFAULT_CTRL_;
	public $title;
	public $description;
	public $html ="";
	
	/*
	 * Primary field name
	 * @var string
	 */
	protected $dbPrimary = 'id_content';
	public static $_instance;
	protected static $class = __CLASS__;
	/*
	 * DB fiedls definition
	 * @var array
	 */
	protected $dbSchema = array(	'id_content'	=> 'SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
									'id_page'		=> 'SMALLINT UNSIGNED',
									'iso_lang'		=> 'VARCHAR (128)',
									'html'			=> 'LONGTEXT',
									'title'			=> 'VARCHAR (128)',
									'rewrite'		=> 'VARCHAR (128)',
									'controller'	=> 'VARCHAR (128)',
									'description'	=> 'TEXT', 
							);	
	/*
	 * Database driver
	 * @var string
	 */
	protected $dbDriver = "MySQL";

	public function set($id_page, $iso_lang, $title="", $rewrite="", $controller=_DEFAULT_CTRL_, $description="", $html=false){
		$this->id_page = $id_page;
		$this->iso_lang = $iso_lang;
		$this->title = $title;
		$this->rewrite = $rewrite;
		$this->controller = $controller;
		$this->description = $description;
		if($html!==false) $this->html = $html;
	}
	public static function getContentsByIdPage( $id_page, &$content_array=array()) {
		$rows = self::getAll( array( 'id_page'=>(int)$id_page ) );
    	foreach ($rows as $key => $row) {
    		$content_array[ $row['iso_lang'] ] = $row;
    	}
    	return $content_array;
	}

	public static function getByRewrite( $rewrite, $ctrl=false, $limit=false ){
		$search = array('rewrite'=>$rewrite);
		if ($ctrl) $search['controller'] = $ctrl;
		$rows = self::getAll( $search, false, $limit );
		if (empty($rows)) return false;
		return $rows;
	}
	public static function getRealRewrite( $id_page, $iso_lang ){
		$contents = array();
		self::getContentsByIdPage($id_page, $contents);
		if (!isset($contents[$iso_lang])) return false;
		return $contents[$iso_lang]['rewrite'];
	}


}
?>