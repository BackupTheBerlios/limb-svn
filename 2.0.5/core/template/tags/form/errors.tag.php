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


class errors_tag_info
{
	var $tag = 'errors';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'errors_tag';
} 

register_tag(new errors_tag_info());

/**
* Compile time component for errorsummary tags. Uses the list_component at
* runtime
*/
class errors_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/list_component';
	
	/**
	* ???
	* 
	* @var object 
	* @access private 
	*/
	var $item_child;

	/**
	* 
	* @return void 
	* @access protected 
	*/
	function check_nesting_level()
	{
		if (!$this->find_parent_by_class('form_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'form',
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
		
		$parent_form =& $this->find_parent_by_class('form_tag');
		
		$code->write_php($this->get_component_ref_code() . '->register_dataset(' .
			$parent_form->get_component_ref_code() . '->get_error_dataset());');

		if (isset($this->attributes['for']))
		{
			$code->write_php($this->get_dataspace_ref_code() . '->restrict_fields(array(\'' . $this->attributes['for'] . '\'));');
		}
	
		$code->write_php($this->get_component_ref_code() . '->prepare();');
		$code->write_php('if (' . $this->get_dataspace_ref_code() . '->next()) {');
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
		parent::post_generate($code);
	} 
	
	/**
	* 
	* @return error _summary_tag this instance
	* @access protected 
	*/
	function &get_dataspace()
	{
		return $this;
	} 
	
	/**
	* 
	* @return string PHP runtime reference to object
	* @access protected 
	*/
	function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code() . '->dataset';
	} 
} 

?>