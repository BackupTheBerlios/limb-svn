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


class label_tag_info
{
	var $tag = 'label';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'label_tag';
} 

register_tag(new label_tag_info());

/**
* Compile time component for building runtime form labels
*/
class label_tag extends server_tag_component_tag
{
	var $runtime_component_path = '/class/template/components/form/label_component';

	/**
	* 
	* @return void 
	* @access protected 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('label_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
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
	function generate_constructor(&$code)
	{
		parent::generate_constructor($code);
		if (array_key_exists('error_class', $this->attributes))
		{
			$code->write_php($this->get_component_ref_code() . '->error_class = \'' . $this->attributes['error_class'] . '\';');
		unset($this->attributes['error_class']);
		} 
		if (array_key_exists('error_style', $this->attributes))
		{
			$code->write_php($this->get_component_ref_code() . '->error_style = \'' . $this->attributes['error_style'] . '\';');
		unset($this->attributes['error_style']);
		} 
	} 
	
} 

?>