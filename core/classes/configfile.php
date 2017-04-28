<?php
/**
 * Use and manage YAML config file
 *
 * @package Auwa \core\classes\
 * @copyright 2017 AuroraN
 */

/*
 * Load Thirty-part library : SPYC
 */
require_once(_CORE_DIR_.'frameworks/spyc/Spyc.php');

/**
 * Give method to load, parse and create config file
 *
 * @use class Spyc
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */
class ConfigFile{

	/*
	 * Path of file to parse/save
	 * @var string
	 */	 
	public $path;
	
	/*
	 * Name of config gile
	 * @var string
	 */
	public $name;

	/*
	 * Content of the file
	 * @var string
	 */
	protected $content;
	
	/*
	 * Array of each parameter parsed
	 * @var array
	 */
	protected $parameters;	

	/**
	 * Get a parsed config file, contructor
	 *
	 * @param	string		$file	source file
	 * @param	boolean		$admin	Set admin mode or not
	 *
	 * Set an instance of ConfigFile class
	 * If config file exists, parse it content
	 */
	public function __construct($file, $admin=true){
		$this->parameters = array();
		$base = $admin ?  _CORE_DIR_ : "" ;
		$this->path = $base.$file.'.yml';
		$this->parse();
	}
	
	/**
	 * Parse the config file content, instance method
	 *
	 * @use Spyc::YAMLLoad
	 */
	protected function parse(){
		$this->parameters = Spyc::YAMLLoad( $this->path );
	}
	
	/**
	 * Write Yaml file with ConfigFile::$parameters
	 *
	 * @param	array	$data		Set ConfigFile::$parameters and use it
	 */
	protected function setYaml($data=false){
		if ( is_array($data) ) $this->parameters = $data;
		return self::setFileContent( $this->path, self::dumpYaml($this->parameters) );
	}
	
	/**
	 * Get all parameters of a parsed config file, instance method
	 *
	 * return	array				All parameters set into config file
	 */
	public function getParameters(){
		return $this->parameters;
	}
	
	/**
	 * Get a parsed config file, static methods
	 *
	 * @param	string		$file	source file
	 * @param	boolean		$admin	Set admin mode or not
	 *
	 * return	ConfigFile			Parsed Config File
	 */
	public static function get($file, $admin=true){
		return new ConfigFile($file, $admin);
	}
	
	/**
	 * Parse the config file content, static method
	 *
	 * @param	string		$input	source file
	 *
	 * @use Spyc::YAMLLoad
	 */
	public static function parseYaml($input){
    	$input = str_replace('    ', '	', $input);
    	return Spyc::YAMLLoad( $input );
	}
	
	/**
	 * Get all parameters of a parsed config file, static method
	 *
	 * @param	string		$file	source file
	 * @param	boolean		$admin	Set admin mode or not
	 *
	 * return	array				All parameters set into config file
	 */
	public static function getConfig($file, $admin=true){
		return self::get($file, $admin)->getParameters();
	}

	/**
	 * Set all parameters of a config file, static method
	 *
	 * @param	string		$file	target file
	 * @param	boolean		$admin	Set admin mode or not
	 *
	 * return	boolean				Result
	 */
	public static function setConfig($file, $data, $admin=true){
		$base = $admin ?  _CORE_DIR_ : "" ;
		$path = $base.$file.'.yml';
		return self::setFileContent($path, $data);
	}

	/**
	 * Return config file content
	 * 
	 * @param	string		$file	Complete path of file
	 *
	 * @return	string				File content
	 */
	public static function getFileContent($file){
		if (is_file($file))
			return file_get_contents($file);
		return null;
	}
	
	/**
	 * Write config file
	 *
	 * @param	string		$file	Path of the file
	 * @param	mixed		$data	Content. Can be array or string
	 *
	 * if $data is an array, @use ConfigFile::dumpYaml to convert it to YAML
	 *
	 * @return	array		status, if array['error'] is set, an error occured
	 */
	public static function setFileContent($file, $data){
		if (! is_file($file))
			return array('error'=>'File not Found');
		$data = (is_array($data)) ? self::dumpYaml($data) : trim($data);	
		$r = file_put_contents($file, $data);
		if (!$r) return array('error'=>'File not written');
		else  return array('error'=>false);
	}

	/**
	 * Dump an data array to Yaml Content
	 *
	 * @param	array		$data	Array of data to dump
	 *
	 * @return	string				Content of future Yml file
	 */
	public static function dumpYaml($data){
		foreach ($data as $key => $value) {
			if ($value=='true') $data[$key] = true;
			if ($value=='false') $data[$key] = false;
		}
		return Spyc::YAMLDump($data);
	}

	/**
	 * Check if the config file exists
	 *
	 * @param 	string 	$file 	Name of the file to check
	 * @param 	boolean $admin 	The file comes from the core or not
	 * 
	 * @return 	boolean 		True if the file exists
	 */
	public static function exists($file, $admin=true){
		$base = $admin ?  _CORE_DIR_ : "" ;
		$path = $base.$file.'.yml';
		return (is_file($path));
	}

	/**
	 * Create a config file is it doesn't exist
	 *
	 * @param 	string 	$file 	Name of the file to check
	 * @param 	boolean $admin 	The file comes from the core or not
	 * 
	 * @return 	boolean 		Status of the operation
	 */
	public static function createIfNotExists($file, $admin=true){
		$base = $admin ?  _CORE_DIR_ : "" ;
		$path = $base.$file;
		if (!self::exists($path, false)) {
			touch($path.'.yml');
			return self::setFileContent($path.'.yml', "# Nouveau fichier de configuration");
		}
		return true;
	}
	
}
?>