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

class objects_access_groups_list_datasource implements datasource
{
	public function get_dataset(&$counter, $params = array())
	{
		$params['order'] = array('priority' => 'ASC');
		$groups = Limb :: toolkit()->getFetcher()->fetch_sub_branch('/root/user_groups', 'user_group', $counter, $params);

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
}


?>