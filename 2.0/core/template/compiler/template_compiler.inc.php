<?php

require_once(LIMB_DIR . 'core/lib/error/error.inc.php');
require_once(LIMB_DIR . 'core/template/compiler/codewriter.class.php');
require_once(LIMB_DIR . 'core/template/compiler/utils.inc.php');
require_once(LIMB_DIR . 'core/template/compiler/utils.inc.php');
require_once(LIMB_DIR . 'core/template/compiler/tag_dictionary.class.php');

require_once(LIMB_DIR . 'core/template/compiler/compiler_component.class.php');
require_once(LIMB_DIR . 'core/template/compiler/compiler_directive_tag.class.php');
require_once(LIMB_DIR . 'core/template/compiler/silent_compiler_directive_tag.class.php');
require_once(LIMB_DIR . 'core/template/compiler/server_component_tag.class.php');
require_once(LIMB_DIR . 'core/template/compiler/server_tag_component_tag.class.php');
require_once(LIMB_DIR . 'core/template/compiler/text_node.class.php');
require_once(LIMB_DIR . 'core/template/compiler/root_compiler_component.class.php');

require_once(LIMB_DIR . 'core/template/compiler/source_file_parser.class.php');
require_once(LIMB_DIR . 'core/template/compiler/codewriter.class.php');
require_once(LIMB_DIR . 'core/template/compiler/variable_reference.class.php');

require_once(TMPL_FILESCHEME_PATH . '/compiler_support.inc.php');

/** * Loads a concrete  compile time tags file *  * @param string $ name  of tag
file, to switch .tag.php will  be appended */ 
function load_tags($tag_dir)  
{ 
	if(is_dir($tag_dir))  
	{  
		if  ($dir  =  opendir($tag_dir))  
		{  
			while(($tag_file = readdir($dir)) !== false) 
			{  
				if  (substr($tag_file, -8,  8) == '.tag.php')  
				{
					include_once($tag_dir . '/' . $tag_file); 
				} 
			} 
			closedir($dir); 
		} 
	}
} 

/**
* Create the tag_dictionary global variable
*/
$GLOBALS['tag_dictionary'] = &new tag_dictionary();
/**
* Load some tag files
*/
$path = get_ini_option('compiler.ini', 'tags', 'path');
foreach ($path as $tagpath)
{
	if (substr($tagpath, 0, 1) != '/')
	{
		load_tags(LIMB_DIR . 'core/template/tags/' . $tagpath);
		load_tags(PROJECT_DIR . 'core/template/tags/' . $tagpath);
	} 
	else
	{
		load_tags($tagpath);
	} 
} 

/**
* Compiles a template file. Uses the file scheme to location the source,
* instantiates the code_writer and root_compiler_component (as the root) component then
* instantiates the source_file_parser to parse the template.
* Creates the initialize and render functions in the compiled template.
* 
* @see root_compiler_component
* @see code_writer
* @see source_file_parser
* @param string $ name of source template
* @return void 
*/
function compile_template_file($filename)
{
	global $tag_dictionary;
	$sourcefile = resolve_template_source_file_name($filename, TMPL_INCLUDE);
	$destfile = resolve_template_compiled_file_name($sourcefile, TMPL_INCLUDE);
	
	if (empty($sourcefile))
	{
		error('MISSINGFILE2', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('srcfile' => $filename));
	} 

	$code =& new codewriter();
	$code->set_function_prefix(md5($destfile));

	$tree =& new root_compiler_component();
	$tree->source_file = $sourcefile;

	$sfp =& new source_file_parser($sourcefile, $tag_dictionary);
	$sfp->parse($tree);
	
	$tree->prepare();

	$render_function = $code->begin_function('(&$dataspace)');
	$tree->generate($code);
	$code->end_function();

	$construct_function = $code->begin_function('(&$dataspace)');
	$tree->generate_constructor($code);
	$code->end_function();

	$code->write_php('$GLOBALS[\'template_render\'][$this->codefile] = \'' . $render_function . '\';');
	$code->write_php('$GLOBALS[\'template_construct\'][$this->codefile] = \'' . $construct_function . '\';');

	write_template_file($destfile, $code->get_code());
} 

?>