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

class user_membership_datasource extends datasource
{
	function user_membership_datasource()
	{
		parent :: datasource();
	}

	function & get_dataset(&$counter, $params = array())
	{
		$user_groups =& fetch_sub_branch('/root/user_groups', 'user_group', $counter);
		
		foreach($user_groups as $id => $group_data)
		{
			$result[$group_data['id']] = $group_data;
			$result[$group_data['id']]['selector_name'] = 'membership[' . $group_data['id'] . ']';
		}	
		
		return new array_dataset($result);
	}
}


?>