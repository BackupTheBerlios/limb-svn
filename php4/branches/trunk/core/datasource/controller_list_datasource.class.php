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
require_once(LIMB_DIR . 'core/datasource/datasource.class.php');

class controller_list_datasource extends datasource
{
	function & get_dataset($params = array())
	{
		if(!$arr = fetch_requested_object())
			return new array_dataset();
		
		$db_table =& db_table_factory :: instance('sys_controller');
		$controllers = $db_table->get_list('', 'name');
		$result = array();
		$params = array();

		foreach($controllers as $controller_id => $controller_data)
		{
			$result[$controller_id] = $controller_data;
			$result[$controller_id]['path'] = $arr['path'];
			$params['controller_id'] = $controller_id;
			$result[$controller_id]['node_id'] = $arr['node_id'];
			
			foreach($arr['actions'] as $action_name => $action_params)
				$arr['actions'][$action_name]['extra'] = $params;

			$result[$controller_id]['actions'] = $arr['actions'];
		}
		
		return new array_dataset($result);
	}
}


?>