<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/form_command.class.php');

abstract class form_edit_site_object_command extends form_command
{
  protected function _register_validation_rules($validator, $dataspace)
  {
    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/tree_node_id_rule', 'parent_node_id'));
    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'identifier'));

    $object_data = $this->_load_object_data();

    if(($parent_node_id = $dataspace->get('parent_node_id')) === null)
      $parent_node_id = $object_data['parent_node_id'];

    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/tree_identifier_rule',
                                     'identifier',
                                     (int)$parent_node_id,
                                     (int)$object_data['node_id']));
  }

  protected function _init_first_time_dataspace($dataspace, $request)
  {
    $object_data = $this->_load_object_data();
    complex_array :: map($this->_define_datamap(), $object_data, $data = array());

    $dataspace->merge($data);
  }

  protected function _load_object_data()
  {
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('requested_object_datasource');
    $datasource->set_request($toolkit->getRequest());

    return $datasource->fetch();
  }

  protected function _define_datamap()
  {
    return array(
      'parent_node_id' => 'parent_node_id',
      'identifier' => 'identifier',
      'title' => 'title'
    );
  }
}
?>