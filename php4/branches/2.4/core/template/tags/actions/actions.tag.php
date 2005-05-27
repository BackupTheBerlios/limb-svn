<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/template/tags/datasource/datasource.tag.php');

class actions_tag_info
{
  var $tag = 'actions';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'actions_tag';
}

register_tag(new actions_tag_info());

class actions_tag extends datasource_tag
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
      {$node} =& map_request_to_node(); {$node_id} = {$node}['id'];}\n");

    $code->write_php($this->get_component_ref_code() . "->set_actions({$actions_array});\n");

    $code->write_php($this->get_component_ref_code() . "->set_node_id({$node_id});\n");
  }
}
?>