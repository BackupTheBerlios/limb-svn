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
require_once(LIMB_DIR . 'class/datasources/datasource.class.php');

class class_group_action_access_datasource extends datasource
{
	function & get_dataset($params = array())
	{
	  $request = request :: instance();
	  
		if(!$class_id = $request->get_attribute('class_id'))
			return new array_dataset();
		
		$db_table =& db_table_factory :: instance('sys_class');
		$class_data = $db_table->get_row_by_id($class_id);
		
		if (!$class_data)
			return new array_dataset();

		$c =& site_object_factory :: instance($class_data['class_name']);	
		
		$site_object_controller =& $c->get_controller();			
		
		$actions = $site_object_controller->get_actions_definitions();
		
		$user_groups =& fetch_sub_branch('/root/user_groups', 'user_group', $counter);
		
		$result = array();
		foreach($actions as $action => $action_params)
		{
			if(isset($action_params['action_name']))
				$result[$action]['action_name'] = $action_params['action_name'];
			else
				$result[$action]['action_name'] = str_replace('_', ' ', strtoupper($action{0}) . substr($action, 1));
				
			$result[$action]['permissions_required'] = $action_params['permissions_required'];
			
			foreach($user_groups as $group_id => $group_data)
			{
				$result[$action]['groups'][$group_id]['selector_name'] = 'policy[' . $group_id . '][' . $action . ']';
			}
		}
		
		return new array_dataset($result);
	}
}


?>
