<?php
namespace Auwa;
/**
 * URL Rewritting Tool
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */

/**
 * Give static methods to decode simply url
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */ 
class Rewriting{

	/*
	 * Rewrite rules
	 * @var array
	 */
	public static $rewriteRules = array();

	/**
	 * Parse Url to set request variable (like Auwacontroller, module, controller)
	 *
	 * @param	string	$input	Url to parse
	 */
	public static function parse( $input ){
		if ( preg_match('/\.([a-z]{2,3})$/', $input) ) die(); // it is a file
		//Session::get()->_set('isCore',false);
		if (preg_match('/core\//', $input)) {
			Session::get()->_set('isCore',true);
			define('_REWRITE_TARGET_', "../");
			return;
		}
		if (!$input) return;


		if (_LOCAL_DIR_) $input =str_replace('/'._LOCAL_DIR_,'',$input);
		$input =preg_replace('/^\//','',$input); // in case of the server add a '/' 
		define('_INIT_URL_', 'http://'._BASE_URL_. preg_replace('/^\//','',$input) );
		$params = explode('/', $input);
		if (count($params)<1) return;
		$_GET['multilang'] = _MULTI_LANG_INIT_;
		// on va étudier l'adresse, récupérer les variables et remplir les variables 
		self::$rewriteRules = \ConfigFile::getConfig('config/rewriting');
		//$modules = Module::autoLoad('ghost'); // PBBBBB des fois!
		$findMatch = false;
		
		// param1
		$ctrl_use=false;
		$mod_use=false;
		$page_use=false;
		$next=false;
		$lang_use = true;
		
		$status_info = "200 OK";
		$status_code = 200;

		$forceDefaultRule = false;
		$lastrule=null;
		
		$langs = \ConfigFile::getConfig('config/lang');
		$params = self::retrieveLang($params);
		$rules= self::$rewriteRules;
			
		// get default rules
		$defaultRules = array();
		if ( isset( $rules['defaultRules'] ) && isset( $rules['defaultRules'][count($params)] ) )
			foreach ($rules['defaultRules'][count($params)] as $key => $var)
				if (!empty($params[$key])) $defaultRules[$var] = $params[$key];	

		unset($rules['defaultRules']);
		// get specifics rules
		$lastrule = false;
		$input_rr = implode('/', $params);
		foreach ($rules as $title => $values) {
			$rule = trim( str_replace('/','\/',$values['filter']) );	
			if ( preg_match('/^\/'.$rule.'/', '/'.$input_rr )   
				&& (count_chars($rule)>count_chars($lastrule) )) {
					$lastrule = $title;
				}
		}
		if ($lastrule){
			$values = $rules[$lastrule];
			if (isset($values['session'])){
				Session::$name = $values['session'];
				unset($values['session']);
			}
			// Modification du code retour, 
			// pour que les moteurs de recherche indexent nos pages 
			$_GET['Auwa_title_replacement'] = $lastrule;
			$status = (isset($values['status'])) ? $values['status'] : 200;
			switch($status){
				case 303 :
					$status_info = "Moved Permanently";
					$status_code = 303;
					break;
				default:
					$status_info = "200 OK";
					$status_code = 200;
			}

			foreach( $values as $key=>$part){
				if ($key!=='filter' && $key!=='status') $_GET[ $key ] = $part;
				if ($key=='AuwaController') {
					$forceDefaultRule = true;
				}
				if ($key=='lang' && $_GET['lang']!==false && $part!==$_GET['lang'] && $_GET['multilang']) {
					Tools::redirect( Auwa::url().$part.preg_replace('/\/$/', '',  $input_rr ) );
				}
				if ($key=='vars' ) {
					$defaultRules = array();
					foreach ($part[count($params)] as $key => $var) {
						if (!empty($params[$key])) $defaultRules[$var] = $params[$key];
					}
				}
			}
			$_GET['urlRule'] = $lastrule;
		}
		// apply rules
		foreach ($defaultRules as $key => $var) {
			$_GET[$key] = $var;
		}
		if(isset(Session::get()->POST['theme'])) {
			$_GET['theme']=Session::get()->POST['theme'];
		} 
		Session::get()->_set('isCore',false);
		Session::get()->_set('current_url',_INIT_URL_);
		//var_dump($_GET); die();
		// language detection
		$params = self::checkLang($params,$input_rr);

		$input_rr= preg_replace('/\/'.$lastrule.'/','',$input_rr);
		$input_rr= preg_replace('/\/$/','',$input_rr);
		$params = explode('/', $input_rr);
		define('_URL_PARTS_', $params);
		$_GET['urlPart'] = $params;
		Session::get()->_set('urlParts', $params);

		/*if (!isset($_GET['page'])) // by default, the first param is the page if not set into defaultRules.
			$_GET['page'] = $params[0]; */
		//header("Status: ".$status_info, false, $status_code);
		
		if (class_exists('HTTP2'))
			\HTTP2::setHeader(':status','200'); 
		else
			header("Status: ".$status_info, false, $status_code);
	}

