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

class user_membership_datasource implements datasource
{
	public function get_dataset(&$counter, $params = array())
	{
		$user_groups = fetch_sub_branch('/root/user_groups', 'user_group', $counter, $params);
		
		$result = array();
		foreach($user_groups as $id => $group_data)
		{
			$result[$group_data['id']] = $group_data;
			$result[$group_data['id']]['selector_name'] = 'membership[' . $group_data['id'] . ']';
		}	
		
		$counter = sizeof($result);
		return new array_dataset($result);
	}
}
?>