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


class actions_tag_info
{
	var $tag = 'actions';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'actions_tag';
} 

register_tag(new actions_tag_info());

class actions_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/actions_component';


	function pre_generate(&$code)
	{
		parent :: pre_generate($code);

		$actions_array = '$' . $code->get_temp_variable();
		$node_id = '$' . $code->get_temp_variable();
		$node = '$' . $code->get_temp_variable();
		$code->write_php("{$actions_array} = ".  $this->parent->get_dataspace_ref_code() . '->get("actions");'."\n");
		
		$code->write_php("{$node_id} = " . $this->parent->get_dataspace_ref_code() . '->get("node_id");'. "\n");

		$code->write_php("if(!{$node_id}){ 
			{$node} =& map_current_request_to_node(); {$node_id} = {$node}['id'];}\n");
				
		$code->write_php($this->get_component_ref_code() . "->set_actions({$actions_array});\n");

		$code->write_php($this->get_component_ref_code() . "->set_node_id({$node_id});\n");
		
		$code->write_php($this->get_component_ref_code() . '->prepare();'."\n");

		$code->write_php('if (' . $this->get_component_ref_code() . '->next()) {');
	} 

	function post_generate(&$code)
	{
		$code->write_php('}');
	} 


	function &get_dataspace()
	{
		return $this;
	} 


	function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code() . '->dataset';
	} 
} 

?>