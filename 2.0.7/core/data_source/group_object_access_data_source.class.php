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
require_once(LIMB_DIR . 'core/data_source/fetch_tree_data_source.class.php');

class group_object_access_data_source extends fetch_tree_data_source
{
	function group_object_access_data_source()
	{
		parent :: fetch_tree_data_source();
	}

	function & _fetch(&$counter, $params)
	{
		$tree_array = parent :: _fetch($counter, $params);
		$user_groups =& fetch_sub_branch('/root/user_groups', 'user_group', $counter);
		
		foreach($tree_array as $id => $node)
		{
			$object_id = $node['id'];
			foreach($user_groups as $group_id => $group_data)
			{
				$tree_array[$id]['groups'][$group_id]['read_selector_name'] = 'policy[' . $object_id . '][' .  $group_id . '][r]';
				$tree_array[$id]['groups'][$group_id]['write_selector_name'] = 'policy[' . $object_id . '][' . $group_id . '][w]';
			}
		}
		return $tree_array;
	}
	
}


?>