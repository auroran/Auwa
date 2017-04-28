<?php
namespace Auwa;
/**
 * Auwa Admin Controller 
 *
 * Auwa use this AuwaController to build the code administration
 *
 * @package Auwa \controllers\
 * @copyright 2017 AuroraN
 */

 
/**
 * Controller for Auwa Administration
 *
 * This Auwa Controller extends CoreController
 * This Class is final, override is impossible
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
final class AuwaCoreController extends CoreController{
	/* upload settings */
	static $fileTypesAllowed = array('jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff');

	/**
	 * Set Css files to use
	 *
	 * Theses files are placed after main Css files
	 * This function reads CoreController::$cssFiles array, witch can be filled by module controllers
	 *
	 * @return 	string		$_content		Head Block part about CSS (<link rel="stylesheet"> tags)
	 */
	public function _setCss(){	
		ob_start();	
		$this->addCssBefore('coreUI');
		$this->addCssBefore('jquery-ui');
		$this->addCss('eff.min', _FW_DIR_.'eff/css/');

		foreach( CoreController::$cssFiles as $file)
			Auwa::insert_css( $file );
		$_content = ob_get_contents();
		ob_end_clean();
		return $_content;
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
		ob_start();
		echo PHP_EOL;
		if (!empty($this->auwa->jsVars)){
			echo PHP_EOL.'<script type="text/javascript">'.PHP_EOL.'var auwa = '.json_encode($this->auwa->jsVars).';</script>';
		}
		$this->addJs('coreUI', _CORE_JS_DIR_, true);
		$this->addJs('update', _CORE_JS_DIR_);
		$this->addJs('tinymce.min', 		'core/frameworks/tinymce/');
		$this->addJs('editor');
		$this->addJs('jquery-ui.min', _JS_DIR_, true);
		$this->addJs(\Definer::$config['jsLibrary']['core'], _JS_DIR_, true);
		foreach( CoreController::$jsFiles as $file)
			Auwa::insert_js( $file);
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
	final public function init(){
			
			Auwa::$base .= 'core/';	
			$p = DefaultModel::loadObject('Page');
			$isCoreUser =  $this->getVar('isCoreUser');
			if (func_num_args()>0) $options = func_get_arg(0);
			// Auwa Admin Controller use admin root template directory
			$this->templatePath = _CORE_DIR_.'views/'; 
			$ctrl_e = new Error;
			
			$siteInfos = Auwa::get_siteInfos();
			$modulesConfig = \ConfigFile::getConfig('config/modules');
			$currentSelectedController = $this->session->currentSelectedController;
			$languages = array();
			foreach (Lang::getEnabledLanguages() as $iso=>$lang) {
				$languages[$iso] = $lang;
			}
			$lang = Tools::getvalue('coreLang') ? Tools::getValue('coreLang') : _DEFAULT_LANG_;
			if (!defined('_CURRENT_LANG_')) define('_CURRENT_LANG_', $lang);

			// set extended rules
			$erA = array(
						'/{expr:([^}]+)}/',
					);
			$erB = array(
						'<?php echo Editor::replaceExpr($1);?>',
					);
			// add extended rules
			Tools::addTplRules($erA, $erB);
			$coreSettings = \ConfigFile::getConfig('config/core');
			$this->setVar('current_menu', $coreSettings['Panel']);
			// Tpl vars
			$this->setVar( array(
				'current_panel'				=> $coreSettings['Panel'],
				'current_lang'				=> $lang,
				'queryCore'					=> Auwa::$base,
				'currentSelectedController'	=> !empty($currentSelectedController) ? $currentSelectedController : _DEFAULT_CTRL_,
				'version'					=> Session::get()->AuwaVersion,
				'languages'					=> $languages,
				'controllers'				=> AuwaController::getCtrls(),
			));
			$panel =$this->getTemplate( 
							"_panel", 
							isset($options['tpl_dir'])? $options['tpl_dir']:false 
						);
			$this->setVar('panelContent', $panel);

			// temp, ACUI is not multi-language capable yet
			$this->setVar('current_panel', 'fr');

			ob_start();
			$_content = "";

			$autoLoad = $modulesConfig['autoLoadCore'];
			$moduleCtrl = array();
			$mod = false;
			foreach ($autoLoad as $mod => $ctrl) {
				if( $mod!==$this->getVar('module')) $r = Auwa::sysprobe( $mod );
				$moduleCtrl[$mod] = explode(',', $ctrl);
			}
			$c = CoreController::call( User::isCoreUser() ? $this->getVar('coreController') : 'DefaultCore' );
			$c->displayErrors();

			$_content = ob_get_contents();
			ob_end_clean();
			if ( $_SERVER['REQUEST_METHOD'] == 'POST'){ // better than using the Auwa::$requestMode variable
				$mod = ($this->getVar('module')) 
						? Auwa::sysprobe( $this->getVar('module') ) 
						: false;
				if (!$mod && $this->getVar('controller')){
					$c = CoreController::call( $this->getVar('controller') );
					$c->displayErrors();
				}
				if ( Tools::getValue('query', 'post') ){
					$this->_query(false);
				} else 
					exit ( json_encode( array('errors'=>['Missing method in request']) ) );
			} elseif ( ! in_array($this->getVar('coreController'), array('DefaultCore', 'CoreLogin') ) )  {
					exit ('Can not be called in GET request');
			}
				

			// Js vars
			$this->setJsVar(array(
				'queryCore'			=> Auwa::$base,
				'current_lang'		=> $lang,
				'jquery_dateformat'	=> Tools::getJqueryDateFormat(),
				'modules'			=> $moduleCtrl,
				'action'			=> Tools::getValue('action'),
				'languages'			=> $languages,
				'fileTypesAllowed'	=> self::$fileTypesAllowed,
			));
			//Tpl vars
			$this->setVar(array(
				'modExec'			=> $mod,
				'main_content'		=> $_content,
				'tpl_head_content'	=> $this->_setCss(),
				'tpl_footer_content'=> $this->_setJs(),
				'tpl_errors'		=> $ctrl_e,
				'fullHeader'		=> true,
				'defaultTemplatePath'=> self::$defaultTemplatePath,
			));
			
			if ( $isCoreUser ) {
        		Session::get()->coreAccess =  Tools::encryptDatas( md5(fileatime(_CORE_DIR_.'core.php')) );
				
			} else {
        		Session::get()->coreAccess = false;
				$this->insertHeaderTab(array(
					'icon'=>'key',
				));
			}
			ob_start();
			$this->displayHeader();
			$_header = ob_get_contents();
			ob_end_clean();
			$this->setVar('displayHeader', $_header);

			ob_start();
			$r =$this->includeTemplate( 
							"_html", 
							isset($options['tpl_dir'])? $options['tpl_dir']:false 
						);
			if (Check::isError($r)) $r->displayErrors();
				
			$content = ob_get_contents();
			ob_end_clean();
			echo $content;
	}

}
?>