<?php
namespace Auwa;
/**
 * Auwa Functions
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */
/**
 * Class of usefull links or methods about Auwa
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class Auwa{
	/**
	 * Base url
	 * @var string
	 */
	public static $base;

    /**
     * Instance of simple object Auwa
     * @var Object
     */
    public static $_instance;

    /**
     * All variables used by template and page
     * @var array
     */
    public $tplVars=array();

    /**
     * All variables used by js scrits
     * @var array
     */
    public $jsVars=array();

    /**
     * type of request
     * @var String
     */
    public static $requestMode;

    public function __construct(){}

    /**
     * Get a tpl vars
     * Check if the controller is succesfully launched
     *
     * @param $name     string  Name of the variable to get
     *
     * @return          string  the variable
     */
    public function getVar($name=null){
        if (empty($name)) return $this->tplVars;
        return isset($this->tplVars[$name]) ? $this->tplVars[$name] : null;
    }
    /**
     * Set a tpl vars
     * @param $name     string  Name of the variable to set
     * @param $value    string  Value of the variable
     */
    public function setVar($name, $value){
        $this->tplVars[$name] = $value;
    }
    /**
     * Set a tpl vars
     * @param $name     string  Name of the variable to set
     * @param $value    string  Value of the variable
     */
    public function setJsVar($name, $value){
        $this->jsVars[$name] = $value;
    }


    /**
     * Get a tpl vars
     * Check if the controller is succesfully launched
     */
    public static function get(){
        if (empty(self::$_instance)) self:$_instance = new Auwa();
        return self::$_instance;
    }
	
	/**
	 * Call a Auwa Controller (Auwa default launcher)
	 * Check if the controller is succesfully launched
	 */
	public static function launch(){
        require_once _CORE_CLASSES_DIR_.'appController.php';
		$ctrl = Tools::getValue('AuwaController') ? Tools::getValue('AuwaController') : _DEFAULT_CTRL_;
		$Auwa = Controller::call($ctrl);
		self::checkControllerLaunch($Auwa);
	}
	
	/**
	 * Call the Admin Auwa Controller
	 * Check if the controller is succesfully launched
	 */
	public static function coreLaunch($adminPath){
		$isCoreUser = User::isCoreUser();													// Check if connected user is admin
		$module =  (Tools::getValue('module','post') ) ? Tools::getValue('module','post') : false; 	        // Get the module requested
        $ctrl =  Tools::getValue('controller','post')? Tools::getValue('controller','post') : false;    // Get the controller requested
        $Cctrl = Tools::getValue('coreController') ? Tools::getValue('coreController') : 'DefaultCore';
		if (!$isCoreUser && $Cctrl!='CoreLogin') {
            if (isset($_POST['controller'])) {
                echo "Disconnected"; die();
            }
            $_GET['coreController'] = 'CoreLogin';
            $Cctrl='CoreLogin';	// force authentification
        }
					
        require_once _CORE_CLASSES_DIR_.'appController.php';
        require_once _CORE_CLASSES_DIR_.'coreController.php';
        require_once _CORE_CTRL_DIR_.'AuwaCore.php';
		$controller = new AuwaCoreController();
		self::checkControllerLaunch( $controller );
		$controller->setVar( array(
            '_core_path'=>     _CORE_DIR_,
            'module'=>          $module,
            'controller'=>      $ctrl,
            'coreController'=>      $Cctrl,
            'isCoreUser'=>         $isCoreUser ) );
		$controller->init();
	}
    
	/**
     * Check if the controller launched is successfully launched
     *
     * @param    Object     $ctrl 
     */
	public static function checkControllerLaunch( $ctrl ){
		if (Check::isError($ctrl) && $ctrl!==false && $ctrl->hasError()) {
			$e = $ctrl->get();
			if (count($e)>1){
				$desc = "Une erreur est survenue";
				$e->displayErrors();
			} else $desc = $e[0]['desc'];
			if($e[0]['type']=='error') trigger_error( $desc, E_USER_ERROR);
		}
	}

    /**
     * Load classe file or dependencie file
     *
     * @param   string|array        $name
     * @param   string|array        $type
     * @param   string|array        $path
     */
    public static function loadPhp($name, $path=''){
        if ( !is_array($name) )
            $name = explode(',',$name);
        foreach ($name as $key=>$c) {
            $c = str_replace(PHP_EOL, '', trim($c));
            $t = (is_array($path) && isset($path[$key])) ? $path[$key] : $path;
            switch ($t) {
                case 'class':
                    $p = _CLASSES_DIR_;
                    break;
                case 'controller':
                     $p = _CTRL_DIR_;
                    break;
                case 'coreClass':
                    $p = _CORE_CLASSES_DIR_;
                    break;
                case 'coreController':
                    $p = _CORE_CTRL_DIR_;
                    break;
                default:
                    $p = $t;
                    break;
            }
            $r= @include_once($p.$c.'.php');
            if (!$r) trigger_error("The $t file '$c' was not found", E_USER_ERROR);
        }
    }

	/**
	 * Load a user module
	 *
	 * @param 	string		$moduleName 	Name of the module
	 *
	 * @see Module::load for more details
	 *
	 * @return 	Module						Module Object loaded
	 * @return	Error						Error Object if an error occured
	 */
	public static function modprobe($moduleName, $param='call:true'){
		return Module::load($moduleName, $param);
	}
	
	/**
	 * Load a system module (core module)
	 *
	 * @param 	string		$moduleName 	Name of the module
	 *
	 * @see Module::load
	 *
	 * @return 	Module						Module Object loaded
	 * @return	Error						Error Object if an error occured
	 */
	public static function sysprobe($moduleName){
		return Module::load($moduleName, 'call:true#system:true');
	}

	/**
	 * Get Informations about the site
	 * 
	 * @return 	array	Array of each informations
	 */
	public static function get_siteInfos(){
		$i = \ConfigFile::getConfig('config/infos');
		if (empty($i)) return array();
		return $i;	
	}
	

    /**
     * Convert a absolute path to an URL
     *
     * @param 	string 		$path	Absolute path to convert
     *
     * @return 	string				URL
     */
    public static function pathToUrl( $path ){
	    return str_replace( _ROOT_DIR_, self::url(),$path );
    }
    
    /**
     * Convert a absolute path to a relative path
     *
     * @param 	string 		$path	Absolute path to convert
     *
     * @return 	string				Relative path
     */
    public static function pathTotRelative( $path ){
	    return str_replace( _ROOT_DIR_, "",$path );
    }
    
    /**
     * Get base URL of the site, with the protocol used
     *
     * @return 	string				URL
     */
    public static function url(){
        return _PROTOCOL_._BASE_URL_;
    }
    
    /**
     * Display URL of the site, whth the protocol used
     */
    public static function display_url(){
        echo self::url();
    }
	    
	/**
     * Get the URL of cache directory
     *
     * @param 	string 		$more	Sub-directory of cache directory
     *
     * @return 	string				Complete URL of cache directory
     */    
	public static function cache_url($more=""){
        return self::url().str_replace(_ROOT_DIR_,'',$more);
    }
    
    /**
     * Get the URL of CSS files directory
     *
     * @param 	string 		$more		Sub-directory
     * @param 	string 		$corePath	if true, use the core/css directory
     *
     * @return 	string					Complete URL of CSS directory
     */ 
    public static function css_url($more="", $corePath=false, $forceCssDir=false){
    	$prefix = (Auwa::$base != _PROTOCOL_._BASE_URL_) ? _PROTOCOL_._BASE_URL_ : '';
   		return $prefix.( !$corePath ? ($forceCssDir ? _CSS_DIR_ : _CSS_THEME_DIR_): str_replace(_BASE_URL_,_BASE_URL_.'core/',_CSS_URL_) ).$more;
    }
    
    /**
     * Display the link tag for CSS
     *
     * @param   array      $cssArray       Sub-directory
     * @param   string      $corePath   if true, use the core/css directory
     */ 
    public static function insert_css($cssArray, $corePath=false){
        if ($corePath) $cssArray['url'] = str_replace(_BASE_URL_, _BASE_URL_.'core/', $cssArray['url']);
        echo '<link href="'.$cssArray['url'].'.css" rel="stylesheet" media="'.(isset($cssArray['media'])? $cssArray['media']: 'screen').'"">'.PHP_EOL;
    }

    /**
     * Set the LINK tag to link CSS file
     *
     * @param 	string 		$input		Sub-directory / file path / file url
     * @param 	string 		$param	if true, use the core/js directory or insertion mode
     * @param	string		$media		Media Query propertie
     */ 
    public static function link_css_url($input="", $param=false, $media=false, $disableCache=false, $placeBefore=false ){
        if (_USE_CACHE_ && $param!=='external' && empty($_POST) && !$disableCache){ 
            $css_content = file_get_contents($input.'.css');
            // we will translate all '../' url form to real path
            $dir = '/'.str_replace(_ROOT_DIR_,'',realpath(dirname($input)));
            $a = array();
            preg_match_all('#(.\.\/)+#', '../'.$css_content, $a);
            arsort($a[0]);
            if (isset($a[0])){
                foreach ($a[0] as $key => $subDir) {
                    $a_d = explode('/', $dir);
                    for($i=0; $i<count( explode('/', $subDir) )-1; $i++){ 
                        array_pop($a_d);
                    }
                    $css_content= str_replace($subDir, Auwa::url().implode('/', $a_d).'/', $css_content);
                }
            }
            if (!$placeBefore)
                Controller::$cssCacheFile .= $css_content.PHP_EOL;
            else 
                Controller::$cssCacheFile = $css_content.PHP_EOL.Controller::$cssCacheFile;
        } else {
        	$prefix = (Auwa::$base != _PROTOCOL_._BASE_URL_) ? _PROTOCOL_._BASE_URL_ : '';
            $path= $input;
            switch ($param) {
                case 'external':
                    $prefix = '';
                    $path = false;
                    break;
                case 'insert':
                    $input = str_replace(_ROOT_DIR_, '', $input);
                    $path = $input;
                    break;
            }
            $insert = array(
                    'url'=> ($param!=='external' ? Auwa::url() : '' ).str_replace(_ROOT_DIR_,'',str_replace(Auwa::url(),'',$input)),
                    'path'=> $path,
                    'media'=> $media!==false ? ' media="'.$media.'"' : "screen",
                    'param'=> $param,
                );
            if (!$placeBefore)
                Controller::$cssFiles[] =$insert;
            else 
                array_unshift(Controller::$cssFiles, $insert);
        }
    }
        
    public static function insert_css_cache(){
        if (!_USE_CACHE_) return;
        $cache_id= _CURRENT_CTRL_."_".implode($_GET['urlPart'],'_');
        $cache_file= _CACHE_DIR_.'css/'.$cache_id.'.css';
        if (!is_dir(_CACHE_DIR_."css")) $r = mkdir(_CACHE_DIR_."css");
        if ($r===false){
            trigger_error('Cache dir ('._CACHE_DIR_.' not writtable', E_USER_ERROR);
        }
        $css = Controller::$cssCacheFile;
        if (_MINZ_CODE_){
            $css = str_replace(PHP_EOL,'',$css);
            $css = str_replace("\t",'',$css);
            $css = str_replace("    ",'',$css);
        }
        file_put_contents($cache_file, $css);
        echo '<link rel="stylesheet" href="'.Auwa::cache_url($cache_file).'" media="screen">';
    }
    
    
    /**
     * Get the URL of JS files directory
     *
     * @param 	string 		$more		Sub-directory/filename
     * @param 	string 		$corePath	if true, use the core/js directory
     *
     * @return 	string					Complete URL of JS directory
     */     
    public static function js_url($file="", $corePath=false){
    	$prefix = (Auwa::$base != _PROTOCOL_._BASE_URL_) ? _PROTOCOL_._BASE_URL_ : '';
   		return $prefix.( !$corePath ? _JS_THEME_URL_ : str_replace(_BASE_URL_,_BASE_URL_.'core/',_JS_URL_) ).$file;
    }
    
    /**
     * Display the script tag for JS
     *
     * @param   array      $cssArray       Sub-directory
     * @param   string      $corePath   if true, use the core/css directory
     */ 
    public static function insert_js($jsArray, $corePath=false){
        if ($corePath) $jsArray['url'] = str_replace(_BASE_URL_, _BASE_URL_.'core/', $jsArray['url']);
        echo '<script src="'.$jsArray['url'].'.js" type="text/javascript"></script>'.PHP_EOL;
    }

    /**
     * Set the SCRIPT tag to link JS file
     *
     * @param   string      $input      Sub-directory / file path / file url
     * @param   string      $param  if true, use the core/js directory or insertion mode
     * @param   string      $media      Media Query propertie
     */ 
    public static function link_js_url($input="", $param=false, $placeBefore=false, $cache=true ){
        if (_USE_CACHE_ && $param!=='external' && $cache  && empty($_POST)){
            $contents = file_get_contents($input.'.js');
            if (!$contents)  return;
            if (!$placeBefore)
                Controller::$jsCacheFile .= $contents.';'.PHP_EOL;
            else 
                Controller::$jsCacheFile = $contents.';'.PHP_EOL.Controller::$jsCacheFile;
        } else {
            $prefix = (Auwa::$base != _PROTOCOL_._BASE_URL_) ? _PROTOCOL_._BASE_URL_ : '';
            switch ($param) {
                case 'external':
                    $prefix = '';
                    break;
                case 'insert':
                    $input = str_replace(_ROOT_DIR_, '', $input);
                    break;
            }
            $insert = array(
                    'url'=> ($param!=='external' ? Auwa::url() : '' ).str_replace(_ROOT_DIR_,'',str_replace(Auwa::url(),'',$input)),
                );
            if (!$placeBefore)
                Controller::$jsFiles[] =$insert;
            else 
                array_unshift(Controller::$jsFiles, $insert);
        }
    }
    public static function insert_js_cache(){
        if (! _USE_CACHE_) return;
        $cache_id= _CURRENT_CTRL_."_".implode($_GET['urlPart'],'_');
        $cache_file= _CACHE_DIR_.'js/'.$cache_id.'.js';
        if (!is_dir(_CACHE_DIR_."js")) $r = mkdir(_CACHE_DIR_."js");
        if ($r===false){
            trigger_error('Cache dir ('._CACHE_DIR_.' not writtable', E_USER_ERROR);
        }
        $js = Controller::$jsCacheFile;
        if (_MINZ_CODE_){
            $js = str_replace(PHP_EOL,'',$js);
            $js = str_replace("\t",'',$js);
        }
        if(!is_file($cache_file) ||true) {
            file_put_contents($cache_file, $js);
        }
        Controller::$jsFiles[] = array('url'=>Auwa::pathToUrl(_CACHE_DIR_.'js/'.$cache_id));
    }
        
        
    /**
     * Get the URL of IMG files directory (use by the site or by the theme)
     *
     * @param 	string 		$more		Sub-directory
     * @param 	string 		$corePath	if true, use the core/js directory
     *
     * @return 	string					Complete URL of IMG directory
     */  
    public static function img_url($more="", $corePath=false){
    	$prefix = (Auwa::$base != _PROTOCOL_._BASE_URL_) ? _PROTOCOL_._BASE_URL_ : '';
   		return $prefix.( !$corePath ? _IMG_URL_ : str_replace(_BASE_URL_,_BASE_URL_.'core/',_IMG_URL_) ).$more;
    }

    /**
     * Get the URL of img from theme directory
     *
     * @param   string      $more       Sub-directory
     * @param   string      $corePath   if true, use the core/js directory
     */ 
    public static function theme_img_url($more="", $corePath=false){
        echo str_replace(_ROOT_DIR_, _PROTOCOL_._BASE_URL_,_THEME_DIR_).'img/';
    }

   
    /**
     * Get the URL of thirty-part Frameworks directory
     *
     * @param 	string 		$more		Sub-directory
     * @param 	string 		$corePath	if true, use the core/js directory
     *
     * @return 	string					Complete URL of Frameworks directory
     */         
    public static function fw_url($more="", $corePath=false){
    	$prefix = (Auwa::$base != _PROTOCOL_._BASE_URL_) ? _PROTOCOL_._BASE_URL_ : '';
   		return $prefix.( !$corePath ? _FW_URL_ : str_replace(_BASE_URL_,_BASE_URL_.'core/',_FW_URL_) ).$more;
    }
 

    /**
     * Get the URL of data files directory
     *
     * @param 	string 		$more		Sub-directory
     * @param 	string 		$corePath	if true, use the core/js directory
     *
     * @return 	string					Complete URL of Data directory
     */     
    public static function data_url($more="", $corePath=false){
    	$prefix = (Auwa::$base != _PROTOCOL_._BASE_URL_) ? _PROTOCOL_._BASE_URL_ : '';
        return $prefix._DATA_URL_.$more;
    }

    /**
     * Get the current Auwa Controller instance
     *
     * @return AuwaController
     */
    public static function getCtrlInstance(){
        return AuwaController::getInstance();
    }
    
}

// define default base url
Auwa::$base = _PROTOCOL_._BASE_URL_;
// Set Instance
Auwa::$_instance = new Auwa();
?>
