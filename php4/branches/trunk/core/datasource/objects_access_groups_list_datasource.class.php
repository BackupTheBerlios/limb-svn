<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: user_membership_datasource.class.php 324 2004-06-11 13:05:50Z pachanga $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');

class objects_access_groups_list_datasource extends datasource
{
	function & get_dataset(&$counter, $params = array())
	{
		$params['order'] = array('priority' => 'ASC');
		$user_groups =& fetch_sub_branch('/root/user_groups', 'user_group', $counter, $params);
		
		$group_params['order'] = array('priority' => 'ASC');
		$groups =& fetch('user_group', $counter, $group_params, 'fetch');
		
		$dataspace =& dataspace_registry :: get('set_group_access');
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