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

class objects_access_groups_list_datasource implements datasource
{
	public function get_dataset(&$counter, $params = array())
	{
		$params['order'] = array('priority' => 'ASC');
		$groups = $this->_get_user_groups();

		$dataspace = dataspace_registry :: get('set_group_access');
		$filter_groups = $dataspace->get('filter_groups');

		if (!is_array($filter_groups) || !count($filter_groups))
			return false;

		foreach(array_keys($groups) as $key)
		{
			if (!in_array($key, $filter_groups))
				unset($groups[$key]);
		}

		return new array_dataset($groups);
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