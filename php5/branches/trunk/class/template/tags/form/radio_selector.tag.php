<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class radio_selector_tag_info
{
	public $tag = 'radio_selector';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'radio_selector_tag';
} 

register_tag(new radio_selector_tag_info());

class radio_selector_tag extends compiler_directive_tag
{
	public function pre_generate($code)
	{
		$value = '$' . $code->get_temp_variable();
		$parent = $this->get_dataspace_ref_code();
		
		$radio_child = $this->find_child_by_class('input_tag');
		$label_child = $this->find_child_by_class('label_tag');
		
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