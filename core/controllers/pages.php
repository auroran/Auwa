<?php
namespace Auwa;
 
/**
 * Controller PAGE for Auwa Administration
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 * @todo Faire la pagination
 */
class PagesController extends CoreController{

	public function loadJs(){
		$this->addJs('pageEditor');
	}

	public function action(){
		$this->setTitle('Pages disponibles');
		if( DefaultModel::tableExists('Auwa\\Page') === false){
			DefaultModel::createTable('Auwa\\Page');
		}
		if( DefaultModel::tableExists('Auwa\\PageContent') === false){
			DefaultModel::createTable('Auwa\\PageContent');
		}
		$this->setVar('baseLink','?controller=pages');
		$this->setJsVar('queryCore', Auwa::$base.'?controller=pages');

		$page = new Page( (int)Tools::getValue('id_page') );
		if (Tools::getValue('id_type')) $page->id_type = Tools::getValue('id_type');
		$_action = Tools::getValue('action');
		//$e = $this->displayContent('pages');
		switch( $_action ){
			case 'edit' :
				$contents  = $page->getContents();
				foreach ($this->getVar('languages') as $idl => $c) {
					if (empty($page->id_page)) $content[$idl]['controller'] = $this->getVar('currentSelectedController');
					$contents[$idl]['html'] = str_replace( "{url:data}", str_replace(_ROOT_DIR_,'../',_DATA_DIR_ ) , $contents[$idl]['html']);
				}
				$this->setTitle( 'Édition de la page '.($page->id_page ? '#'.$page->id_page:'') );
				$this->setVar(array(
					'page'				=> (array)$page,
					'contents'			=> $contents,
				));
				$e = $this->displayContent('pages/edit');
			break;
			case 'infos' :
				//page css
				$contents  = $page->getContents();
				$this->setTitle( "Informations sur la page ".($page->id_page ? '#'.$page->id_page:'') );
				foreach ($this->getVar('languages') as $idl => $c) {
					if( !isset($contents[$idl]) ){
						$contents[$idl] = (array)new PageContent();
					}
					if ( empty($contents[$idl]['rewrite']) ) 
						$contents[$idl]['rewrite'] =  !empty($contents[$idl]['title']) 
								? Tools::url_transform($contents[$idl]['title'])
								: '';
				}

				$this->setVar(array(
					'controller'		=> Tools::getValue('auwaController'),
					'page'				=> (array)$page,
					'contents'			=> $contents,
				));
				$e = $this->displayContent('pages/infos');
				break;
			default:
				$this->addCss('pages');
				$this->setJsVar(array(
					'addLink'		=> '?controller=pages&action=edit&id_type='
				));
				$this->setVar('list_item',  Page::getCollectionAsArray() );
				$e = $this->displayContent('pages/list');
		}
	}

	protected function query(){
		error_reporting(0);
		$errors=new Error();
		switch($this->query){

			case 'setPageInfos':
			//Save the page type and SEO informations
				$form = $this->data;
				$id=$form['id_page'];
				$page = new Page($id);
				$page->id_type = $form['id_type'];
				$page->css =$form['css'];
				$page->js =$form['js'];
				$page->enable = true;
				$res = $page->update();
				if (!$res ) {
					$this->errors->addError('L\'enregistrement de la page a échoué', 'danger');
					goto endPageBuild;
				}
				if ($page->id_page==null) $page = DefaultModel::getLastInserted('Auwa\\Page', $page->insert_date);
				$id_page = $page->id_page;
		    	foreach (Lang::getEnabledLanguages() as $iso_lang=>$lang) {
		    		$contents = $page->contents[ $iso_lang ];
		    		$c_obj = new PageContent( $contents['id_content'] );
		    		$res = false;
	    			$c_obj -> set( 
	    				$id_page, 
	    				$iso_lang, 
	    				$form['title_'.$iso_lang], 
	    				$form['rewrite_'.$iso_lang], 
	    				$form['controller'], 
	    				$form['description_'.$iso_lang]
	    			);
	    			$res = $c_obj->update();
	    			if (!$res || !is_array($res) || $res['id_page']==null) 
	    				$this->errors->addError('L\'enregistrement des contenus et description pour la langue '+$lang['name']+ ' a échoué');
	    		}
				endPageBuild:
		    	if ( ! $errors->hasError() ){
		    		$this->setResponse(true, $id_page);
		    	} else {
		    		$this->setResponse(false, $this->errors->getErrorMsg() );
		    	}
		    	break;

		    case 'setHtmlContent':
		    // write the html content of a page
		    	$r = array();
		    	foreach (Lang::getEnabledLanguages() as $iso_lang=>$lang) {
		    		$id_content = $this->data[$iso_lang]['id_content'];
		    		$c_obj = new PageContent( $id_content );
		    		$h = new Editor( $this->data[$iso_lang]['html'] );
	    			$c_obj -> html =  Editor::replaceExpr( $h->getHtml() );
	    			$res = $c_obj->update();
		    		if (!$res || !is_array($res) || $res['id_content']==null) 
		    			$this->errors->addError('L\'enregistrement en "'+$lang['name']+ '" a échoué');
			    		$c_obj = new PageContent( $id_content );
			    		$r[$iso_lang] = $c_obj -> html;
		    	}
		    	if ( ! $this->errors->hasError() ){
		    		$this->setResponse(true, $r);
		    	} else {
		    		$this->setResponse(false, $this->errors->getErrorMsg() );
		    	}
		    	break;
		    case 'deletePage':
		    // delete a page and its contents
		    	if (!$this->data['id_page']){
		    		$this->setResponse(false, 'ID non renseigné');
		    		return;
		    	}
		    	$p = new Page($this->data['id_page']);
		    	$r = $p->remove();
		    	$this->setResponse($r, $r ? 'Page supprimée' : 'Erreur durant la suppresion');
		    	break;
		}
	}
}
?>