<?php
namespace Auwa;
include_once(_FW_DIR_.'simple_html_dom.php');
/**
 * Tools for help to develop application
 * And increase variables treatment
 *
 * @package Auwa \core\classes\
 * @copyright 2015 AuroraN
 */

 
/**
 * Include tirty-part library to use Json
 */
 include_once(_CORE_CLASSES_DIR_.'JSON.php');

/**
 * Give usefull static methods
 *
 * - Inclusion and rewriting of template
 * - Get and secure request variables
 * - Encryption
 * - Transcryption
 * - Date tools
 * - Random tolls
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class Tools{
	private static $temp;
	public static $extendsRules = array('patterns'=>array(), 'replaces'=>array());
	
	
	/**
	 * Get the translation of a text for a specified controller
	 *
	 * @param	string	$txt		Text to translate
	 * @param	string	$c			Category of the translation (used by the core UI)
	 * @param	string	$iso_lang	Requiered language
	 *
	 * @return	string				Text translated
	 */
	public static function translate($txt, $c, $iso_lang=_CURRENT_LANG_){
		return Translation::getTranslation($txt, $c, $iso_lang);
	}
	/**
	 * Get the translation of a text for all controller
	 *
	 * @param	string	$txt		Text to translate
	 * @param	string	$c			Category of the translation (used by the core UI)
	 * @param	string	$iso_lang	Requiered language
	 *
	 * @return	string				Text translated
	 */
	public static function translateForAll($txt, $c, $iso_lang=_CURRENT_LANG_){
		return Translation::getTranslation($txt, $c, $iso_lang, false);
	}

	/**
	 * Get the value of a template variable stored into the session
	 *
	 * @param	string	$var 		Name of the variable
	 *
	 * @return	mixed				Value of the variabe
	 */
	private static function getVarValue($var){
		if( Auwa::get()->tplVars[$var] )
			return Auwa::get()->tplVars[$var];
		else if (Session::get()->{$var})
			return Session::get()->{$var};
		return '';
	}

	/**
	 * Translate common variable used into Auwa template files
	 *
	 * @param	string	$code		Variable code
	 *
	 * @return	string				Real variable which will be interpreted
	 */
	public static function replaceVars($code){
		self::$temp = $code;
		$pc = array(
			'/{\$([^}]+)}/' => function($v){ 					// Display a variable	
				self::$temp = str_replace($v[0], self::getVarValue($v[1]), self::$temp ); 
			} ,
			'/{url\:([^}]+)}/' => function($v){  				// Display a url (data, img, css, js) 
				$f = $v[1].'_url';
				self::$temp = str_replace($v[0], Auwa::$f(), self::$temp ); 
			},
			'/{url}/' => function($v){ 							// Display base URL
				self::$temp = str_replace($v[0], Auwa::display_url(), self::$temp ); 
			},
		);
		if (function_exists('preg_replace_callback_array')){ // in case of no php7
			preg_replace_callback_array($pc, $code);
		} else {
			foreach ($pc as $key => $fn) {
				preg_replace_callback($key, $fn, $code);
			}
		}
		$code = self::$temp ;
		return $code;
	}

	/**
	 * Add a new template variable translation to Auwa
	 *
	 * @param	mixed	$patterns	String of a variable code / Array of some variable codes
	 * @param	string	$replaces	String of a variable translation / Array of some variable translations
	 *
	 */
	public static function addTplRules($patterns, $replaces){
		Template::addTplRules($patterns, $replaces);
	}
	
	/**
	 * Define the page to load  as BLANK
	 */
	public static function setBlankPage(){
		$_POST['page'] = 'blank';
	}
	
	/**
	 * Get and sanitize a request variable
	 *
	 * @param 	string	$var	Name of the variable
	 * @param	string	$from	Request  type (GET, POST, BOTH)
	 *
	 * @return 	mixed	$v		Value of the variable
	 */
	public static function getValue($var=null, $from='all'){
		if ($var===null) return false;
		$v = false;
		if (($from=='all' or $from=='get') && array_key_exists( $var, $_GET )) $v = $_GET[$var];
		if (($from=='all' or $from=='post' ) && array_key_exists( $var,$_POST)) $v = $_POST[$var];
		if ($v!==false && !User::isCoreUser()) return Check::sanitize($v);
		return $v;
	}
	

	/**
	 * Set and sanitize a GET variable
	 *
	 * @param 	string	$var	Name of the variable
	 * @param	string	$from	Request  type (GET, POST, BOTH)
	 *
	 * @return 	mixed	$v		Value of the variable
	 */
	public static function setValue($var=null, $value=false){
		$_GET[$var] = Check::sanitize($value);
	}
	/**
	 * Get and sanitize all request variables
	 *
	 * @param 	string	$var	Request type (GET, POST by default)
	 *
	 * @return 	array	$v		Array of each variables
	 */
	public static function getValues($var=null){
		$v = false;
		if (strtolower($var)=='post' || $var==null) $v = ($_POST); // faire un traitement des val
		if (strtolower($var)=='get') $v = ($_GET);
		if ($v!==false && is_array($v) && !User::isCoreUser() ){
			foreach($v as $key=>$value)
				$v[$key] = Check::sanitize($value);
		}
		return $v;
		return false;
	}

	/**
	 * Retrieve POST data from the session (used after a POST request via query.php
	 */
	public static function retrievePostData(){
		if ( is_array(Session::get()->POST) ){
			$_POST = Session::get()->POST;
			Session::get()->_remove('POST');
		}
	}

	/**
	 * Make an internal redirection
	 */
	public static function redirectToAuwa( $rel_url, $admin=false ){
		Session::get()->POST = $_POST;
		header('location:'.Auwa::url().( $admin ? 'core/':'' ).$rel_url);
	}
	/**
	 *  Redirect to an url
	 *
	 * @param 	string	$url 	Targeted url
	 *
	 */
	public static function redirect($url){
		if ($url==_INIT_URL_) return; // to avoid bad redirection
		header('location:'.$url);
		exit;
	}
	
	/**
	 * Slash echapment
	 *
	 * @param 	string	$var	Variable to sanitize
	 *
	 * @return 	string	$v		Variable after traitment
	 */
	public static function stripslashes_r($var){
		if(is_array($var)){ // Si la variable passée en argument est un array, on appelle la fonction stripslashes_r dessus
		        return array_map('stripslashes_r', $var);
		}else{ // Sinon, un simple stripslashes suffit
		        return stripslashes($var);
		}
	}

	/**
	 * Encrypt data
	 *
	 * @param 	string	$data	Variable to encrypt
	 * @param 	bollean	$data	set if the url is to the core or not
	 *
	 * @return 	string			Variable encrypted
	 */
	public static function encryptDatas($data){
		$key="passphrase";
		$key = md5($key);
		$letter = -1;
		$newstr = '';
		$strlen = strlen($data);
		for($i = 0; $i < $strlen; $i++ ){
			$letter++;
			if ( $letter > 31 )	$letter = 0;
			$neword = ord($data{$i}) + ord($key{$letter});
			if ( $neword > 255 )	$neword -= 256;
			$newstr .= chr($neword);
		}
		return base64_encode($newstr);
	}
	
	/**
	 * Decrypt data
	 *
	 * @param 	string	$data	Variable to decrypt
	 *
	 * @return 	string	$newstr	Variable decrypted
	 */
	public static function uncryptDatas($datas){
		$key="passphrase";
		$key = md5($key);
		$letter = -1;
		$newstr = '';
		$datas = base64_decode($datas);
		$strlen = strlen($datas);
		for ( $i = 0; $i < $strlen; $i++ ){
			$letter++;
			if ( $letter > 31 )	$letter = 0;
			$neword = ord($datas{$i}) - ord($key{$letter});
			if ( $neword < 1 )	$neword += 256;
			$newstr .= chr($neword);
		}
		return $newstr;
	}

	/**
	 * Genere a random string id or reference
	 *
	 * @param 	int		$car		Length od output
	 *
	 * @return	string	$string		Output
	 */
	public static function random($car) {
		$string = "";
		$chaine = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		srand((double)microtime()*1000000);
		for($i=0; $i<$car; $i++) {
		$string .= $chaine[rand()%strlen($chaine)];
		}
		return $string;
	}
	public static function round($value, $round){
		echo number_format($value, $round);
	}
	
	/**
	 * Alias for Tools::random, with length set by default to 7
	 * 
	 * @param 	int		$car		Length od output
	 *
	 * @return	string	$string		Output
	 */
	public static function randID($car=7){
		return self::random($car);
	}

	/**
	 * Get the data format for PHP data displaying
	 *
	 * @param 	string	$php_format	Inject a format from locale
	 *
	 * @return 	string	$php_format	The dataformat used for data displaying
	 */
	public static function getDateFormat($php_format=null){
		if ($php_format==null) $php_format=_DATE_FORMAT_;
	    $php_format = str_replace('-', '/', $php_format);
	    $php_format = str_replace('.', '/', $php_format);
	    return $php_format;
	}
	/**
	 * Matches each symbol of PHP date format standard
	 * with jQuery equivalent codeword
	 * @author Tristan Jahier revised  by Grégory Gaudin
	 */
	public static function getJqueryDateFormat($php_format=null)	{
		if ($php_format==null) $php_format=_DATE_FORMAT_;
	    $SYMBOLS_MATCHING = array(
	        // Day
	        'd' => 'dd',
	        'D' => 'D',
	        'j' => 'd',
	        'l' => 'DD',
	        'N' => '',
	        'S' => '',
	        'w' => '',
	        'z' => 'o',
	        // Week
	        'W' => '',
	        // Month
	        'F' => 'MM',
	        'm' => 'mm',
	        'M' => 'M',
	        'n' => 'm',
	        't' => '',
	        // Year
	        'L' => '',
	        'o' => '',
	        'Y' => 'yy',
	        'y' => 'y',
	        // Time
	        'a' => '',
	        'A' => '',
	        'B' => '',
	        'g' => '',
	        'G' => '',
	        'h' => '',
	        'H' => '',
	        'i' => '',
	        's' => '',
	        'u' => ''
	    );
	    $jqueryui_format = "";
	    $escaping = false;
	    for($i = 0; $i < strlen($php_format); $i++)
	    {
	        $char = $php_format[$i];
	        if($char === '\\') // PHP date format escaping character
	        {
	            $i++;
	            if($escaping) $jqueryui_format .= $php_format[$i];
	            else $jqueryui_format .= '\'' . $php_format[$i];
	            $escaping = true;
	        }
	        else
	        {
	            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
	            if(isset($SYMBOLS_MATCHING[$char]))
	                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
	            else
	                $jqueryui_format .= $char;
	        }
	    }
	    $jqueryui_format = str_replace('-', '/', $jqueryui_format);
	    $jqueryui_format = str_replace('.', '/', $jqueryui_format);
	    return $jqueryui_format;
	}

	/**
	 * Transform an url into its valid form
	 *
	 * @param 	string	$url 	Url to transform
	 *
	 * @return 	string	$url	The valid url
	 */
	public static function url_transform($url){
		$url = strtolower(trim($url));
		$url = preg_replace('#d\'|l\'|s\'#', '', $url);
		$url = preg_replace('#-{2,}#', '-', preg_replace('#\s#', '-',  $url) ); 
		$url = preg_replace('#à|á|â|ã|ä|å#','a', $url);
		$url = preg_replace('#è|é|ê|ë#','e', $url);
		$url = preg_replace('#ì|í|î|ï#','i', $url);
		$url = preg_replace('#ò|ó|ô|õ|ö#','o', $url);
		$url = preg_replace('#ù|ú|û|ü#','u', $url);
		$url = preg_replace('#ý|ÿ#','y', $url);
		//$url = str_replace(array('ç', 'ñ'), array('c', 'n'), $url);
		return $url;
	}

	/**
	 * TTruncate a html contents
	 *
	 * @param 	string		$html 		Contents to truncate
	 * @param 	int 		$l 			length of truncated content
	 * @param 	Boolean 	$keepImg 	keep or not the image tag
	 *
	 * @return 	string	$url	The valid url
	 */
	public static function html_truncate($html, $l=300, $keepImg=true){
        if ($keepImg==false){
        	$html = Editor::removeMedia($html);
        }
    	$h = explode('<p><!-- truncate --></p>', $html);
    	if (count($h) > 1) return $h[0]; // in case of custom spliter

        $lwH = strlen(strip_tags($html));
        if($lwH < $l) return $html;
        $mSplit = '#</?([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?>#';
        $mMatch = '#<(?:/([a-zA-Z1-6]+)|([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?)>#';
        $html .= ' ';
        $htmlParts = preg_split($mSplit, $html, -1,  PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $nbParts = count($htmlParts);
        if( $nbParts == 1 ){
                $length = strlen($html);
                return substr($html, 0, strpos($html, ' ', $length > $l ? $l : $length));
        }
        $length = 0;
        $iLastPart = $nbParts - 1;
        $position = $htmlParts[$iLastPart][1] + strlen($htmlParts[$iLastPart][0]) - 1;
        $iPart = $iLastPart;
        $searchSpace = true;
        foreach( $htmlParts as $index => $part ) {
            $length += strlen($part[0]);
            if( $length >= $l ){
                    $endPart = $part[1] + strlen($part[0]) - 1;
                    $position = $endPart - ($length - $l);
                    if( ($posSpace = strpos($part[0], ' ', $position - $part[1])) !== false  )
                    {
                            $position = $part[1] + $posSpace;
                            $searchSpace = false;
                    }
                    if( $index != $iLastPart )
                            $iPart = $index + 1;
                    break;
            }
        }
        if( $searchSpace === true )
            for( $i=$iPart; $i<=$iLastPart; $i++ ){
                $position = $htmlParts[$i][1];
                if( ($posSpace = strpos($htmlParts[$i][0], ' ')) !== false ){
                    $position += $posSpace;
                    break;
                }
            }
        $html = substr($html, 0, $position);
        preg_match_all($mMatch, $html, $back, PREG_OFFSET_CAPTURE);
        $BoutsTag = array();
        foreach( $back[0] as $index => $tag ){
            if( isset($back[3][$index][0]) )
                continue;

            if( $back[0][$index][0][1] != '/' )
                array_unshift($BoutsTag, $back[2][$index][0]);
            else
                array_shift($BoutsTag);
        }

        if( !empty($BoutsTag) ){
            foreach( $BoutsTag as $tag )
                $html .= '</' . $tag . '>';
        }

        if ($lwH > $l){
            $html .= ' [......]';

            $html =  str_replace('</p> [......]', ' [...] </p>', $html);
            $html =  str_replace('</ul> [......]', ' [...] </ul>', $html);
            $html =  str_replace('</div> [......]', ' [...] </div>', $html);
        }

        return $html;
	}
	/**
	 * Convert from JSON
	 *
	 * @param	mixed	$data	json data convert
	 *
	 * @return	mixed			Json format of data
	 */
	public static function json_decode($data){
		$json = new Services_JSON();
        return( self::objectToArray($json->decode($data)) );
	}
	public static function json_dec($data){
		$json = new Services_JSON();
	    return( self::objectToArray($json->decode($data)) );
	}
	
	/**
	 * Convert Object to Array after json_decode
	 *
	 * @param	mixed	$d		decoded data
	 *
	 * @return	array	$d		data in array format
	 */
	public static function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}
	
	/**
	 * Convert into JSON
	 *
	 * @param	mixed	$data	Data to convert
	 *
	 * @return	json			Json format of data
	 */
	public static function json_enc($data){
		if (function_exists('json_encode')) return json_encode($data);
		if ($data===null) return 'null';
			if ($data===false) return 'false';
			if ($data===true) return 'true';
			if (is_string($data)) return '"'.$data.'"';
			if (!is_array($data)) return 'null';
			$r = '{';
			foreach ($data as $key => $value) {
				if ($r!='{') $r .= ', ';
				if (is_array($value)) $value = json_enc($value);
				else $value = '"'.$value.'"';
				$r .= '"'.$key.'":'.str_replace('\n', '', $value); 
			}
			$r .= '}';
			return $r;
	}

	
	/**
	 * Clear empty data from an array and reset its numerical keys
	 *
	 * @param 	string	$url 	Array to clear
	 *
	 * @return 	string	$url	The cleared array
	 */
	public static function clearNumericArray($p){
		foreach ($p as $key => $v)
			if (empty($v)) unset($p[$key]);
		$p = array_values ($p);
		return $p;
	}	

}

