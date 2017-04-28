<?php
namespace Auwa;
/**
 * Controller YamlEditor for Auwa Administration
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class yamlEditorController extends CoreController{
	function loadCss(){ // from core
		$this->addCss('monokai-sublime', 	'core/frameworks/codeEditor/styles/');
		$this->addCss('yamlEditor');
	}
	function loadJs(){
		if (!User::isCoreUser()) return;
		$this->addJs('codeEditor', 		_CORE_DIR_.'frameworks/codeEditor/');
		$this->addJs('highlight.pack', 		_CORE_DIR_.'frameworks/codeEditor/');
		$this->addJs('yamlEditor/yamlEditor');
		$this->addJs('uiActions',		_CORE_JS_DIR_.'yamlEditor/');
	}
	function action(){
		$this->setTitle('Configurations');
		$headerTabs = 
			array(
				array('icon'=>'save',
					"attr"=>array(
							'role'=>'saveFile'
						)
					),
				array(
					'class'=>'separator',
				),
				array(
					'icon'=>'file-text', 
					'smartAccess'=>true,
					'attr'=> array(
							'title'=>"Liste des fichiers",
							'data-config'=>'config/yamlEditor',
							'role'=>'yamlFileSettings'
							)
				),
				array(
					'icon'=>'gear', 
					'smartAccess'=>true,
					'attr'=> array(
							'title'=>"Paramètres Généraux",
							'data-config'=>'config/config',
						)
					),
				array(
					'icon'=>'info', 
					'smartAccess'=>true,
					'attr'=> array(
							'title'=>"Informations sur le site",
							'data-config'=>'config/infos',
						)
					)
			);

		$config = \ConfigFile::getConfig('config/core');
		$config = $config['YamlEditor'];
		foreach ($config['files'] as $key => $value) {
			$headerTabs[] = array(
					'icon'=>$value['icon'],
					'smartAccess'=>true,
					'attr'=> array(
							'title'=>$value['title'],
							'data-config'=>$key,
						)
				);
			$r = \ConfigFile::createIfNotExists($key);
		}
		$this->setVar('headerTabs', $headerTabs);
		if (User::isCoreUser()){
			$e = $this->includeTemplate('yamlEditor');
			if (Check::isError($e)) $e->displayErrors();
		}
		else 
			Error::displayError("Vous n'avez pas les droits nécessaires pour utiliser cet éditeur");

	}

	protected function query(){
		// fonction exécutées via POST
		$isAdmin = User::isCoreUser();
		if (!$isAdmin) {
			$this->setResponse(false, 'You are not a Core User');
			return;
		}
		switch ($this->query) {
			case 'getYamlFile':
				$this->setResponse(true, \ConfigFile::getFileContent( _CORE_DIR_.$this->data['file'] ));
				break;
			
			case 'setYamlFile':
				$this->setResponse(true, \ConfigFile::setFileContent( _CORE_DIR_.$this->data['file'], $this->data['yamlContent'] ) );
				break;
		}
	}
}
	
?>
