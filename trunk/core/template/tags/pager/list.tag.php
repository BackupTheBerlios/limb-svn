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


class pager_list_tag_info
{
	var $tag = 'pager:LIST';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'pager_list_tag';
} 

register_tag(new pager_list_tag_info());

/**
* Compile time component for the iterable section of the pager
*/
class pager_list_tag extends compiler_directive_tag
{
	/**
	* 
	* @return void 
	* @access private 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('pager_list_tag'))
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
	function pre_generate(&$code)
	{
		parent::pre_generate($code);

		$parent = &$this->find_parent_by_class('pager_navigator_tag');
		$code->write_php('if (' . $parent->get_component_ref_code() . '->next()) {');
	} 
	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function post_generate(&$code)
	{
		$code->write_php('}');

		$emptychild = &$this->find_child_by_class('list_default_tag');
		if ($emptychild)
		{
			$code->write_php(' else { ');
			$emptychild->generate_now($code);
			$code->write_php('}');
		} 
		parent::post_generate($code);
	} 
	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate_contents(&$code)
	{
		$sep_child = &$this->find_child_by_class('pager_separator_tag');
		$current_child = &$this->find_child_by_class('pager_current_tag');
		$number_child = &$this->find_child_by_class('pager_number_tag');
		$section_child = &$this->find_child_by_class('pager_section_tag');
		
		$parent = &$this->find_parent_by_class('pager_navigator_tag');

		$code->write_php('do { ');

		if ($sep_child)
		{
			$code->write_php('if (');
			$code->write_php($parent->get_component_ref_code() . '->show_separator');
			$code->write_php('&& (');
			$code->write_php($parent->get_component_ref_code() . '->is_current_page() ||');
			$code->write_php($parent->get_component_ref_code() . '->is_display_page()');
			$code->write_php(')) {');
			$sep_child->generate_now($code);
			$code->write_php('}');
			$code->write_php($parent->get_component_ref_code() . '->show_separator = true;');
		} 

		$code->write_php('if (' . $parent->get_component_ref_code() . '->is_display_page()) {');
		
		$code->write_php('if (!(' . $parent->get_component_ref_code() . '->is_first() && ' . $parent->get_component_ref_code() . '->is_last())) {');
		
		if ($number_child)
			$number_child->generate($code);
			
		if($current_child)
			$current_child->generate($code);
			
		$code->write_php('}');
			
		$code->write_php('}else{');
		
		$code->write_php('if (' . $parent->get_component_ref_code() . '->has_section_changed()) {');
		
		if($section_child)
			$section_child->generate($code);
			
		$code->write_php('}');
		
		$code->write_php($parent->get_component_ref_code() . '->show_separator = false;');
		$code->write_php('}');

		$code->write_php('} while (' . $parent->get_component_ref_code() . '->next());');
	} 
} 

?>