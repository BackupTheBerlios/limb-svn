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
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');

class controller_template_actions_list_datasource extends datasource
{
	function & get_dataset($params = array())
	{
	  $request = request :: instance();
	  
		if(!$controller_id = $request->get_attribute('controller_id'))
			return new array_dataset();
			
		$db_table =& db_table_factory :: instance('sys_controller');
		$controller_data = $db_table->get_row_by_id($controller_id);
		
		if (!$controller_data)
			return new array_dataset();

		$site_object_controller =& site_object_controller :: create($controller_data['name']);			
		
		$actions = $site_object_controller->get_actions_definitions();
		
		$result = array();
		foreach($actions as $action => $action_params)
		{
			if (!isset($action_params['can_have_access_template']) || !$action_params['can_have_access_template'])
				continue;

			if(isset($action_params['action_name']))
				$result[$action]['action_name'] = $action_params['action_name'];
			else
				$result[$action]['action_name'] = str_replace('_', ' ', strtoupper($action{0}) . substr($action, 1));				
		}
		
		return new array_dataset($result);
	}
}


?>