<?php
	class menuAdminModuleAdmin extends Auwa\Module{
		public function __construct(){
			$this->name = 'menuAdmin';
			$this->author = 'Grégory GAUDIN';
			$this->_path = _SYS_MOD_DIR_.'menuAdmin/';
			return $this->e;
		}
	}
?>