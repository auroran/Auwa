<?php
namespace Auwa;
/**
 * Auwa Core Controller 
 *
 * Auwa use this CoreController to manage login
 *
 * @package Auwa \controllers\
 * @copyright 2016 AuroraN
 */

 
/**
 * Controller LOGIN for Auwa Core
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class CoreLoginController extends CoreController{

	public function init(){
		if ( User::isCoreUser() ) Tools::redirectToAuwa('core/');
		return;
	}

	public function query(){

		if ($this->query=='coreConnexion'){
			// connexion
			$this->setVar('loginErrors', false);
			// due to a redirection, Tools doesn"t work => manual checking
			$user = Check::sanitize($_POST['data']['user']);
			$passwd = Check::sanitize($_POST['data']['passwd']);
			$r = User::connect($user, $passwd, true);
			if ($r===true && !is_string($r)) $this->setResponse(true,true);
			else $this->setResponse(false,Error::getErrMsg($r) );
		}
		if ($this->query=='chgPasswd'){
			$u = new User( $this->data['id'] );
			$r = $u->set('passwd', md5($this->data['pwd']));
			$this->setResponse($r, $r);
		}
		if ($this->query=='disconnect'){
			User::disconnect();
			$this->setResponse(true, true );
		}
	}
}
?>