class Editor{
	public $html='';
	public function __construct($html='', $run=true){
		$this->html = str_get_html( $html );
		if ($run){
			$html = str_replace('<p><!-- truncate --></p>', '', $html);
			$this->indentCode();
			$this->setPictures();
		}
	}
	public function indentCode(){

	}
	public function setPictures(){

		foreach( $this->html->find('img') as $img){
			$src = str_replace('{url}', _ROOT_DIR_,$img->getAttribute('src'));
			$src = str_replace('{url:data}', _DATA_DIR_, $src); //finit plus tard
			$s = getimagesize( $src );
			if ($s)	$img->setAttribute('data-ratio', $s[0]/$s[1]);	
		}

	}
	public function getHtml($b=true){
		$html = $this->html->root->innertext();
		if ($b) str_replace( str_replace(_ROOT_DIR_,'../',_DATA_DIR_ ), "{url:data}" ,$html );
		return $html;
	}
	public static function removeMedia($html){
		$html = preg_replace('/{([^{}]+)}/','', $html);
		$o = new Editor($html, false);

		foreach( $o->html->find('img') as $img){
			$img->outertext = '';
		}
		foreach( $o->html->find('video') as $video){
			$video->outertext = '';
		}
		foreach( $o->html->find('iframe') as $iframe){
			$iframe->outertext = '';
		}
		return $o->getHtml();
	}

	public static function replaceExpr($html, $inverse=false){
		return !$inverse ? str_replace( array("{","}"), array("%code(",")%"), $html ): str_replace( array("%code(",")%"), array("{","}"), $html );
	}
}
?>
