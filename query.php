<?php
	namespace Auwa;
	// forward POST DATA in rewritted URL
	// transmitted data will be distroy from session after redirection
	if (empty($_POST)) exit('<p style="color:#bb3333">Auwa Queries needs POST requests</p>');
	$no_rw=true;
	include ('core/core.php');
	if (isset($_FILES) && !empty($_FILES)){
		$forbidden = array('.php', '.html', '.js', '.txt');
		if (!is_dir(_ROOT_DIR_.'temp/')) @mkdir(_ROOT_DIR_.'temp/');
		$_POST['file_upload']=array();
		foreach ($_FILES as $file) {
			$extension = strrchr($file['name'], '.');
			if (!in_array($extension, $forbidden)){ // faire check si php/html/js par mime
				move_uploaded_file($file['tmp_name'], _ROOT_DIR_.'temp/'.$file['name']);
				$_POST['file_upload'][] = array('path'=>_ROOT_DIR_.'temp/'.$file['name'], 'name'=>$file['name']);
			}
		}
	}
	Session::get()->POST= $_POST;
	Tools::redirectToAuwa(Tools::getValue('cu',"post"));
?>