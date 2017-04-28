<?php
namespace Auwa;
 
/**
 * Controller PAGE for Auwa Administration
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 * @todo Faire la pagination
 */
class UserController extends CoreController{
	public function loadJs(){
		$this->addJs();
	}

	public function action(){
		$this->setTitle('Utilisateurs');
		$this->setJsVar('queryCore', Auwa::$base.'?controller=user');

		$user = new User( (int)Tools::getValue('id_user') );
		$_action = Tools::getValue('action');
		//$e = $this->displayContent('pages');
		switch( $_action ){
			case 'edit' :
				$this->setTitle( 'Édition de l\'utilisateur '.$user->login );
				$this->setVar(array(
					'user'				=> $user,
				));
				$e = $this->displayContent('users/edit');
			break;
			default:
				$this->noCtrlFilter=true;	
				$this->setVar('list',  User::getCollection() );
				$e = $this->displayContent('users/list');
		}
	}

	protected function query(){
		switch($this->query){
			case 'setUser':
				$user = new User( $this->data['id']);
				if ($this->data['passwd']!==$user->getPasswd()) $this->data['passwd'] = md5($this->data['passwd']);
				$r = $user->setValues($this->data);
				$this->setResponse( $r==true, $r);
				break;
			case 'deleteUser':
				break;
		}
	}
}
?>