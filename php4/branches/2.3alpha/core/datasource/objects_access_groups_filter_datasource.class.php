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

class objects_access_groups_filter_datasource extends datasource
{
	function get_options_array()
	{
		$params['order'] = array('priority' => 'ASC');
		$user_groups =& fetch_sub_branch('/root/user_groups', 'user_group', $counter, $params);

		$options_array = array();
		
		foreach($user_groups as $key => $user)
			$options_array[$key] = $user['title'];
		
		return $options_array;
	}
	
	function get_default_option()
	{
		return null;
	}	
}


?>