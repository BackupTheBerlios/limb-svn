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

class form_errors_tag_info
{
	var $tag = 'form:ERRORS';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'form_errors_tag';
} 

register_tag(new form_errors_tag_info());

class form_errors_tag extends server_component_tag
{
	var $runtime_component_path = '/class/template/components/list_component';
	
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
		if (!isset($this->attributes['target']))
		{
			error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'target',
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
		$parent_form =& $this->find_parent_by_class('form_tag');
		
		$target =& $this->parent->find_child($this->attributes['target']);
		
		$code->write_php($target->get_component_ref_code() . '->register_dataset(' .
			$parent_form->get_component_ref_code() . '->get_error_dataset());');	
			
		parent :: generate_contents($code);
	} 
	
} 

?>