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
require_once(LIMB_DIR . '/class/datasources/datasource.interface.php');

class class_group_access_template_datasource implements datasource
{
	public function get_dataset(&$counter, $params = array())
	{
		if(!$class_id = Limb :: toolkit()->getRequest()->get('class_id'))
			return new array_dataset();

		$db_table = Limb :: toolkit()->createDBTable('sys_class');
		$class_data = $db_table->get_row_by_id($class_id);

		if (!$class_data)
			return new array_dataset();

		$site_object = Limb :: toolkit()->createSiteObject($class_data['class_name']);

		$site_object_controller = $site_object->get_controller();

		$actions = $site_object_controller->get_actions_definitions();

		$user_groups = $this->_get_user_groups();

		$result = array();

		foreach($user_groups as $group_id => $group_data)
		{
			foreach($actions as $action => $action_params)
			{
				if (!isset($action_params['can_have_access_template']) || !$action_params['can_have_access_template'])
					continue;

				if(isset($action_params['action_name']))
					$result[$group_id]['actions'][$action]['action_name'] = $action_params['action_name'];
				else
					$result[$group_id]['actions'][$action]['action_name'] = str_replace('_', ' ', strtoupper($action{0}) . substr($action, 1));

				$result[$group_id]['group_name'] = $group_data['identifier'];
				$result[$group_id]['actions'][$action]['access_selector_name'] = 'template[' . $action . '][' . $group_id . ']';
			}
		}

		$counter = sizeof($result);
		return new array_dataset($result);
	}
  
  protected function _get_user_groups()
  {
    $datasource = Limb :: toolkit()->getDatasource('site_objects_branch_datasource');
    $datasource->set_path('/root/user_groups');
    $datasource->set_site_object_class_name('user_group');
    $datasource->set_restrict_by_class();
    
		return $datasource->fetch();
  }
}


?>