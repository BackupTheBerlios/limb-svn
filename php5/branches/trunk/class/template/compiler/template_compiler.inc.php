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
require_once(LIMB_DIR . 'class/lib/error/error.inc.php');
require_once(LIMB_DIR . 'class/template/compiler/codewriter.class.php');
require_once(LIMB_DIR . 'class/template/compiler/utils.inc.php');
require_once(LIMB_DIR . 'class/template/compiler/tag_dictionary.class.php');

require_once(LIMB_DIR . 'class/template/compiler/compiler_component.class.php');
require_once(LIMB_DIR . 'class/template/compiler/compiler_directive_tag.class.php');
require_once(LIMB_DIR . 'class/template/compiler/silent_compiler_directive_tag.class.php');
require_once(LIMB_DIR . 'class/template/compiler/server_component_tag.class.php');
require_once(LIMB_DIR . 'class/template/compiler/server_tag_component_tag.class.php');
require_once(LIMB_DIR . 'class/template/compiler/text_node.class.php');
require_once(LIMB_DIR . 'class/template/compiler/root_compiler_component.class.php');

require_once(LIMB_DIR . 'class/template/compiler/source_file_parser.class.php');
require_once(LIMB_DIR . 'class/template/compiler/codewriter.class.php');
require_once(LIMB_DIR . 'class/template/compiler/variable_reference.class.php');

require_once(LIMB_DIR . 'class/template/fileschemes/compiler_support.inc.php');
require_once(LIMB_DIR . '/class/core/packages_info.class.php');

/**
* Create the tag_dictionary global variable
*/
$GLOBALS['tag_dictionary'] = new tag_dictionary();

function load_tags_from_directory($tags_repository_dir)  
{
  if(!is_dir($tags_repository_dir))
    return;
  
  $repository_dir = opendir($tags_repository_dir);
 
  while(($tag_dir = readdir($repository_dir)) !== false)
  {
  	if(!is_dir($tags_repository_dir . $tag_dir))
  	  continue;
  	
		if(($dir = opendir($tags_repository_dir . $tag_dir)) == false)
		  continue;

		while(($tag_file = readdir($dir)) !== false) 
		{  
			if  (substr($tag_file, -8,  8) == '.tag.php')  
			{
				include_once($tags_repository_dir . $tag_dir . '/' . $tag_file); 
			} 
		} 
		closedir($dir); 
  }
  closedir($repository_dir);
} 

function load_core_tags()
{
  load_tags_from_directory(LIMB_DIR . '/class/template/tags/');
}

load_core_tags();

function load_packages_tags()
{
  $info = packages_info :: instance();
  $packages = $info->get_packages();
  
  foreach($packages as $package)
	{
		load_tags_from_directory($package['path'] . '/template/tags/');
	} 
}

load_packages_tags();

/**
* Compiles a template file. Uses the file scheme to location the source,
* instantiates the code_writer and root_compiler_component (as the root) component then
* instantiates the source_file_parser to parse the template.
* Creates the initialize and render functions in the compiled template.
*/
function compile_template_file($filename, $resolve_path = true)
{
	global $tag_dictionary;
	
	if($resolve_path)
	{
		if(!$sourcefile = resolve_template_source_file_name($filename))
			error('template file not found', 
						__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
						array('file' => $filename));
	}
	else
		$sourcefile = $filename;
		
	$destfile = resolve_template_compiled_file_name($sourcefile, TMPL_INCLUDE);
	
	if (empty($sourcefile))
	{
		error('MISSINGFILE2', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('srcfile' => $filename));
	} 

	$code = new codewriter();
	$code->set_function_prefix(md5($destfile));

	$tree = new root_compiler_component();
	$tree->source_file = $sourcefile;

	$sfp = new source_file_parser($sourcefile, $tag_dictionary);
	$sfp->parse($tree);
	
	$tree->prepare();

	$render_function = $code->begin_function('($dataspace)');
	$tree->generate($code);
	$code->end_function();

	$construct_function = $code->begin_function('($dataspace)');
	$tree->generate_constructor($code);
	$code->end_function();

	$code->write_php('$GLOBALS[\'template_render\'][$this->codefile] = \'' . $render_function . '\';');
	$code->write_php('$GLOBALS[\'template_construct\'][$this->codefile] = \'' . $construct_function . '\';');

	write_template_file($destfile, $code->get_code());
} 

?>