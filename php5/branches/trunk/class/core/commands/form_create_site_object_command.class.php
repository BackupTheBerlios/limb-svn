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

class form_create_site_object_command extends form_command
{
	protected function _register_validation_rules($validator)
	{
    $dataspace = Limb :: toolkit()->getDataspace();
    
		if (($parent_node_id = $dataspace->get('parent_node_id')) === null)
		{
		  if(!$parent_object_data = $this->_load_parent_object_data())
		  	return;

			$parent_node_id = $parent_object_data['parent_node_id'];
		}

		$validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_node_id_rule', 'parent_node_id'));
		$validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_identifier_rule', 'identifier', $parent_node_id));    
	} 

	protected function _load_parent_object_data()
	{
    $toolkit = Limb :: toolkit();
		return $toolkit->getFetcher()->fetch_requested_object($toolkit->getRequest());
	}
}
?>