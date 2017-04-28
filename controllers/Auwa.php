<?php
namespace Auwa;
Error::report(3);
/**
 * Auwa Controller 
 *
 * Auwa use an object AuwaController to build a part of web site/app
 *
 * @package Auwa \controllers\
 * @copyright 2015 AuroraN
 */

 
/**
 * Default Controller for Auwa
 *
 * All AuwaController should extend this class
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class AuwaController extends AppController{

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
	/**
	 * Set directory of all templates used by this Controller
	 *
	 * @overide of Controller::setTemplatePath
	 *
	 * Pages docuement are not subject to it
	 */
	public function setTemplatePath(){
		$this->templatePath = _TPL_THEME_DIR_; 		// Auwa Controller use root template directory by default
	}
	
	/**
	 * Set main Css files to use
	 *
	 * Theses files are in first positions
	 */
	public function setCss(){
		$this->addCssBefore('default');
	}
	
	/**
	 * Set main Js files to use
	 *
	 * Theses files are in first positions
	 */
	public function setJs(){
	}
	
	/**
	 * Set Css files to use
	 *
	 * Theses files are placed after main Css files
	 * This function reads Controller::$cssFiles array, witch can be filled by any controllers
	 *
	 * This method is FINAL, overrive will be impossible !
	 *
	 * @return 	string		$_content		Head Block part about CSS (<link rel="stylesheet"> tags)
	 */
	final public function _setCss(){
		ob_start();		
		$this->setCss();
		$this->addCssBefore('eff.min', _FW_DIR_.'eff/css/');
		
		foreach( Controller::$cssFiles as $file)
			Auwa::insert_css( $file );
		
		Auwa::insert_css_cache();
		// $this->setCustomCss(2);
		$_content = ob_get_contents();
		ob_end_clean();
		return $_content;
	}
	
	/**
	 * Set Js files to use
	 *
	 * Theses files are placed after main Js files
	 * This function reads Controller::$jsFiles array, which can be filled by module controllers
	 *
	 * This method is FINAL, overrive will be impossible !
	 *
	 * @return 	string		$_content		Footer Block part about JS (<script type"text/javascript"> tags)
	 */
	final public function _setJs(){
		$this->setJs();
		ob_start();
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
		//var_dump($_content); die();
		return $_content;
	}

	/**
	 * Controller Initilization
	 *
	 * Set and get all part of the apge to displaying
	 *
	 * Set many variables for use into template.
	 *
	 * @param 	boolean		$ghost			Display or not init result
	 *
	 * @return	string		$cache_content	Display cache if param is true
	 *
	 * This method is the base work of all Auwa Controller. 
	 *		It will call AuwaController::main method which will be the body of controller.
	 *
	 * This method is FINAL, override is impossible !
	 *
	 */
	final public function init($ghost=false){

		if (_CURRENT_RULE_) Auwa::$base .= _CURRENT_RULE_.'/';
		$lang = Tools::getvalue('lang') ? Tools::getValue('lang') : _DEFAULT_LANG_;
		$i = unserialize(_INFOS_);
		$this->setVar(array(
			'base_url'=> 				Auwa::$base,
			'Link'=>					Link::$_instance,
			'page_type'=>				0,
			'current_page'=> 			Tools::getValue('page'),
			'current_ctrl'=> 			_CURRENT_CTRL_,
			'current_date_display'=> 	_DATE_DISPLAY_,
			'current_date_format'=> 	_DATE_FORMAT_,
			'current_lang'=> 			_CURRENT_LANG_,
			'current_iso_code'=>		Lang::getIsoCode( _CURRENT_LANG_ ),
			'form'=> 					'http://'._BASE_URL_.'query.php?cu='.$this->getVar('current_url'),
			'query'=> 					'http://'._BASE_URL_.'query.php?cu=',
			'enabled_langs'=> 			Lang::getEnabledLanguages(),
			'infos'=> 					$i,
			'_year'=> 					date('Y'),
		));
				
		// disable cache if a form is submitted or if the user is an admin
		if (Tools::getValues("post") || User::isCoreUser()) $this->useCache=false;
		if ($this->useCache){
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
		}

		ob_start();
		if (func_num_args()>0) $options = func_get_arg(0);
		$containerType = ( isset($options['container']) ) ? $options['container'] : 'container';
		$this->setVar('containerType', $containerType );
		
		$ctrl_e = new Error;
		
		// Modules load
		$_mod = "";
		ob_start();
		Module::autoLoad();
		if (Tools::getValue('module')) Module::load( Tools::getValue('module') );
		$_mod = ob_get_contents();
		ob_end_clean();
		if ( empty($_mod) && Tools::getValue('module')) Tools::setValue('page', '404');


		// Templates names
		$html_template = !isset($options['tpl_html']) ? "_html" : $options['tpl_html'];
		$body_template = !isset($options['tpl_body']) ? "_body"  : $options['tpl_body'];
		
		// Head Template variables
		$this->setVar(array(
			'Auwa_version'=> 	$this->session->AuwaVersion,
			'site_publisher'=>	$this->session->publisher,
			'site_title'=> 		$this->session->title,
			'tpl_path'=> 		isset($options['tpl_dir'])? $options['tpl_dir']:$this->templatePath 
		));
		
		$metaTitle = Tools::getValue('Auwa_title_replacement')  
					? Tools::getValue('Auwa_title_replacement') 
					: $this->session->headTitle;

		// Content load
		$_content = $_mod;
		if (empty($_content)){
			ob_start();
			$this->main();
			$_content = ob_get_contents();
			ob_end_clean();
		}
		
		// Default Content load
		if (empty($_content)){
			ob_start();	
			$_e = $this->includePage();	
			if ( Check::isError($_e) ) 
				foreach ($_e->get() as $key => $error)
					switch ($error['desc']) {
						case '404':
							$_hook = Hook::exec('error404');
							if (!$_hook) 
								$this->setVar( 'h_error', Error::displayError('Page non trouvée') );
							else $this->setVar( 'h_error', $_hook );
						break;
						default:
							$this->setVar( 'h_error', Error::displayError($error) );
							break;
					}
			if (Check::isError($_e)) $_e->displayErrors();	
			$_content = ob_get_contents();
			ob_end_clean();
		}

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
		
		// Body Template variables
		$this->setVar(array(
			'tpl_head_content' =>	$this->_setCss(),
			'tpl_footer_content'=> 	$this->_setJs(),
			'tpl_errors'=> 			$ctrl_e,
			'tpl_main_content'=> 	$_content
		));
		
		$body = $this->getTemplate( 
						$body_template, 
						isset($options['tpl_dir'])? $options['tpl_dir']:$this->templatePath  
					);
		if (Check::isError($body)) $body->displayErrors();
		else $body = Tools::replaceVars($body);
		// Body code traitement
		$this->postTraitment($body);
		// Html Template variables
		$this->setVar('tpl_body_content', (!Check::isError($body)) ? $body : '' );
		
		$r =$this->includeTemplate( 
						$html_template, 
						_TPL_DIR_
					);
		if (Check::isError($r)) $r->displayErrors();
		
		// Cache
		$cache_content = ob_get_contents();
		ob_end_clean();
		// All HTML code traitement
		$this->finalTraitment($cache_content);
		if ($this->minimizeCode){
			$cache_content = str_replace(PHP_EOL,'',$cache_content);
			$cache_content = str_replace("\t",'',$cache_content);
		}
		if($this->useCache) file_put_contents($cache, $cache_content) ; 
		$this->cachingControl($cache_content);
		if (!$ghost) echo $cache_content;
		else {	
			return $cache_content;
		}
	}
	
	public function cachingControl( $PageContent ){		 
		 
		// Generate unique Hash-ID by using MD5
		$HashID = md5($PageContent);
		 
		// Define the proxy or cache expire time 
		$ExpireTime = 3600; // seconds (= one hour)
		 
		// Get request headers:
		$headers = apache_request_headers();
		 
		// Add the content type of your page
		header('Content-Type: text/html');
		 
		// Content language
		header('Content-language: '._CURRENT_LANG_);
		 
		// Set cache/proxy informations:
		header('Cache-Control: max-age=' . $ExpireTime); // must-revalidate
		header('Expires: '.gmdate('D, d M Y H:i:s', time()+$ExpireTime).' GMT');
		 
		// Send a "ETag" which represents the content
		header('ETag: ' . $HashID);
		header('Content-Length: ' . strlen($PageContent));
	
	}
	/**
	 * Override of Controller::call for Auwa Controller
	 *
	 * @param 	boolean ghost	Call without displaying, or not
	 *
	 * @return	Error	e		Error
	 */
	public static function load($name, $ghost){
		$e = new Error();
		if ($name ==null) $name = Tools::getValue('controller');
		if (!$name) return $e;
		$c = self::getCtrls();
		$r = include_once( array_search($name, $c) ) ;
		if ( $r ){
			$className = $name.'Controller';
			if ( class_exists($className) ){
				$i = false;
				$controller = new $className();
				$controller->init($ghost);
				$e->merge( $controller->errors );
			}
		}
		else $e->addError($name.' : Ce contrôleur n\'existe pas' );
		
		return $e;

	}
	/**
	 * Get all registered objects
	 *
	 * @return	array	$r	Array of Sub-classes
	 */
	final public static function getCtrls(){
		$dir = _CTRL_DIR_;
		$c = array();
		if (is_dir($dir)) {
		   if ($dh = opendir($dir)) {
		       while (($file = readdir($dh)) !== false) {
		           if( $file != '.' && $file != '..' && preg_match('/.php$/', $file)) {
		           include_once _CTRL_DIR_.$file;
		           }
		       }
		       closedir($dh);
		   }
		}
		$r = array();
		$classes=get_declared_classes();
		foreach($classes as $class){
			if(is_subclass_of($class, 'Auwa\AuwaController') || is_subclass_of($class, 'Auwa\AuwaAppController')){
				$reflector = new \ReflectionClass($class);
				$r[$reflector->getFileName()] = preg_replace('/^Auwa\\\/','', preg_replace('/Controller$/','',$class) );
			}
		}
		return $r;
	}

	public function setInstance(&$o){
		self::$_instance = $o;
	}
	public static function getInstance(){
		return self::$_instance;
	}
}

?>
