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


require_once(LIMB_DIR . 'class/template/compiler/var_file_compiler.inc.php');

class core_import_tag_info
{
	var $tag = 'core:IMPORT';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'core_import_tag';
} 

register_tag(new core_import_tag_info());

/**
* Imports a var file into the dataspace (e.g. a configuration file)
*/
class core_import_tag extends silent_compiler_directive_tag
{
	/**
	* 
	* @return void 
	* @access protected 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('core_import_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	/**
	* 
	* @return int PARSER_FORBID_PARSING
	* @access protected 
	*/
	function pre_parse()
	{
		if (! array_key_exists('file', $this->attributes) ||
				empty($this->attributes['file']))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'file',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		$file = $this->attributes['file'];
		if (!$sourcefile = resolve_template_source_file_name($file))
		{
			error('MISSINGFILE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'srcfile' => $file,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	
		$dataspace = &$this->get_dataspace();
		$dataspace->vars += parse_var_file($sourcefile);
		return PARSER_FORBID_PARSING;
	} 
} 

?>