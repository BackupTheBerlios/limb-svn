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
require_once(LIMB_DIR . 'class/datasources/datasource.interface.php');

class class_group_action_access_datasource implements datasource
{
	public function get_dataset(&$counter, $params = array())
	{
	  $request = request :: instance();

		if(!$class_id = $request->get('class_id'))
			return new array_dataset();

		$db_table = db_table_factory :: create('sys_class');
		$class_data = $db_table->get_row_by_id($class_id);

		if (!$class_data)
			return new array_dataset();

		$site_object = site_object_factory :: create($class_data['class_name']);

		$site_object_controller = $site_object->get_controller();

		$actions = $site_object_controller->get_actions_definitions();

		$user_groups = fetcher :: instance()->fetch_sub_branch('/root/user_groups', 'user_group', $counter);

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

		$counter = sizeof($result);
		return new array_dataset($result);
	}
}


?>
