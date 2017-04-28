<?php
namespace Auwa;
 
/**
 * Controller MENU for Auwa Administration
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class MenusController extends CoreController{

	function loadCss(){ // from core/
		$this->addCss('menus');
	}
	function loadJs(){
		$this->addJs('menuEditor');
	}

	public function action(){
    	$languages = array();
    	foreach (Lang::getEnabledLanguages() as $iso=>$lang) {
    		$languages[$iso] = $lang;
    	}
		$this->setTitle('les menus disponibles');
		$this->setVar('langs',  $languages);
		$this->setVar('j_langs',  json_encode($languages));
		$this->setVar('baseLink','?module=menuAdmin&controller=menus');
		$this->setVar('iso_lang', _DEFAULT_LANG_ ); // français
		$this->setJsVar('ajaxUrl', Auwa::$base.'?module=menuAdmin');
		//$e = $this->displayContent('pages');
		switch( Tools::getValue('action') ){
			case 'edit' :
				$menu = Menu::getMenu(Tools::getValue('menu'), true);
				$this->setVar('menu', $menu);
				$this->setVar('menuName', Tools::getValue('menu'));
				$this->setVar('createMenu', empty($menu));
				$this->setTitle( (empty($menu)?'Création':'Édition').' du menu '.Tools::getValue('menu'));
				$this->setVar('extendedLink','&action=edit'.( Tools::getValue('menu') ? '&menu='.Tools::getValue('menu') : '') );
				$this->setVar('i',0);
				$itemSample = array(
					'text'=>array(),
					'type'=>'Link',
					'link'=>null,
					'menu'=>array()
				);
				foreach ($languages as $iso => $lang) {
					$itemSample['text'][$iso] = $lang['name'];
				}
				$this->setVar('itemSample', $itemSample);
				$e = $this->displayContent('edit');
				break;
			case 'editItem':
				$menu = Tools::getValue('menu');
				$item_type_available = \ConfigFile::getConfig('config/menuTypes');
				$item = Tools::getValue('item') ? Tools::getValue('item') : array();
				$this->setVar(array(
					'menu'=> $menu,
					'item'=>$item,
					'item_type_available' => $item_type_available,
				));
				$this->setTitle(  (empty($item) ? 'Création':'Édition').' d\'un élément du menu '.Tools::getValue('menu') );
				$e = $this->displayContent('item');
				break;
			default:
				$this->setVar('list_item',  Menu::getAllMenus());
				$e = $this->displayContent('list');
		}
	}

	public function query(){

		// Save live-ordering changes
		if ( $this->query=='saveChanges' &&  isset($this->data['menu']) ){
			$r = Menu::setMenu($this->data['name'], isset($this->data['menu']) ? $this->data['menu'] : array() );
			$r ? $this->setResponse(true, $r) : $this->setResponse(false, 'Échec de la sauvegarde du menu');
		}		

		// Set a yaml based menu item
		if ( $this->query=='setName' ){
			$newName = $this->data['newName'];	
			$iniName = $this->data['iniName'];	
			$r = Menu::renameMenu(  $iniName, $newName, $this->data['origin'], $this->data['key']);
			$this->setResponse( $r? true:false, $r ? 'Update menu name done' : 'Update menu name failed');
		}


	}

}
?>