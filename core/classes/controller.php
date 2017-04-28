<?php
namespace Auwa;
/**
 * Define the base of Controller Layer of Auwa
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */
 
/**
 * Define Controller core
 *
 * All controller types should extend this class
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class Controller{

	/*
	 * Controller owner
	 * @var string
	 */
	public $module = null;
	
	/*
	 * Controller name
	 * @var string
	 */
	public $name = null;
	
	/*
	 * Templates controller path
	 * @var string
	 */
	public $templatePath = null;
	
	/*
	 * Controller launch mode
	 * @var string
	 */
	public $mode = null;
	
	/*
	 * Controller type : default or system
	 * @var string
	 */
	public $ctrlType;
	
	/*
	 * Template instance
	 * @var Template
	 */
	public $template;
	
	/*
	 * Session instance
	 * @var Session
	 */
	public $session;

	/*
	 * Auwa instance
	 * @var auwa
	 */
	public $auwa;
	
	/*
	 * Errors occured
	 * @var Error
	 */
	public $errors;
	
	/*
	 * Array of Css files to use
	 * @var array
	 */
	public static $cssFiles = array();
	
	/*
	 * Array of Js files to use
	 * @var array
	 */
	public static $jsFiles = array();

	/*
	 * Cache for all js files
	 * @var string
	 */
	public static $jsCacheFile = "";

	/*
	 * Cache for all css files
	 * @var string
	 */
	public static $cssCacheFile = "";
	

	/**
	 * Instance of the controller
	 * @var boolean
	 */
	protected static $_instance;
	
	/**
	 * Create a instance of Controller
	 *
	 * @param	string		$mod		Referer module, in case of a module has launch this controller
	 * @param	string		$name		Name of this controller
	 * @param	string		$load		Launch mode : default, header or script
	 * @param	string		$ctrlType	Type of this controller : default(user) or system
	 */
	public function __construct($mod=null, $name=false, $load='default', $ctrlType='default'){
		$this->mode=$load;
		$this->errors=new Error();
		$this->module=$mod;
		$this->name=$name;
		$this->ctrlType = $ctrlType;
		$this->session = Session::get();
		$this->auwa = Auwa::get();
		if ( method_exists ( $this, 'loadCss') ) 	$this->loadCss();
		if ( method_exists ( $this, 'loadJs') ) 	$this->loadJs();
		
		if ($mod && $name) {
			switch ($load) {
				case 'header':
					if ( method_exists ( $this, 'loadCss') ) $this->loadCss();
					break;
				case 'script':
					if ( method_exists ( $this, 'loadJs') ) $this->loadJs();
					break;
			}
		}
		$this->setTemplatePath();
	}
	
	
	/**
	 * Set directory of all template use by this Controller
	 *
	 * Pages docuement are not subject to it
	 */
	public function setTemplatePath(){
		if ($this->module==null)
			$this->templatePath=_TPL_THEME_DIR_.$this->name.'/';
		else{
			$path = ($this->ctrlType=='system') ? _SYS_MOD_DIR_ : _MOD_DIR_;
			$this->templatePath=$path.$this->module.'/views/';
		}
		$this->template = new Template(false, $this->templatePath);
	}
		
	/** 
	 * Initilizer => Launch main methods only by default
	 *
	 * You can override this method to improve default work of your controller
	 */
	public function init(){
		$vars = (func_num_args()>0) ? func_get_arg(0) : null;
		$this->setJsVar('EOL', PHP_EOL);
		$this->main($vars);
	}
	
	/**
	 * Add a css file reference to Controller::$cssFiles
	 *
	 * @param	string		$file		Name of css file
	 * @param	string		$path		Path to this controller. if null, one of default paths
	 * @param	string		$media		Media Query property value of link tag
	 * @param	boolean		$unshift	PLace new items before others
	 *
	 * The reference is an array( url, absolute path, media query )
	 */
	public function addCss($file=null, $path=null, $media=false, $unshift=false){
		if ($file==null) $file=$this->name;
		if ($path!==null) $rel = $path;
		else {
			if( !empty($this->module)){
				if ($this->ctrlType=='default'){
					$rel = (is_file(_THEME_DIR_.'/modules/'.$this->module.'/css/'.$file.'.css')
						? _THEME_DIR_.'modules/'.$this->module.'/' : _MOD_DIR_.$this->module).'/css/';
				} else $rel = _SYS_MOD_DIR_.$this->module.'/css/';
			}
			else 
				$rel =  ($this->ctrlType=='system') ? _CORE_DIR_.'css/' : _CSS_THEME_DIR_;
		}
		Auwa::link_css_url($rel.$file, false, $media, $this->ctrlType=='system', $unshift);		
	}
	
	/**
	 * Add a css file reference to Controller::$cssFiles, before others
	 *
	 * @param	string		$file		Name of css file
	 * @param	string		$path		Path to this controller. if null, one of default paths
	 * @param	string		$media		Media Query property value of link tag
	 *
	 * The reference is an array( url, absolute path, media query )
	 */
	public function addCssBefore($file=null, $path=null, $media=false){
		$this->addCss($file, $path, $media, true);
	}
	
	/**
	 * Add a js file reference to Controller::$jsFiles
	 *
	 * @param	string		$file		Name of js file
	 * @param	string		$path		Path to this controller. if null, one of default paths
	 *
	 * The reference is an array( url )
	 */
	public function addJs($file=null, $path=null, $unshift=false){
		if ($file==null) $file=$this->name;
		if ($path) $rel = $path;
		else {
			if( !empty($this->module)){
				if ($this->ctrlType=='default'){
					$rel = (is_file(_THEME_DIR_.'/modules/'.$this->module.'/js/'.$file.'.js')
						? _THEME_DIR_.'modules/'.$this->module.'/' : _MOD_DIR_.$this->module).'/js/';
				} else $rel = 	_SYS_MOD_DIR_.$this->module.'/js/';
			}else 
				$rel =  ($this->ctrlType=='system') ? _CORE_DIR_.'js/' :_JS_THEME_DIR_ ;
		}
		Auwa::link_js_url($rel.$file, false, $unshift, $this->ctrlType!=='system');
	}
	
	/**
	 * Main method of a controller
	 *
	 * Do nothing by default, 
	 * override this method to define specefic work of your controller
	 */
	public function main()	{}
	
	/**
	 * Post Traitment of a controller (rewrite the code...)
	 *
	 * @param	string		$_contentCode		HTML code to use
	 * Do nothing by default, 
	 * override this method to define specefic work of your controller
	 */
	public function postTraitment(&$_contentCode)	{}
	
	/**
	 * Final Post Traitment of a controller (rewrite the entiere code...)
	 *
	 * @param	string		$_contentCode		HTML code to use
	 * Do nothing by default, 
	 * override this method to define specefic work of your controller
	 */
	public function finalTraitment(&$_contentCode)	{}


	/**
	 * Erase all variables used by templates into Auwa instance
	 *
	 * !! Use this methods can cause umprevisable effects !!
	 */	 
	public function resetVars(){
		$this->auwa->tplVars =array();
	}

	/**
	 * Set value of a template variable into Template instance
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
	 * Set value of a js variable into Auwa instance
	 *
	 * @param	string or array		$name		Name of the variable
	 * @param	mixed				$value		Value of the variable
	 */
	public function setJsVar($name, $value=null){
		if(!is_array($name))
			$name=array( $name=>$value );
		foreach ($name as $key => $value)
			$this->auwa->setJsVar($key, $value);
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
	 * Get a template content
	 *
	 * @param	string		$template		Name of the template file
	 * @param	string		$path			Path of the template
	 *
	 * @return	string		$tpl_content	Buffer of inclusion 
	 */
	public function getTemplate($template=null, $path=false){
		if ($template==null) $template = $this->name;
		if (!$this->template || !is_object($this->template) ) $this->template = new Template();
		$this->template->link($template, $path ? $path : $this->templatePath);
		return $this->template->render();
	}
	
	
	/**
	 * Display/Include a template content
	 *
	 * @param	string		$template		Name of the template file
	 * @param	string		$path			Path of the template
	 *
	 * @use		Tools::includeTpl			Get and rewrite template, and include this
	 *
	 * @return	Error
	 */
	public function includeTemplate($template=null, $path=false, $content=false){
		if ($template==null) $template = $this->name;
		if (!$this->template) $this->template = new Template();
		$this->template->link($template, $path ? $path : $this->templatePath);
		if ($content){
			$this->template->content = $content;
			$this->template->type = 'page';
		}
		return $this->template->display();
	}
	
	/**
	 * Include a Page file
	 *
	 * @param	string	$pageFile	Name of page to include
	 * @param	string	$ghost		Diseable redirections
	 * @param	string	$force		Force inclusion of the error page inclusion
	 *
	 * @use Tools::insertPage to insert and rewrite the page
	 *
	 * @return 	Error				Error Object (can be empty)
	 */
	public function includePage($pageName=false, $ghost=null, $force=false){
		$e = new Error();
		if (Tools::getValue("page")=='blank' && $pageName==false) return $e;
			 $page = $this->insertPage($pageName, $ghost, $force);
			 if ($page==false ){ 
				if ( (Tools::getValue("page") && Tools::getValue("page")!=='')  || $pageName!==false) {
					$e->addError( Error::getErrMsg('p00') );
					header("Status: Not Found", false, 404);
				}
			}
		return $e;
	}

	/**
	 * Insert the target Page file
	 *
	 * @param	string	$pageFile	Name of page to include
	 * @param	string	$ghost		Diseable redirections
	 * @param	string	$force		Force inclusion of the error page inclusion
	 *
	 * All TplVars from Session will be injected
	 *
	 * @return 	Error				Error Object (can be empty)
	 */
	public function insertPage($page=false, $ghost=false, $force=false){

		if (!$page) $page = Tools::getValue("page") ? Tools::getValue("page") : 'getHome' ;
		$iso_lang = _CURRENT_LANG_;
		if ($page=='getHome') {
			$p = Page::getHomePage($iso_lang, _CURRENT_CTRL_);
			$page = $p->contents[_CURRENT_LANG_]['rewrite'];
		}
		else $p = Page::getByRewrite( $page, _CURRENT_CTRL_ );
		if ( !is_object($p) || !$p->id_page ) {
			if (in_array($page, array('footer','header', 'menus', 'menu'))) return "";
			return ($page!='404') ? $this->insertPage('404') : false;
		}
		// redirect if the rewrite is not from the good language
			if (Auwa::$requestMode =='GET' && !$ghost && $p->contents[_CURRENT_LANG_]['rewrite']!= $page && $p->id_type<2 && !in_array($page, array('404','wip')) ){
			Tools::redirect( Link::$_instance->getLink( $p->contents[_CURRENT_LANG_]['rewrite']) );
		}
		$pageContent = null;
		if ($p->id_type <2 ){ 
			Auwa::$_instance->setVar('page_type', $p->id_type);
			Auwa::$_instance->setVar('canonical_url', Link::$_instance->getLink($p->contents[_DEFAULT_LANG_]['rewrite'], 'page', true, _CURRENT_CTRL_, _DEFAULT_LANG_, false));
			$alternate = array();
	    	foreach (Lang::getEnabledLanguages() as $iso=>$lang) {
	    		$l =explode('.',$lang['locale'] );
				$alternate[ $l[0] ] = Link::$_instance->getLink($p->contents[$iso]['rewrite'], 'page', true, _CURRENT_CTRL_, $iso, false);
			}
			Auwa::$_instance->setVar('alternate_url', $alternate);
			Auwa::$_instance->setVar('current_page', $page);
			if ($p->contents[$iso_lang]['title']) $pageContent .= "{Set:Title=".$p->contents[$iso_lang]['title'].'}'.PHP_EOL;
			if ($p->contents[$iso_lang]['description']) $pageContent .= "{Set:Description=".$p->contents[$iso_lang]['description'].'}'.PHP_EOL;
		}
		if ($p->css) $pageContent .= "{Set:Css=".$p->css.'}'.PHP_EOL;
		if ($p->js) $pageContent .= "{Set:Js=".$p->js.'}'.PHP_EOL;
		if (!$force && !(boolean)$p->enable && isset($p->id_page) || trim($p->contents[ $iso_lang ]['html'])=='') $pageContent = Page::getPageContentByRewrite( 'wip', true );
		else $pageContent .= $p->contents[ $iso_lang ]['html'];

		// include the page
		$t = new Template($page);
		$t->type="page";
		$t->fill($pageContent, $p->update_date);
		$t->display();
		return true;
	}

	/**
	 * Get the executed contents of a page
	 *
	 * @param	string	$pageFile	Name of page to include
	 * @param	string	$ghost		Diseable redirections
	 * @param	string	$force		Force inclusion of the error page inclusion
	 *
	 * @use Tools::rewriteTpl to cache no-php files
	 *
	 * All TplVars from Session will be injected
	 *
	 * @return 	Error				Error Object (can be empty)
	 */
	public static function getPage($page=false, $ghost=false, $force=false){
		$p = Page::getByRewrite($page, _CURRENT_CTRL_);
		$t = new Template($page);
		$t->type="page";
		$t->fill($p->contents[_CURRENT_LANG_]['html'], $p->update_date);
		$c = $t->render();
		// erase all 'Set' variable in template or page
		$c = preg_replace('/{Set\:([^}]+)}/','',$c);
		return $c;
	}
	
	/**
	 * Translate an expression for a category and the current controller
	 *
	 * @param 	string 	$expr 	Expression to translate
	 * @param 	string 	$cat 	Name of the category
	 *
	 */
	public function t($expr, $cat){
		return Tools::translate($expr,$cat);
	}

	/**
	 * Translate an expression for a category and all controllers
	 *
	 * @param 	string 	$expr 	Expression to translate
	 * @param 	string 	$cat 	Name of the category
	 *
	 */
	public function tfa($expr, $cat){
		return Tools::translateForAll($expr,$cat);
	}

	/**
	 * Set the controller in use
	 *
	 * @param 	string 	$name 	Controller name to use
	 * @param 	string 	$path 	Path of the controller file
	 *
	 */
	public function useCtrl($name, $path=_CTRL_DIR_){
		Controller::call($name, "default", $path, null, $this->templatePath);
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
	public static function call($name=null, $mode="default", $path=null, $mod=null, $templatePath=false){
		$e = new Error();
		if ($name ==null) $name = Tools::getValue('controller');
		if (!$name) return $e;
		if ($path==null) $path = _CTRL_DIR_;
		if (is_file( $path.$name.'.php') ){
			include_once($path.$name.'.php' );
			$className = ($mod!=null ? $mod.'Module' : 'Auwa\\').$name.'Controller';
			if ( class_exists($className) ){ 
				$i = false;
				$controller = new $className($mod, $name, $mode);
				if ($templatePath) $controller->templatePath = $templatePath;
				if ($mode=='default' ) {
					$controller->init();
				}
				if (is_array($mode) ) $controller->init($mode);
				$e->merge( $controller->errors );
			}
		}
		else $e->addError($name.' : Ce contrôleur n\'existe pas' );
		
		return $e;
	}
	
}
?>
