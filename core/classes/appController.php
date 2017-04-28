<?php
namespace Auwa;
/**
 * APP Controller 
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */
 
/**
 * App Controller class
 * This class extends the base controller class
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class AppController extends Controller{
	
	/*
	 * Header Title
	 * @var string
	 */ 
	public static $headerTitle;
	
	/*
	 * Header sub Title
	 * @var string
	 */
	public static $headerSubTitle;

	/*
	 * Items on a header
	 * @var array
	 */
	public static $headerTabs = array();
	
	/*
	 * Default template path for the app UI
	 */
	public static $defaultTemplatePath;
	
	/*
	 * Set if this controller uses the AuwaController filter
	 * @var array
	 */
	public $noCtrlFilter = false;

	/*
	 Queries vars
	 */
	public $query=false;
	protected $data=false;
	protected $response = array('errors'=>['Méthode manquante ou incomplète'], 'data'=>array());
	protected $action=false;

	/**
	 * Get an AppController instance
	 *
	 * @see Controller::__contruct
	 */
	public function __construct($mod=false, $name=false, $load='default', $mode='app'){ 
		parent::__construct($mod, $name, $load, $mode);
		self::$defaultTemplatePath = _ROOT_DIR_.'templates/app/';
		$this->ctrlType = 'app';
		$this->templatePath = ( isset($this->module) )? _ROOT_DIR_.'modules/'.$this->module.'/views/' : _TPL_THEME_DIR_;
	}
	
	/**
	 * Define into a template variable a specific class in use
	 *
	 * @param 	string 		$class 		Name of the class
	 */
	public function setSpecificClass($class=null){
		if (isset($class)) $this->setVar('ctrlName', $class);
	}

	/**
	 * Set Title displayed by the navigator
	 *
	 * @param 	string		$title		Title
	 */
	public function setTitle($title){
		$this->setVar('app_title', $title);
	}

	/**
	 * Set a header zone
	 * 
	 * @param	string		$title		Title displayed by the header
	 * @param	string		$subtitle	Set header subtitle
	 * @param	array		$tabs		Set header items
	 */
	public function setHeader($title=false, $subtitle=false, $tabs=false){
		if ($title) self::$headerTitle = $title;
		if ($subtitle) self::$headerSubTitle = $subtitle;
		if ($tabs) self::$headerTabs = $tabs;
	}
	/**
	 * Insert headers items on current Header
	 *
	 * @param	array		$tabs		Array of items
	 */
	public function insertHeaderTabs($tabs){
		$this->headerTabs = $tabs;
	}
	
	/**
	 * Insert a item on current Header
	 *
	 * @param	array		$tab		Array of item settings
	 */
	public function insertHeaderTab($tab){
		self::$headerTabs[] = $tab;
	}

	/**
	 * Display current header
	 *
	 * @param 	string		$includeTpl 	Insert a specific template
	 */
	public function displayHeader($includeTpl = false){
		$this->setVar('headerTitle', self::$headerTitle);
		$this->setVar('headerSubTitle', self::$headerSubTitle);
		$this->setVar('headerTabs', self::$headerTabs);
		$this->setVar('headerIncl', $includeTpl ? $this->getTemplate($includeTpl,$this->templatePath) : '');
		$e = $this->includeTemplate('_header', self::$defaultTemplatePath);
		if (Check::isError($e)) $e->displayErrors();
	}

	/**
	 * include a template and display his content
	 * 
	 * @param 	string 	$tpl 	name of the template to include
	 */
	public function displayContent($tpl){
		$e = $this->includeTemplate($tpl, $this->templatePath);
		if (Check::isError($e)) $e->displayErrors();
	}



	/**
	 * Execute queries from Ajax
	 * 
	 */
	protected function query(){}

	/**
	 * Build the ajax query response
	 * 
	 * @param 	boolean 	$ctrlExists 
	 * @return 	json 		json formated response
	 */
	protected function _query($ctrlExists=true){
		$this->query = Tools::getValue('query', 'post');
		$this->data = Tools::getValue('data', 'post');
		if (!$this->query && $ctrlExists) return;
		$this->query();
		$this->response['controller'] = $this->name;
		$this->response['module'] = $this->module;
		if (!$ctrlExists){
			$this->response['errors'] = ["Ce controlleur n'existe pas"];
			$this->response['controller'] = $this->getVar('controller');
			$this->response['module'] = $this->getVar('module');
		}
		echo json_encode( $this->response ); die();
	}

	/**
	 * Execute action from a controller call via Ajax
	 * 
	 */
	protected function action(){}

	/**
	 * Build the response for an Ajax action from a controller
	 * 
	 * @param 	boolean 	$ctrlExists 
	 * @return 	json 		json formated response
	 */
	protected function _action($ctrlExists=true){
		$this->action = Tools::getValue('action', 'post');
		if (!$this->action && $ctrlExists) return;
		ob_start();
		$r = $this->action();
		$_content = ob_get_contents();
		ob_end_clean();
		if (is_array($r) && isset($r['errors'])) {
			$this->response['errors'] = $r['errors'];
			exit( json_encode( $this->response ) );
		}
		if ($_content=='') {
			$this->response['errors'] = ["Missing or empty method for ".$this->name];
			exit( json_encode( $this->response ) );
		}
		$vars = array();
		preg_match_all('/{Set\:([^}]+)}/', $_content, $vars);
		if (count($vars)>1){
			foreach($vars[1] as $rule){; 
				$params = explode('=',trim($rule));
				$vname = 'meta'.trim($params[0]);
				$$vname = trim($params[1]) ;
			}
		}
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

		$json = array(
				'css' => AppController::$cssFiles,
				'js'  => AppController::$jsFiles,
				'title'=> $this->getVar('app_title'),
				'html' => preg_replace('#\\t|\\n#', '', ($_content)),
				'noCtrlFilter' => $this->noCtrlFilter
			);
		echo json_encode( $json ); die();
	}

	/**
	 * Build a response from status and data
	 *
	 * @param 	boolean 	$status 	status of the response
	 * @param 	mixed 		$date 		data to include with the response
	 */
	protected function setResponse($status, $data){
		if ($status===true) {
			$this->response['errors'] = false;
			$this->response['data'] = $data;
		} else {
			$this->response['errors'] = is_array($data) ? $data : array(0=>$data);
		}
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
	public static function call($name, $mode="default", $path=null, $mod=null){
		$e = new Error();
		$c = new AppController();
		$c->setVar('controller', Tools::getValue('controller'));
		if ($name ==null) $name = Tools::getValue('controller');
		if (!$name) return $e;
		if ($path==null) $path = _CTRL_DIR_;
		if (is_file( $path.$name.'.php') ){
			include_once($path.$name.'.php' );
			$className = 'Auwa\\'.$name.'Controller';
			if ( class_exists($className) ){
				$i = false;
				$controller = new $className($mod, $name, $mode);
				if(Auwa::$requestMode == 'POST' && $name==Tools::getValue('controller', 'post')) {
					$controller->_query();
					$controller->_action();
				}
				//var_dump($_GET);die();
				if ($mode=='default' && Auwa::$requestMode == 'GET' ) $controller->init();
				if (is_array($mode) && Auwa::$requestMode == 'GET' ) $controller->init($mode);
				$e->merge( $controller->errors );
			}
		}
		else {
			if ($c->getVar('controller')==$name) $c->_query(false);
		}
		return $e;
	}
}
?>