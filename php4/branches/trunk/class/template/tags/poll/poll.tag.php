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

class poll_tag_info
{
	var $tag = 'poll';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'poll_tag';
} 

register_tag(new poll_tag_info());

/**
* The parent compile time component for lists
*/
class poll_tag extends server_component_tag
{
	var $runtime_component_path = '/class/template/components/poll_component';

	function pre_generate(&$code)
	{
		parent::pre_generate($code);
		
		$code->write_php($this->get_component_ref_code() . '->prepare();');
	} 

	function generate_contents(&$code)
	{		
		$form_child =& $this->find_child_by_class('poll_form_tag');
		$results_child =& $this->find_child_by_class('poll_result_tag');
		
		$code->write_php('if (' . $this->get_component_ref_code() . '->poll_exists()) {');
					
		$code->write_php('if (' . $this->get_component_ref_code() . '->can_vote()) {');

		if($form_child)
			$form_child->generate($code);
										
			$code->write_php('}else{');
		
		if ($results_child)
			$results_child->generate($code);
				
		$code->write_php('}}');
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