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
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class site_structure extends site_object
{
	function site_structure()
	{
		parent :: site_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 1,
			'controller_class_name' => 'site_structure_controller',
		);
	}
	
	function save_priority($params)
	{
		if(!count($params))
			return true;

		$db_table =& db_table_factory :: instance('sys_site_object_tree');
			
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