	public static function retrieveLang($params){
		//$params = Tools::clearNumericArray($params);
		$langs = \ConfigFile::getConfig('config/lang');
		$down = false;

		$_GET['lang']=false;
		$_GET['isLang']= self::checkIsoLang($params[0]);
		$_GET['needRedirect'] = !$_GET['isLang'];
		if($_GET['isLang']){
			// could be iso code
			if ( isset($langs[$params[0]]) && $langs[$params[0]]['enable']  ) {
				// we can use this lang
				$_GET['lang']= $params[0];
			} else {
				// this lang is not found into config or it is not enable
				if ( !isset($langs[$params[0]]) || !$langs[$params[0]]['enable']  ) 
					$_GET['lang'] = _DEFAULT_LANG_;
					$_GET['needRedirect'] = true;
			}
			// we akk to cut the url
			$down = true;
			unset($params[0]);	
			$params = Tools::clearNumericArray($params);
		}
		//Session::get()->__set('current_lang', $_GET['lang']);		
		$input_rr = implode('/', $params);
		foreach ($params as $key => $value) {
			if (empty($value)) unset($params[$key]);
		}
		$params = array_values ($params);
		return $params;
	}

	private static function checkLang($params=null,$input_rr=null){
		
		if ($_GET['multilang']==false) $_GET['lang'] = _DEFAULT_LANG_; // only one lang
		else {
			if ($_GET['lang']==false) $_GET['lang'] = Session::get()->current_lang;
			if ($_GET['lang']==false) {
				$cur_local = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
				// else fill with the default lang set
				$_GET['lang'] = !empty($cur_local) ? \Locale::getPrimaryLanguage($cur_local) : _DEFAULT_LANG_;
			}
		}		

		Session::get()->__set('current_lang',$_GET['lang']);
		//var_dump($_GET); die();
		// if the site should use only one language
		if ( $_GET['multilang']===false  && $_GET['isLang']){
			$url_r = Auwa::url().preg_replace('/\/$/','',preg_replace('/^\//','',$input_rr));
			Tools::redirect($url_r);
		}
		// if we should redirect with the good lang (and if multi-language is enable)
		if ( $_GET['needRedirect'] && $_GET['multilang']) {
				$url_r = Auwa::url().$_GET['lang'].'/'.preg_replace('/\/$/','',$input_rr) ;
				Tools::redirect($url_r);
		}
		unset($_GET['needRedirect']);
		unset($_GET['isLang']);
	}

