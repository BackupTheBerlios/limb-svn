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


class list_item_tag_info
{
	var $tag = 'list:ITEM';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'list_item_tag';
} 

register_tag(new list_item_tag_info());

/**
* Compile time component for items (rows) in the list
*/
class list_item_tag extends compiler_directive_tag
{
	/**
	* 
	* @return void 
	* @access protected 
	*/
	function check_nesting_level()
	{
		if (!is_a($this->parent, 'list_list_tag') && !is_a($this->parent, 'errors_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'list:LIST or errors',
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
		$sep_child =& $this->find_immediate_child_by_class('list_separator_tag');
		
		$code->write_php('do { ');
						
		if ($sep_child)
		{
			$code->write_php('if (' . $this->get_component_ref_code() . '->show_separator) {');
			$sep_child->generate_now($code);
			$code->write_php('}');
			$code->write_php($this->get_component_ref_code() . '->show_separator = TRUE;');
		} 
		
		parent::generate_contents($code);

		$code->write_php('} while (' . $this->get_dataspace_ref_code() . '->next());');
	} 
} 

?>