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


class pager_section_tag_info
{
	var $tag = 'pager:section';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'pager_section_tag';
} 

register_tag(new pager_section_tag_info());

class pager_section_tag extends server_component_tag
{
	/**
	* 
	* @return void 
	* @access private 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('pager_section_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!$this->find_parent_by_class('pager_navigator_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'pager:navigator',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate_contents(&$code)
	{
		$parent = &$this->find_parent_by_class('pager_navigator_tag');
		$code->write_php('if (!' . $parent->get_component_ref_code() . '->is_display_page()) {');

		$code->write_php($this->get_component_ref_code() . '->set("href", ' . $parent->get_component_ref_code() . '->get_current_section_uri());');
		$code->write_php($this->get_component_ref_code() . '->set("number_begin", ' . $parent->get_component_ref_code() . '->get_current_section_begin_number());');
		$code->write_php($this->get_component_ref_code() . '->set("number_end", ' . $parent->get_component_ref_code() . '->get_current_section_end_number());');
		
		parent :: generate_contents($code);
		
		$code->write_php('}');
	} 
	
	function &get_dataspace()
	{
		return $this;
	}
	
	function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code();
	} 
} 

?>