<?php
namespace Auwa;
/**
 * Auwa Admin Controller 
 *
 * Auwa use this AuwaController to build the code administration
 *
 * @package Auwa \controllers\
 * @copyright 2015 AuroraN
 */

 
/**
 * Controller for Auwa Administration
 *
 * This Auwa Controller extends CoreController
 * This Class is final, override is impossible
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class AuwaAppController extends AppController{
	/**
	 * Set default value to cache use
	 * @var boolean
	 */
	protected $useCache = _USE_CACHE_;
	
	/**
	 * Set default value to Css minimizer use
	 * @var boolean
	 */
	protected $minimizeCode = _MINZ_CODE_;

	/* upload settings */
	static $fileTypesAllowed = array('jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff');

	/*
	 * True : Use the full app UI = uiNodes can not hover the header
	 */
	public $fullApp = true;
	public $fullHeader = true;

	public $includeHeaderFile = false;
	/**
	 * Set Css files to use
	 *
	 * Theses files are placed after main Css files
	 * This function reads CoreController::$cssFiles array, witch can be filled by module controllers
	 *
	 * @return 	string		$_content		Head Block part about CSS (<link rel="stylesheet"> tags)
	 */
	public function _setCss(){	
		$this->setCss();
		ob_start();	
		$this->addCssBefore('coreUI', _CORE_CSS_DIR_);
		$this->addCssBefore('jquery-ui', _CORE_CSS_DIR_);
		$this->addCss('eff.min', _FW_DIR_.'eff/css/');

		foreach( AppController::$cssFiles as $file)
			Auwa::insert_css( $file );
		Auwa::insert_css_cache();
		$_content = ob_get_contents();
		ob_end_clean();
		return $_content;
	}
	
	/**
	 * Set main Css files to use
	 *
	 * Theses files are in first positions
	 */
	public function setCss(){
	}
	
	/**
	 * Set main Js files to use
	 *
	 * Theses files are in first positions
	 */
	public function setJs(){
	}

	/**
	 * Set Js files to use
	 *
	 * Theses files are placed after main Js files
	 * This function reads CoreController::$jsFiles array, which can be filled by module controllers
	 *
	 * @return 	string		$_content		Footer Block part about JS (<script type"text/javascript"> tags)
	 */
	public function _setJs(){
		$this->setJs();
		ob_start();
		echo PHP_EOL;
		$this->addJs('coreUI', _CORE_JS_DIR_, true);
		
		foreach (\Definer::$config['jsLibrary']['plugins'] as $p) {
			$this->addJs($p, _JS_DIR_, true );
		}
		$this->addJs(\Definer::$config['jsLibrary']['core'], _JS_DIR_, true );
		Auwa::insert_js_cache();
		$this->setJsVar('jsFiles', AppController::$jsFiles);
		if (!empty($this->auwa->jsVars)){
			$this->setVar('_auwaJsVars', json_encode($this->auwa->jsVars));
		}
		$_content = ob_get_contents();
		ob_end_clean();
		return $_content;
	}
	
	/**
	 * Controller Initilization
	 *
	 * Set and get all part of the page to displaying
	 *
	 * Set many variables for use into templates.
	 *
	 * This methods build all menu and work spaces
	 *
	 */
	public function init(){
			
		// disable cache if a form is submitted or if the user is an admin
		if (Tools::getValues("post") || User::isCoreUser()) $this->useCache=false;
		/*if ($this->useCache){
			$cache_id= _CURRENT_CTRL_;
			$var_id = _CURRENT_LANG_."_".implode($_GET['urlPart'],'_');
			if (!is_dir(_CACHE_DIR_."$cache_id")) mkdir(_CACHE_DIR_."$cache_id");
			$cache = _CACHE_DIR_."$cache_id/$var_id.html";
			$expire = time() -60 ; // valable 1min
			if( file_exists($cache) && filemtime($cache) > $expire ){
				echo "<!--cache file | status $expire / ".filemtime($cache)." -->".PHP_EOL;
				readfile($cache);
				return;
			} 
		}*/

		if (func_num_args()>0) $options = func_get_arg(0);
		// Auwa Admin Controller use admin root template directory
		$ctrl_e = new Error;
		
		$siteInfos = Auwa::get_siteInfos();
		$currentSelectedController = $this->session->currentSelectedController;
		$languages = array();
		foreach (Lang::getEnabledLanguages() as $iso=>$lang) {
			$languages[$iso] = $lang;
		}
		if (!defined('_CURRENT_LANG_')) define('_CURRENT_LANG_', $lang);

		$this->setVar( array(
            'module'=>          Tools::getValue('module','post'),
            'controller'=>     Tools::getValue('controller','post') ? Tools::getValue('controller','post') : Tools::getValue('AuwaController'),
        ) );

		// Tpl vars
		$this->setVar( array(
			'current_lang'				=> _CURRENT_LANG_,
			'current_iso_code'=>		Lang::getIsoCode( _CURRENT_LANG_ ),
			'current_ctrl'=> 			_CURRENT_CTRL_,
			'current_date_display'=> 	_DATE_DISPLAY_,
			'current_date_format'=> 	_DATE_FORMAT_,
			'queryCore'					=> Auwa::$base.'query.php',
			'version'					=> Session::get()->AuwaVersion,
			'languages'					=> $languages,
			'Link'=>					Link::$_instance,
			'infos'=> 					\Definer::$infos['Infos'],
			'_year'=> 					date('Y'),
		));

		// temp, ACUI is not multi-language capable yet
		//$this->setVar('current_lang', 'fr');

		ob_start();
		$_content = "";
		Module::autoLoad();
		$_content = ob_get_contents();
		ob_end_clean();// Content load

		if (Auwa::$requestMode == 'POST'){
			ob_start();
			$mod = ($this->getVar('module')) 
					? Auwa::sysprobe( $this->getVar('module') ) 
					: false;
			if (!$mod && $this->getVar('controller')){
				$c = AppController::call( $this->getVar('controller') );
				$c->displayErrors();
			}
			$_content = ob_get_contents();
			ob_end_clean();// Content load
			if ( Tools::getValue('query', 'post')){
				$_content = '';
				$this->_query(false);
			} else 
				exit ('Missing method');
			exit();
		} elseif ( $this->getVar('controller') && ! in_array($this->getVar('controller'), array('AppLogin', Tools::getValue('AuwaController')) ) )  {
				exit ('Can not be called in GET request');
		}
		ob_start();
		$this->main();
		$_content .= ob_get_contents();
		ob_end_clean();// Content load

		
		// Default Content load
		if (empty($_content)){
			ob_start();	
			$_e = $this->includePage();	
			if (Check::isError($_e)) $_e->displayErrors();	
			$_content = ob_get_contents();
			ob_end_clean();
		}

		$metaTitle = Tools::getValue('Auwa_title_replacement')  
					? Tools::getValue('Auwa_title_replacement') 
					: $this->session->headTitle;

		// read custom parameter
		$vars = array();
		preg_match_all('/{Set\:([^}]+)}/', $_content, $vars);
		if (count($vars)>1){
			foreach($vars[1] as $rule){; 
				$params = explode('=',trim($rule));
				$vname = 'meta'.trim($params[0]);
				$$vname = trim($params[1]) ;
			}
		}	
		
		$this->setVar(array(
			'metaTitle'=> 			_HEAD_TITLE_BASE_.$metaTitle,
			'metaDescription'=> 	isset($metaDescription)?$metaDescription:null ,
			'metaImage'=> 			isset($metaImage)?$metaImage:null 
		));
		if (isset($metaCss)) {
			$metaCss = preg_replace('/\\t| |'.PHP_EOL.'/','', $metaCss);
			$metaCss = explode(',',$metaCss);
			foreach($metaCss as $cssInser){
				$css = explode('/',$cssInser);
				$path = null;
				if(count($css)>1) {
					$filename =$css[count($css)-1];
					$path = preg_replace('/'.$filename.'$|\/\//', '', $cssInser);
					//$path = preg_replace('/\/\//', '/', $path);
					$filename =preg_replace('/\.css/','',$filename);
				}
				$filename = explode("@", $filename);
				$media = (isset($filename[1])) ? $filename[1] : false;
				$this->addCss($filename[0],_DATA_DIR_.'css/'.$path, $media);
			}
				
		}
		if (isset($metaJs)) {
			$metaJs = preg_replace('/\\t| |'.PHP_EOL.'/','', $metaJs);
			$metaJs = explode(',',$metaJs);
			foreach($metaJs as $jsFile){
				$filename =preg_replace('/\.js/','',$jsFile);
				$this->addJs($filename,_DATA_DIR_.'js/');
			}
				
		}

		// erase all 'Set' variable in template or page
		$_content = preg_replace('/{Set\:([^}]+)}/','',$_content);
				

		$patterns = array(
			'/{url}/',						// Display base URL
			'/{url:img}/',
			'/{url:pictures}/',
			'/{url:data}/',
			'/{url:js}/',
			'/{url:css}/',
		);
		$replaces = array(
			Auwa::$base,
			Auwa::$base._IMG_THEME_URL_.'img/',
			Auwa::$base._DATA_URL_.'pictures/',
			Auwa::$base._DATA_URL_,
			Auwa::$base._JS_URL_,
			Auwa::$base._CSS_URL_,
		);

		foreach($patterns as $key=>$pattern)
			$_content = preg_replace($pattern,$replaces[$key],$_content);
		
			// Js vars
			$this->setJsVar(array(
				'queryCore'			=> Auwa::$base.'query.php',
				'appController'		=> Tools::getValue('AuwaController'),
				'current_lang'		=> $lang,
				'jquery_dateformat'	=> Tools::getJqueryDateFormat(),
				'action'			=> Tools::getValue('action'),
				'fileTypesAllowed'	=> self::$fileTypesAllowed,
			));
			//Tpl vars
			$this->setVar(array(
				'main_content'		=> $_content,
				'tpl_head_content'	=> $this->_setCss(),
				'tpl_footer_content'=> $this->_setJs(),
				'tpl_errors'		=> $ctrl_e,
				'fullHeader'		=> $this->fullHeader,
				'fullApp'			=>$this->fullApp,
				'defaultTemplatePath'=> self::$defaultTemplatePath,
			));
			
			ob_start();
			$this->displayHeader($this->includeHeaderFile);
			$_header = ob_get_contents();
			ob_end_clean();
			$this->setVar('displayPanel', $this->getTemplate('_panel', _CORE_DIR_.'views/'));
			$this->setVar('displayHeader', $_header);
			$this->setVar('tpl_body_content', $this->getTemplate('_body'));

			ob_start();
			$r =$this->includeTemplate( 
							"_html", 
							self::$defaultTemplatePath 
						);
			if (Check::isError($r)) $r->displayErrors();
				
			$cache_content = ob_get_contents();
			ob_end_clean();

			/*if ($this->minimizeCode){
				$cache_content = str_replace(PHP_EOL,'',$cache_content);
				$cache_content = str_replace("\t",'',$cache_content);
			}
			if($this->useCache) file_put_contents($cache, $cache_content) ; */
			echo $cache_content;
	}
	public function setInstance(&$o){
		self:: $_instance = $o;
	}
	public static function getInstance(){
		return self::$_instance;
	}

}
?>