<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
define('TMPL_IMPORT', 'import');
define('TMPL_INCLUDE', 'include');

require_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');
require_once(LIMB_DIR . 'class/lib/error/error.inc.php');
require_once(LIMB_DIR . 'class/template/component.class.php');
require_once(LIMB_DIR . 'class/template/fileschemes/simpleroot/compiler_support.inc.php');
require_once(LIMB_DIR . 'class/template/fileschemes/simpleroot/runtime_support.inc.php');

/**
* Instantiate global variable $template_render and $template_construct as arrays
*/
$template_render = array();
$template_construct = array();

/**
* Public facade for handling templates, dealing with loading, compiling and
* displaying
* 
* @access public
*/
class template extends component
{
	/**
	* Stored the name of the compiled template file
	* 
	* @var string 
	* @access private 
	*/
	var $codefile;

	var $file;
	/**
	* Name of function in compiled template which outputs display to screen
	* 
	* @var string 
	* @access private 
	*/
	var $render_function;

	/**
	* Constructs template
	* 
	* @param string $ name of (source) template file (relative or full path)
	* @access public 
	*/
	function template($file, $resolve_path = true)
	{
		$this->file = $file;
		
		if($resolve_path)
		{
			if(!$srcfile = resolve_template_source_file_name($file))
				error('template file not found', 
							__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
							array('file' => $file));
		}
		else
			$srcfile = $file;				

		$this->codefile = resolve_template_compiled_file_name($srcfile, TMPL_INCLUDE);
		
		if (!isset($GLOBALS['template_render'][$this->codefile]))
		{
			if (get_ini_option('config.ini', 'force_compile', 'templates'))
			{
			  include_once(LIMB_DIR . 'class/template/compiler/template_compiler.inc.php');
				compile_template_file($file, $resolve_path);
			}
			
			if(!file_exists($this->codefile))
			{
			  include_once(LIMB_DIR . 'class/template/compiler/template_compiler.inc.php');
				compile_template_file($file, $resolve_path);
			}
			
			$errorlevel = error_reporting();
			error_reporting($errorlevel &~E_WARNING);
			$parse_error = include_once($this->codefile);
			error_reporting($errorlevel);
			
		} 
		$this->render_function = $GLOBALS['template_render'][$this->codefile];
		$func = $GLOBALS['template_construct'][$this->codefile];
		$func($this);
	} 

	function &get_child($server_id)
	{
		$result = &$this->find_child($server_id);
		if (!is_object($result))
		{
			error('COMPONENTNOTFOUND', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
					array('file' => $this->file,
					'server_id' => $server_id));
		} 
		return $result;
	} 

	/**
	* Outputs the template, calling the compiled templates render function
	* 
	* @return void 
	* @access public 
	*/
	function display()
	{
		$func = $this->render_function;
		$func($this);
	} 
} 

?>