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


class grid_list_tag_info
{
	var $tag = 'grid:LIST';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'grid_list_tag';
} 

register_tag(new grid_list_tag_info());

/**
* The parent compile time component for lists
*/
class grid_list_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/list_component';
	
	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		$code->write_php($this->get_component_ref_code() . '->prepare();');
		
		parent :: pre_generate($code);

		$code->write_php('if (' . $this->get_dataspace_ref_code() . '->get_total_row_count()){');
	} 

	function post_generate(&$code)
	{
		$code->write_php('} else {');
		
		if ($default = &$this->find_immediate_child_by_class('grid_default_tag'))
			$default->generate_now($code);
			
		$code->write_php('}');
		
		parent :: post_generate($code);
	} 
	/**
	* 
	* @return list _list_tag this instance
	* @access protected 
	*/
	function &get_dataspace()
	{
		return $this;
	} 

	/**
	* 
	* @return string PHP runtime variable reference to component
	* @access protected 
	*/
	function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code() . '->dataset';
	} 
} 

?>