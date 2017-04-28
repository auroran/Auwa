<?php
namespace Auwa;

class Template{
	public $path;
	public $output_file;
	public $name;
	public $content;
	public $updateTime;
	public $forceCompilation=false;
	public $type='template';
	public $vars = array();
	public $e;

	public static $compilationMode = 0;
	public static $expirationTime = 3600;
	public static $cacheDirectory = false;

	public static $varSigns = array(
			'.' => '[\'',
			'->' => '->',
			'[$' => '[$',
			'[' => '[',
			']' => '\']',
	);

	// tags and 1level function
	public static $tags = array(
			'/<p>{/'							=> '{',													// remove <p> tag
			'/}<\/p>/'							=> '}',													// remove </p> tag
			'/{@([^{}]+)}/'						=> '<?php \$$1; ?>',									// Set a variable => and the case of "{url:data}"
			'/{\$([^}]+)}/'						=> '<?php echo $$1;?>',									// Display variable
			'/{fn\:([^}]+)}/'					=> '<?php $1;?>',										// Execute a function
			'/{if ([^}]+)}/'					=> '<?php if($1){?>',									// IF condition clause
			'/{else}/'							=> '<?php } else {?>',									// ELSE condition clause
			'/{\/if}|{fi}|{\/foreach}|{\/for}/'	=> '<?php }?>',											// End of a condition/loop
			'/{foreach ([^}]+)}/'				=> '<?php foreach( $1 ){ ?>',							// foreach loop
			'/{for ([^}]+)}/'					=> '<?php for( $1 ){ ?>',								// for loop
			'/{url:data}/'						=> '<?php Auwa::pathToUrl(_DATA_DIR_);?>',				// Display data folder URL
			'/{url}/'							=> '<?php Auwa::display_url();?>',						// Display base URL
			'/{submitted:([^}]+)}/'				=> '<?php if ( Tools::getValue(\'$1\') ){?>',			// Test if a post variable is set
			'/{\/submitted}/'					=> '<?php }?>',											// End of POST var existing test
			'/{formValue:([^}]+)}/'				=> '<?php echo Tools::getValue(\'$1\', \'post\');?>',	// Get value of a POST variable
			'/Text\(([^\)]+)\)/'				=> '\'$1\'',
			'/{t:([^}]+),([^}]+)}/'				=> '<?php echo Tools::translate(trim($1),trim($2));?>',	// Translation for an Auwa(App)Controller
			'/{T:([^}]+),([^}]+)}/'				=> '<?php echo Tools::translateForAll(trim($1),trim($2));?>',// Translation for all Auwa(App)Controller
			'/{round:([^}]+),([^}]+)}/'			=> '<?php echo Tools::round(trim($1),trim($2));?>',
			'/{date:([^}]+)}/'					=> '<?php echo date(trim(\'$1\'));?>',
			'/{date}/'							=> '<?php echo date();?>',
	);

	public function __construct($name=false, $path='', $type=false){
		$this->id_exec = 'tpl_'.Tools::random(4);
		if ($name)  $this->name = $name;
		if ($type)  $this->type = $type;
		$this->path = $path;
		$this->vars = array();
		$this->e = new Error();
	}

	public function fill($content, $upd){
		$this->content = Editor::replaceExpr($content, true);
		if ($upd) $this->updateTime = $upd;
	}

	public function link($tpl, $path = false){
		$this->name = $tpl;
		if ($path) $this->path = $path;
	}

	public function assign($var, $value){
		$this->vars[$var]= $value;
	}

	protected function translateVar($var, $expr="", $lastSign=''){
		//if ($expr=='') echo "$var = > ";
		$input = preg_split('/\.|\-\>\|\[|\[\$/', $var, 2);
		preg_match('/\.|\-\>\|\[|\[\$/', $var, $signs);
		$expr.= $input[0];
		if ($lastSign=='[\'') $expr .='\']';
		
		$sign = isset($signs[0]) ? self::$varSigns[$signs[0]] : '';
		$expr .= $sign;
		if (isset($input[1])) return $this->translateVar($input[1], $expr, $sign);
		if ($sign==self::$varSigns['[']) $expr .= self::$varSigns[']'];
		//echo "$expr".PHP_EOL;
		return $expr;
	}

	protected function retrieveVar($var, $key){
		if (empty($var)) return null;
		if (is_object($var)) {
			if ( preg_match('/\(/', $key)){
				$fn = preg_replace('/\(|\)/', '', $key);
				if (method_exists($var, $fn)) return $var->$fn();
				return  _DEBUG_MOD_ ? "Unknown method <i>$fn</i> [Object <i>".get_class($var).'</i>]' : null;
			}
			if (!isset($var->{$key})) return  _DEBUG_MOD_ ? "Property <i>{$key}</i> doesn't exist [Object <i>".get_class($var).'</i>]' : null;
			return $var->{$key};
		}
		if (is_array($var) && isset( $var[$key] )) return $var[$key];
		return  null;
	}
	
