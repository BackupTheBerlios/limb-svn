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
class poll_tag_info
{
	public $tag = 'poll';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'poll_tag';
} 

register_tag(new poll_tag_info());

/**
* The parent compile time component for lists
*/
class poll_tag extends server_component_tag
{
	function __construct()
	{
	  $this->runtime_component_path = dirname(__FILE__) . '/../components/poll_component';
	}

	public function pre_generate($code)
	{
		parent::pre_generate($code);
		
		$code->write_php($this->get_component_ref_code() . '->prepare();');
	} 

	public function generate_contents($code)
	{		
		$form_child = $this->find_child_by_class('poll_form_tag');
		$results_child = $this->find_child_by_class('poll_result_tag');
		
		$code->write_php('if (' . $this->get_component_ref_code() . '->poll_exists()) {');
					
		$code->write_php('if (' . $this->get_component_ref_code() . '->can_vote()) {');

		if($form_child)
			$form_child->generate($code);
										
			$code->write_php('}else{');
		
		if ($results_child)
			$results_child->generate($code);
				
		$code->write_php('}}');
	} 
 
	public function get_dataspace()
	{
		return $this;
	} 

	public function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code();
	} 
}

?>