	public static function checkIsoLang($iso){
		$iso_codes = array(
	        'en' => 'English' , 
	        'aa' => 'Afar' , 
	        'ab' => 'Abkhazian' , 
	        'af' => 'Afrikaans' , 
	        'am' => 'Amharic' , 
	        'ar' => 'Arabic' , 
	        'as' => 'Assamese' , 
	        'ay' => 'Aymara' , 
	        'az' => 'Azerbaijani' , 
	        'ba' => 'Bashkir' , 
	        'be' => 'Byelorussian' , 
	        'bg' => 'Bulgarian' , 
	        'bh' => 'Bihari' , 
	        'bi' => 'Bislama' , 
	        'bn' => 'Bengali/Bangla' , 
	        'bo' => 'Tibetan' , 
	        'br' => 'Breton' , 
	        'ca' => 'Catalan' , 
	        'co' => 'Corsican' , 
	        'cs' => 'Czech' , 
	        'cy' => 'Welsh' , 
	        'da' => 'Danish' , 
	        'de' => 'German' , 
	        'dz' => 'Bhutani' , 
	        'el' => 'Greek' , 
	        'eo' => 'Esperanto' , 
	        'es' => 'Spanish' , 
	        'et' => 'Estonian' , 
	        'eu' => 'Basque' , 
	        'fa' => 'Persian' , 
	        'fi' => 'Finnish' , 
	        'fj' => 'Fiji' , 
	        'fo' => 'Faeroese' , 
	        'fr' => 'French' , 
	        'fy' => 'Frisian' , 
	        'ga' => 'Irish' , 
	        'gd' => 'Scots/Gaelic' , 
	        'gl' => 'Galician' , 
	        'gn' => 'Guarani' , 
	        'gu' => 'Gujarati' , 
	        'ha' => 'Hausa' , 
	        'hi' => 'Hindi' , 
	        'hr' => 'Croatian' , 
	        'hu' => 'Hungarian' , 
	        'hy' => 'Armenian' , 
	        'ia' => 'Interlingua' , 
	        'ie' => 'Interlingue' , 
	        'ik' => 'Inupiak' , 
	        'in' => 'Indonesian' , 
	        'is' => 'Icelandic' , 
	        'it' => 'Italian' , 
	        'iw' => 'Hebrew' , 
	        'ja' => 'Japanese' , 
	        'ji' => 'Yiddish' , 
	        'jw' => 'Javanese' , 
	        'ka' => 'Georgian' , 
	        'kk' => 'Kazakh' , 
	        'kl' => 'Greenlandic' , 
	        'km' => 'Cambodian' , 
	        'kn' => 'Kannada' , 
	        'ko' => 'Korean' , 
	        'ks' => 'Kashmiri' , 
	        'ku' => 'Kurdish' , 
	        'ky' => 'Kirghiz' , 
	        'la' => 'Latin' , 
	        'ln' => 'Lingala' , 
	        'lo' => 'Laothian' , 
	        'lt' => 'Lithuanian' , 
	        'lv' => 'Latvian/Lettish' , 
	        'mg' => 'Malagasy' , 
	        'mi' => 'Maori' , 
	        'mk' => 'Macedonian' , 
	        'ml' => 'Malayalam' , 
	        'mn' => 'Mongolian' , 
	        'mo' => 'Moldavian' , 
	        'mr' => 'Marathi' , 
	        'ms' => 'Malay' , 
	        'mt' => 'Maltese' , 
	        'my' => 'Burmese' , 
	        'na' => 'Nauru' , 
	        'ne' => 'Nepali' , 
	        'nl' => 'Dutch' , 
	        'no' => 'Norwegian' , 
	        'oc' => 'Occitan' , 
	        'om' => '(Afan)/Oromoor/Oriya' , 
	        'pa' => 'Punjabi' , 
	        'pl' => 'Polish' , 
	        'ps' => 'Pashto/Pushto' , 
	        'pt' => 'Portuguese' , 
	        'qu' => 'Quechua' , 
	        'rm' => 'Rhaeto-Romance' , 
	        'rn' => 'Kirundi' , 
	        'ro' => 'Romanian' , 
	        'ru' => 'Russian' , 
	        'rw' => 'Kinyarwanda' , 
	        'sa' => 'Sanskrit' , 
	        'sd' => 'Sindhi' , 
	        'sg' => 'Sangro' , 
	        'sh' => 'Serbo-Croatian' , 
	        'si' => 'Singhalese' , 
	        'sk' => 'Slovak' , 
	        'sl' => 'Slovenian' , 
	        'sm' => 'Samoan' , 
	        'sn' => 'Shona' , 
	        'so' => 'Somali' , 
	        'sq' => 'Albanian' , 
	        'sr' => 'Serbian' , 
	        'ss' => 'Siswati' , 
	        'st' => 'Sesotho' , 
	        'su' => 'Sundanese' , 
	        'sv' => 'Swedish' , 
	        'sw' => 'Swahili' , 
	        'ta' => 'Tamil' , 
	        'te' => 'Tegulu' , 
	        'tg' => 'Tajik' , 
	        'th' => 'Thai' , 
	        'ti' => 'Tigrinya' , 
	        'tk' => 'Turkmen' , 
	        'tl' => 'Tagalog' , 
	        'tn' => 'Setswana' , 
	        'to' => 'Tonga' , 
	        'tr' => 'Turkish' , 
	        'ts' => 'Tsonga' , 
	        'tt' => 'Tatar' , 
	        'tw' => 'Twi' , 
	        'uk' => 'Ukrainian' , 
	        'ur' => 'Urdu' , 
	        'uz' => 'Uzbek' , 
	        'vi' => 'Vietnamese' , 
	        'vo' => 'Volapuk' , 
	        'wo' => 'Wolof' , 
	        'xh' => 'Xhosa' , 
	        'yo' => 'Yoruba' , 
	        'zh' => 'Chinese' , 
	        'zu' => 'Zulu' , 
        );
		return array_key_exists($iso, $iso_codes);
	}
}
?>