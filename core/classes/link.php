<?php
namespace Auwa;
class Link{
	public static $_instance;
	public function __construct(){}


	/**
	 * Get a Link
	 *
	 * @param	string		$name		Name of the link base (page, ...)
	 *
	 * @return	string					the link
	 */
	public function getLink($name=null, $type=null, $allLink=true, $ctrl=_CURRENT_CTRL_, $iso_lang=_CURRENT_LANG_, $retrieve=true){
		$name = trim($name);
		if (empty($name)) $name='getHome';
		switch ($type) {
			case 'Link':
				return $name;
			break;
			default:
				// default : it is a page link
				/*
						WARNING !!!

						Mettre la base du controller sélectionné dans l'attribut base de l'item de menu
				*/
				$pre = ($allLink) ? Auwa::url().(_MULTI_LANG_ ? $iso_lang.'/' :'') : '';
				$url = $retrieve ? Page::getPageRewrite($name, $iso_lang, $ctrl) : $name;
				return $pre.$url ;
				break;
		}
	}

	public static function get($rewrite=null, $type=null, $allLink=true){
		return Link::$_instance->getLink($rewrite, $type, $allLink);
	}

}
Link::$_instance = new Link();
?>