	protected function buildVar( $var, $target=false ){
		$input = preg_split('/\.|\-\>\|\[/', $var, 2);
		if (!isset($input)) $input = array($var);

		if ($key) $input[0] = preg_replace('/\]$/', '', $input[0]);

		if ( preg_match('/\$/', $input[0]) ){
			$varname = trim( str_replace('$','',$input[0]) );
			$key = $this->vars[$varname];
			$target = $target ? $this->retrieveVar($target, $key) : $key;
		} else {
			if (!$target) $target = $this->vars;
			$target = $this->retrieveVar( $target, trim($input[0]) );
		} 
		if ($target==null) return null;
		if (isset($input[1])) return $this->buildVar( $input[1], $target );
		return $target;
	}


	protected function includeRendering($input, $type=false){
		$args = explode(',',$input);
		$tpl = trim($args[0]);
		$path = false;
		$varstr= "";
		$vars = array();
		$php = "<?php echo Template::includeTemplate('$tpl'";
		unset($args[0]);
		foreach ($args as $k => $arg) {
			if ( preg_match('/\=/', $arg)){
				//variable
				$var = explode('=',$arg);
				$varstr .= ', array("'.trim($var[0]).'",'.$var[1].')';
			} else {
				if ($args[1]) $path = trim($args[1]);
			}
		}
		if (!$path) $path = $this->path;
		$php .= ", \"$path\", false";
		$php .="$varstr); ?>";
		return $php;
	}



	public static function compareByLength($a, $b){
	    if (strlen($a) == strlen($b)) {
	        return 0;
	    }
	    return (strlen($a) > strlen($b) ) ? -1 : 1;
	}

	/**
	 * Convert custom tag to PHP
	 *
	 * @param	string	$str	Original content of the template
	 */
	public function compile( $str, $input_vars =array() ){
		// simple tags replacement
		foreach(self::$tags as $pattern=>$replace)
			$str = preg_replace($pattern,$replace,$str);

		// specific tags replacement

		foreach (Session::get() as $name => $value) {
			if (!isset($this->vars[$name])) $this->assign($name, $value);
		}
		foreach (Auwa::get()->tplVars as $name => $value) {
			if (!isset($this->vars[$name])) $this->assign($name, $value);
		}
		foreach ($input_vars as $name => $value) {
			$this->assign($name, $value);
		}
		
		// TAG: Constant (evaluate the value of the constant)
		preg_match_all('/%([\w]+)%/', $str, $constants,PREG_PATTERN_ORDER);
		foreach ($constants[1] as $key => $value) {
			eval("\$v = $value;");
			$str = str_replace('%'.$value.'%', $v, $str);
		}
		// TAG : Variable rewriting
		preg_match_all('/\$[\w\.\[\$\]]+/', $str, $variables,PREG_PATTERN_ORDER);
		usort($variables[0], array('Auwa\Template','compareByLength'));
		//var_dump($variables);
		$patterns = $replaces = array();
		foreach ($variables[0] as $key => $value) {
			//continue;
			if (!preg_match( '/\./', $value ) ) continue;
			$patterns[] = $value;
			$replaces[] = $this->translateVar( $value );
		}
		$str = str_replace($patterns, $replaces, $str);

		// TAG : VARIABLE SETTER
		/*preg_match_all('/{@([^}]+)}/', $str, $templates,PREG_PATTERN_ORDER);
		foreach ($templates[0] as $key => $value) {
			$args = explode('=',$templates[1][$key], 2);
			$v = $this->buildVar( $args[1] );
			$this->assign(trim($args[0]), $v);
			$str = preg_replace('/'. addcslashes($value,"()[]/,:.+*$^").'/', '', $str);
		}*/

		// TAG ; template inclusion
		preg_match_all('/{template:([^}]+)}/', $str, $templates,PREG_PATTERN_ORDER);
		foreach ($templates[0] as $key => $value) {
			$str = preg_replace(
					'/'. addcslashes($value,"()[]/,:.+*$^").'/', 
					$this->includeRendering( $templates[1][$key] ),
					$str
				);
		}
		// TAG : page inclusion
		preg_match_all('/{page:([^}]+)}/', $str, $templates,PREG_PATTERN_ORDER);
		foreach ($templates[0] as $key => $value) {
			$r = "<?php echo Page::getPageContentByRewrite('".trim($templates[1][$key])."'); ?>";
			$str = preg_replace(
					'/'. addcslashes($value,"()[]/,:.+*$^").'/', 
					$r,
					$str
				);
		}
		// TAG : page inclusion
		preg_match_all('/{Hook:([^}]+)}/', $str, $templates,PREG_PATTERN_ORDER);
		foreach ($templates[0] as $key => $value) {
			$r = "<?php Hook::exec('".trim($templates[1][$key])."'); ?>";
			$str = preg_replace(
					'/'. addcslashes($value,"()[]/,:.+*$^").'/', 
					$r,
					$str
				);
		}


		// TAG : content inclusion
		preg_match_all('/{content:([^}]+)}/', $str, $templates,PREG_PATTERN_ORDER);
		foreach ($templates[0] as $key => $value) {
			$content = preg_match('/^\$/', trim($templates[1][$key])) ? trim($templates[1][$key]) : "'".trim($templates[1][$key])."'";
			$r = "<?php echo Template::includeTemplate('content_".Tools::random(4)."', false, ".$content."); ?>";
			$str = preg_replace(
					'/'. addcslashes($value,"()[]/,:.+*$^").'/', 
					$r,
					$str
				);
		}
		return $str;
	} 

