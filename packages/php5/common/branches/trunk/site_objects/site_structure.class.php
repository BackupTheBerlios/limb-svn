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
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');

class site_structure extends site_object
{
	protected function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 1,
			'controller_class_name' => 'site_structure_controller',
		);
	}
	
	public function save_priority($params)
	{
		if(!count($params))
			return true;

		$db_table = Limb :: toolkit()->createDBTable('sys_site_object_tree');
			
		foreach($params as $node_id => $value)
		{
			$data = array();
			$data['priority'] = (int)$value;
			$db_table->update_by_id($node_id, $data);
		}				

		return true;
	}
}

?>