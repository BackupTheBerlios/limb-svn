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


class core_include_tag_info
{
	var $tag = 'core:INCLUDE';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'core_include_tag';
} 

register_tag(new core_include_tag_info());

/**
* Include another template into the current template
*/
class core_include_tag extends compiler_directive_tag
{
	
	var $resolved_source_file;
	
	/**
	* 
	* @return int PARSER_FORBID_PARSING
	* @access protected 
	*/
	function pre_parse()
	{
		global $tag_dictionary;
		if (! array_key_exists('file', $this->attributes) ||
				empty($this->attributes['file']))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
				array('tag' => $this->tag,
							'attribute' => 'file',
							'file' => $this->source_file,
							'line' => $this->starting_line_no));
		} 
		$file = $this->attributes['file'];

		$this->resolved_source_file = resolve_template_source_file_name($file, TMPL_INCLUDE, $this->source_file);
		if (empty($this->resolved_source_file))
		{
			error('MISSINGFILE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
				array('tag' => $this->tag,
							'srcfile' => $file,
							'file' => $this->source_file,
							'line' => $this->starting_line_no));
		} 

		if (array_key_exists('literal', $this->attributes))
		{
			$literal_component =& new text_node(read_template_file($this->resolved_source_file));
			$this->add_child($literal_component);
		} 
		else
		{
			$sfp =& new source_file_parser($this->resolved_source_file, $tag_dictionary);
			$sfp->parse($this);
		} 
		return PARSER_FORBID_PARSING;
	} 
	
	function generate_contents(&$code)
	{
		if($this->is_debug_enabled())
			$code->write_html("<div style='border:dashed 1px red;'><img src='/shared/images/i.gif' alt='{$this->resolved_source_file}'><br>");
		
		parent :: generate_contents($code);
		
		if($this->is_debug_enabled())
			$code->write_html('</div>');
	}
} 

?>