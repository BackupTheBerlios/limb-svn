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
class actions_tag_info
{
	public $tag = 'actions';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'actions_tag';
} 

register_tag(new actions_tag_info());

class actions_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/actions_component';
	}
	
	public function pre_generate(&$code)
	{
		parent :: pre_generate($code);

		$actions_array = '$' . $code->get_temp_variable();
		$node_id = '$' . $code->get_temp_variable();
		$node = '$' . $code->get_temp_variable();
		$code->write_php("{$actions_array} = ".  $this->parent->get_dataspace_ref_code() . '->get("actions");'."\n");
		
		$code->write_php("{$node_id} = " . $this->parent->get_dataspace_ref_code() . '->get("node_id");'. "\n");

		$code->write_php("if(!{$node_id}){ 
			{$node} =& map_request_to_node(); {$node_id} = {$node}['id'];}\n");
				
		$code->write_php($this->get_component_ref_code() . "->set_actions({$actions_array});\n");

		$code->write_php($this->get_component_ref_code() . "->set_node_id({$node_id});\n");
		
		$code->write_php($this->get_component_ref_code() . '->prepare();'."\n");

		$code->write_php('if (' . $this->get_component_ref_code() . '->next()) {');
	} 

	public function post_generate(&$code)
	{
		$code->write_php('}');
	} 

	public function get_dataspace()
	{
		return $this;
	} 

	public function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code() . '->dataset';
	} 
} 

?>