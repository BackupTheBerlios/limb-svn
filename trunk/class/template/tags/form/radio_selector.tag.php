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

//require_once(LIMB_DIR . '/class/template/tags/form/compiler_directive_tag.class.php');

class radio_selector_tag_info
{
	var $tag = 'radio_selector';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'radio_selector_tag';
} 

register_tag(new radio_selector_tag_info());

class radio_selector_tag extends compiler_directive_tag
{
	function pre_generate(&$code)
	{
		$value = '$' . $code->get_temp_variable();
		$parent = $this->get_dataspace_ref_code();
		
		$radio_child =& $this->find_child_by_class('input_tag');
		$label_child =& $this->find_child_by_class('label_tag');
		
		$radio = $radio_child->get_component_ref_code();
		$label = $label_child->get_component_ref_code();
		
				
		$code->write_php("
		if ({$value} = {$parent}->get('id'))
		{
			{$radio}->set_attribute('value', {$value});
			{$radio}->set_attribute('id', {$value});
			{$label}->set_attribute('for', {$value});
		}	
		");	

		parent :: pre_generate($code);
	}
} 

?>