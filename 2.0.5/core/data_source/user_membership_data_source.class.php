<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: user_membership_data_source.class.php 435 2004-02-11 16:22:50Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/data_source/data_source.class.php');

class user_membership_data_source extends data_source
{
	function user_membership_data_source()
	{
		parent :: data_source();
	}

	function & get_data_set($params = array())
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