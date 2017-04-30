<?php
namespace Auwa;
/**
 * Auwa Core Controller 
 *
 * @package Auwa \controllers\
 * @copyright 2017 AuroraN
 */
 
/**
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class DefaultCoreController extends CoreController{

	private static $i = 0;
	private static $temp = false;

	public function main(){
		$coreUser = User::isCoreUser();
		$this->addJs('jquery.Jcrop.min', _CORE_JS_DIR_	);
		$this->addJs('fadata', _FW_DIR_.'eff/js/');
		$this->insertHeaderTab(array(
			'icon'=>'',
			'class'=>'fa fa-lock Disconnect'
		));
		if (_Auwa_ROOT_CONNECTED_)
			$this->insertHeaderTab(array(
				'icon'=>'',
				'id'=>'updateAuwa',
				'class'=>'fa fa-refresh'
			));
		$this->insertHeaderTab(array(
			'icon'=>'',
			'class'=>'fa fa-user UserAccount'
		));
		$m = \ConfigFile::getConfig('config/modules');
		if(isset($m['topLink'])){
			foreach ($m['topLink'] as $key => $values) {
				$this->insertHeaderTab($values);
			}
		}
		if ($coreUser){
			$this->setHeader('Auwa Core', 'Administration');
			$this->setTitle ('AuwaCore');
			$this->setVar('user', User::getMainConnection());
		} else {
			$this->addJs('login', _CORE_JS_DIR_	);
			$this->setTitle ('AuwaCore: Identifiez-vous');
			$this->setHeader('Auwa Core', 'Connexion');
		}
		
		$e = $this->displayContent($coreUser?'default':'login');
		if (Check::isError($e)) $e->displayErrors();
	}

	public function action(){
		switch ($this->action) {
			case 'navigator':
				$this->setTitle(Tools::getValue('title'));
				$shortPath = str_replace(_DATA_DIR_,'',Tools::getValue('path'));
				$path = _DATA_DIR_.$shortPath;
				$filetype = Tools::getValue('filetype');
				$blockDirectory = Tools::getValue('blockDirectory');
				$preselected = isset($_POST['preselected']) ? $_POST['preselected'] : null;
				$list = array( 'Errors'=>array(), 'Folders'=>array(), 'Documents'=>array(), 'Pictures'=>array(), 'Videos'=>array() );
				$dir = opendir($path);
				if (!$dir) $list['Errors'][] = array('name'=>'Folder not found : '.$path);
				$dirs = array();
				$files= array();
				$img= array();
				clearstatcache();
				while ($f = readdir($dir)) {	
					 if(is_dir($path.$f) && $f!="." && $f!="..") 
						$list['Folders'][] = array('name'=>$f, 'path'=>$shortPath.$f);
					else {
						$s = stat($path.$f);
						$d = $s[9];
					}
					 if(is_file($path.$f) && !preg_match('/^(\.|\.\.)/',$f) && preg_match('/\.(html|tpl)$/', strtolower($f)) && ($filetype=='document' || empty($filetype)))
						$list['Documents'][$d] = array('name'=>$f, 'path'=>$shortPath.$f);
					 if(is_file($path.$f) && !preg_match('/^(\.|\.\.)/',$f) && preg_match('/\.(png|jpg|jpeg|tiff|tif|gif)$/', strtolower($f) ) && ($filetype=='picture' || empty($filetype)))
						$list['Pictures'][ isset($list['Pictures'][$d]) ? $s[1] : $d] = array('url'=>'../data/'.$shortPath.$f, 'name'=>$f,'path'=>$shortPath.$f);
					 if(is_file($path.$f) && !preg_match('/^(\.|\.\.)/',$f) && preg_match('/\.(avi|mov|mkv|mpg|mpeg|mp4|ogv)$/', strtolower($f) ) && ($filetype=='movie' || empty($filetype)))
						$list['Videos'][$d] = array('name'=>$f,'path'=>$shortPath.$f);
				}
				foreach ($list as $key => $type) {
					krsort($list[$key]);
				}
				$this->setVar( array(
						'typeList'=> $list,
						'filetype'=> $filetype,
						'blockDirectory' => false, // i think it will be removed in the future
						'path_e'=> explode('/',$shortPath),
						'shortPath'=> $shortPath,
						'path_f' => $path,
						'cutfile' => Tools::getValue('fileaction')=='cut' ? Tools::getValue('filetarget') : '',
					));
				$this->includeTemplate('nav');
			break;
			case 'translations':
				$this->addCss('translations');
				$this->addJs('translations');
				Translation::checkTranslations();
				$this->setTitle('Traductions');
				$config = \ConfigFile::getConfig('config/translations');
				ksort($config);
				$this->setVar(array(
					'config'=> $config,
					'iso_ref'=> _DEFAULT_LANG_,
				) );
				$this->includeTemplate('translations');
				break;

			// update

			// Updates
			case 'checkUpdate':
				if (!_Auwa_ROOT_CONNECTED_) $this->setResponse(false, 'Action refusée');
				$this->setTitle('Utilitaire de mise-à-jour' );
				if (!is_dir(_CORE_DIR_.'releases/')) @touch(_CORE_DIR_.'releases/');
				@touch(_CORE_DIR_.'releases/update.json');
				$auwa = self::getRelease('Auwa');
				$release = (!is_object($auwa) && isset($auwa[0])) ? $auwa[0] : false;
				$r_auwa = Session::get()->AuwaVersion == $release->tag_name;
				$coreSettings = \ConfigFile::getConfig('config/core');
				$coreModules = array();
				/*foreach ($coreSettings['Panel'] as $key => $tab) {
					foreach ($tab as $item) {
						if (isset($item['module']) && $item['module']!==false){
							$r = self::getRelease('AuwaCoreModule-'.$item['module']);
							$m = \ConfigFile::getConfig('modules/'.$item['module'].'/module');
							if (!is_object($r) && isset($r[0]) && isset($m['version']) && $r[0]->tag_name!==$m['version']){
								$coreModules[$item['module']] = array(
									'version' => $r[0]->tag_name,
									'prerelease' => $r[0]->prerelease,
									'archive' => 'https://github.com/auroran/Auwa/archive/AuwaCoreModule-'.$item['module'].'.zip',
								);
							}							
						}
					}
				}*/
				$this->setVar(array(
					'release'	=> !$r_auwa ? $release : true,
					'm_releases'=> $coreModules
				));
				$this->displayContent('updates');
				break;
		}
	}
	public function query(){

		switch ($this->query) {
			case 'setCurrentController':
				$this->session->currentSelectedController = $this->data;
				$this->setResponse(true, $this->session->currentSelectedController ) ;
				break;
			
			case 'writeDataURL':
				$dataURL = $this->data['dataURL'];
				$ext = isset($this->data['ext']) ? $this->data['ext'] : '.jpg';
				$parts = explode(',', $dataURL);
				$data = base64_decode($parts[1]);
				$path = _DATA_DIR_.rawurldecode( $this->data['file'] );
				$filename = explode('/',$path);
				$r = @file_put_contents($path.$ext,$data);
				$this->setResponse($r!==false, array('full'=>$path.$ext,'filename'=>$filename[ count($filename)-1 ] )) ;
				break;
			case 'fileRemove':
				$file = trim($this->data['file']);
				$path = !isset($this->data['abspath']) ? _DATA_DIR_ : $this->data['abspath'];
				$this->setResponse( unlink($path.$file), 'La suppression à échouée' );
				break;
			case 'fileCopy':
				$path = !isset($this->data['abspath']) ? _DATA_DIR_ : $this->data['abspath'] ;
				$destination = trim(implode("",explode("\\",$this->data['destination'])));
				$source = trim(implode("",explode("\\",$this->data['source'])));
				$file = trim($this->data['file']);
				$newfile = $file;
				$action = $this->data['fileaction'];				
				switch ($action) {
					case 'rename':
						$newfile = $this->data['newfile'];
					case 'copy':
					case 'cut':
						$r = self::copy($path.$source.$file, $path.$destination.$newfile, $action);
						break;
					default:
						$r = array('error'=>true, 'msg'=>'Action invalide');;
						break;
				}
				$this->setResponse($r['error']!==false, $r['msg']);
				break;
			case 'createDirectory':
				$r = array(
					'result'=> false,
					'error'=> 'Parameters missings',
				);
				if ($this->data['name'] && $this->data['parent']){
					$parent = $this->data['parent'];
					$name = $this->data['name'];
					if ( is_dir(_DATA_DIR_.$parent)){
						$r['result'] = @mkdir(_DATA_DIR_.$parent.$name.'/');
						if (!$r['result']){
							$r['error'] = "Impossible de créer un dossier dans ce répertoire";
						}
					} else {
						$r['error'] = "Le répertoire parent n'existe pas";
					}
				}
				$this->setResponse($r['result']!==false, $r['error']);
				break;
			// Translations
			case 'saveTranslations':
				$data = $this->data['translations'];
				if (!is_array($data)) return $this->setResponse(false, "Données au mauvais format");
				$r = \ConfigFile::setConfig('config/translations', $data, true); // save new translations
				$this->setResponse($r ? true : false, !$r ? "Erreur dans la sauvegarde des traductions" : "Traductions sauvegardées");
				break;

			// Updates
			case 'installUpdate':
				if (!_Auwa_ROOT_CONNECTED_) $this->setResponse(false, 'Action refusée');
				$r = $this->data['release'];									// release name
				$t = $this->data['target'];										// release type (module or auwa)
				$m = str_replace('AuwaCoreModule-', '', $t);					// module name
				$l = _CORE_DIR_.'releases/update.json';							// log file
				$p = _CORE_DIR_.($t=='Auwa' ? '' : "modules/$m/").'releases/';	// path to release dir
				$d = $t=='Auwa' ? _ROOT_DIR_ : _CORE_DIR_.'modules/'.$r;			// destination
				$f = $p.$r.'.zip';												// release archive
				$s = "https://github.com/auroran/$t/archive/$r.zip";			// remote release archive url
				self::setLog("Téléchargement de l'archive", false, $l);
				if (!is_dir($p)) @mkdir($p);

				$remote = fopen($s, 'r');
				if( $remote ) {
					$local = fopen($f, 'w');
					$read_bytes = 0;
					while(!feof($remote)) {
						$buffer = fread($remote, 2048);
						fwrite($local, $buffer);
					}
					fclose($local);
				} else {
					$this->setResponse(false, 'Archive introuvable');
					return;
				}
				fclose($remote);

				$zip = new \ZipArchive;
				if ($zip->open( $f) ===  true) {
					$n = $zip->numFiles;
					self::setLog("Extration de l'archive", false, $l);
					$res = $zip->extractTo($p);
					$zip->close();
					@unlink($f);
					$updDir = $p.preg_replace('/^v/', $t.'-', $r);var_dump($updDir);die();

					self::setLog('Création d\'une copie de sauvegarde', 0, $l);
					self::$i=0;;
					$nb = 0;
					self::copy(_ROOT_DIR_, '', 'list', false, $nb);
					@unlink($p.'backup.zip');
					$ressource = new \ZipArchive();
					$ressource->open( $p.'backup.zip', \ZipArchive::CREATE);
					self::$i=0;
					$res = self::copy($d, '', 'zip', array(
						'action'=> 'Création d\'une copie de sauvegarde',
						'nb'=>$nb,
						'i'=>0,
						'file'=>$l
					),$ressource);
					$resR = $ressource->close();
					if (!$resR) {
					    $res = array('error'=>1, 'msg'=>'Impossible de créer l\'archive');
					}
					$this->setResponse($r['error']==0, $r['msg']);
					self::setLog('Copie des fichiers', 0, $l);
					self::$i=0;
					$res = self::copy($updDir, $d, 'copy', array(
						'action'=> 'Copie des fichiers',
						'nb'=>$n,
						'i'=>0,
						'file'=>$l
					));
					self::copy($updDir, false, 'remove');
					$this->setResponse($res['error']==0, $res['msg']);
				} else {
					$this->setResponse(false, 'Erreur dans l\'extraction de l\'archive');
				}
				break;
		}
	}

	private static function getRelease($repo){
		$ch = curl_init("https://api.github.com/repos/Auroran/$repo/releases"); // such as http://example.com/example.xml
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
		$data = json_decode( curl_exec($ch) );
		curl_close($ch);
		return $data;
	}

	private static function setLog($action, $status, $file, $desc=null){
		$status = array(
			'action' => $action,
			'status' => $status,
			'desc' => $desc
		);
		file_put_contents($file, json_encode($status));
	}

	private static function copy($source, $destination, $action, $log=false, &$ressource=false){
		$result = array('error'=>0, 'msg'=>'');
		$r = true;
		if ( is_file($source) && !preg_match('/backup.zip$/', $source) ){
			switch ($action) {
				case 'copy':
					$r = @copy($source, $destination);
					break;
				case 'move':
					$r = @rename($source, $destination);
					break;
				case 'remove':
					$r = @unlink($source, $destination);
					break;
				case 'list':
					$ressource++;
					break;
				case 'zip':
					$destination = preg_replace('/^\//','',$destination);
					$r = $ressource->addFromString( $destination, file_get_contents($source) );
					if ($r===false){
						$result = array('error'=>1, 'msg'=>'Backup | Fichier non ajouté : '.$destination);
					}
					break;
				default:
					return array('error'=>1, 'msg'=>'Action non valable');
					break;
			}
			if (!$r && $result['error']==0){
				$result = array('error'=>1, 'msg'=>'La manipulation a échouée');
			}			
			self::$i++;
			if ($log){;
				self::setLog($log['action'], $log['nb'] ? round(100*self::$i/$log['nb']) : false, $log['file'], $source);
			}
			return $result;
		}
		if (is_dir($source) && !preg_match('/\/data$|\/config$|\/fonts$|\/modules$|\/themes$|\/releases$/', $source)){
			// read dir and copy/move each files and folders
			$dir = opendir($source);
			if (!is_dir($destination) && $action!=='zip' && $action!=='remove') @mkdir($destination);
			if (!$dir) {
				return array('error'=>1, 'msg'=>'Impossible d\'ouvrir le répertoire');
			}
			while ($f = readdir($dir)) {	
				if($f!="." && $f!="..") {								
					$r = self::copy($source.'/'.$f, $destination.'/'.$f, $action, $log, $ressource);
					if ( $r['error']==1 ) return $r;
				}
			}
			if ($action=='move'){
				$r = @rmdir($source);
				return array('error'=>$r?0:1, 'msg'=>!$r ? 'Erreur dans le déplacement' : '');
			}
		}
		return $result;
	}

}
?>