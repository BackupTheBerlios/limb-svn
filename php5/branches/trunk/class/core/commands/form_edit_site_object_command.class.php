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
require_once(LIMB_DIR . 'class/core/commands/form_command.class.php');

abstract class form_edit_site_object_command extends form_command
{
	protected function _register_validation_rules($validator)
	{
		$validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_node_id_rule', 'parent_node_id'));
		$validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'identifier'));
    
    $dataspace = Limb :: toolkit()->getDataspace();
    $object_data = $this->_load_object_data();
    
		if(($parent_node_id = $dataspace->get('parent_node_id')) === null)
			$parent_node_id = $object_data['parent_node_id'];

		$validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_identifier_rule', 
                                     'identifier', 
                                     (int)$parent_node_id, 
                                     (int)$object_data['node_id']));
	}

	protected function _load_object_data()
	{
    $toolkit = Limb :: toolkit();
		return $toolkit->getFetcher()->fetch_requested_object($toolkit->getRequest());
	}
}
?>