	/**
	 * Build template : convert it to HTML/PHP file
	 */
	public function build( $vars = array() ){
		//echo "Build: ".$this->name."<br>".PHP_EOL;
		$input_file = $this->path.$this->name;
		$cache_dir = self::$cacheDirectory;
		if (!$cache_dir) return trigger_error('No cache directory set !');
		if (!is_dir($cache_dir)) @mkdir($cache_dir);

		$ctrl = _CURRENT_CTRL_;
		$this->output_file = $cache_dir.$this->type."_"._CURRENT_LANG_.'_'.md5($this->path)."_".str_replace('/','_',$this->name).".php";
		
		if ($this->forceCompilation==true || !file_exists($this->output_file) )
			$recompile = true;
		else
			switch (self::$compilationMode) {
			 	case 1:
			 		$recompile = $this->updateTime 
			 			? ( strtotime($this->updateTime) > filemtime($this->output_file)  ) : (filemtime($this->output_file)  < self::$expirationTime ) ;
			 		break;
			 	case 2 :
			 		$recompile = true;
			 		break;
			 	default:
			 		$recompile = false;
			 		break;
			}

		if( !$recompile ) return file_get_contents($this->output_file);
		switch( $this->type ){
			case 'page':
				// $content is set from parameter
				$content = $this->content;
				break;
			default :
				$filepath = false;
				$rewrite = true;
				if (is_file($input_file.'.php')){
					$filepath = $input_file.'.php';
					$rewrite = false;
				}
				if (is_file($input_file.'.tpl')){
					$filepath = $input_file.'.tpl';
				}
				if (is_file($input_file.'.html')){
					$filepath = $input_file.'.html';
				}

				if (!$filepath) {
					$this->e->addError("Template non trouvé : ".$input_file);
					return false;
				};
				
				if (! $rewrite) {
					$this->output_file = $filepath;
					return true;
				} 
				$content = file_get_contents($filepath); 
		} 
		$this->content = $this->compile( $content, $vars);
		$r = file_put_contents($this->output_file, $this->content);
		if (!$r) $this->e->addError("Impossible d'écrire le fichier ".$this->output_file, 'danger');
	}

	public function render( $vars=array() ){
		$this->build($vars);
		foreach ($this->vars as $name => $value) {
			$$name = $value;
		}
		$output = null;
		eval("?><?php namespace Auwa; ob_start(); ?>".$this->content."<?php \$output=ob_get_contents(); ob_end_clean();");
		return $output;
	}
	public function display(){
		echo $this->render();
	}

	public static function includeTemplate($tpl, $path=false, $content=false){
		$vars = func_get_args();
		$input_vars = array();
		unset($vars[0]); unset($vars[1]);
		foreach ($vars as $key => $value) {
			$input_vars[ $value[0] ] = $value[1];
		}
		$t = new Template( $tpl, $path );
		if ($content){
			$t->content = $content;
			$t->type = 'page';
		}
		return $t->render($input_vars);
	}

	public static function getRendering($tpl, $path=false, $include=false, $vars){
		$t = new Template( $tpl, $path );
		$c = $t->render($vars);
		return $include ? true : $c;
	}

	public static function addTplRules($patterns, $replaces){
		if ( is_string($patterns) ) $patterns = array($patterns);
		if ( is_string($replaces) ) $replaces = array($replaces);
		foreach ($patterns as $key => $value) {
			self::$tags[$value] = $replaces[$key];
		}
	}
}
?>