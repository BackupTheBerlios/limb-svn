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
require_once(LIMB_DIR . '/core/datasource/fetch_tree_datasource.class.php');

class site_map_datasource extends fetch_tree_datasource
{	
	function & _fetch(&$counter, $params)
	{
		$tree_array =& parent :: _fetch($counter, $params);
		
		if(!count($tree_array))
			return array();
		
		$result = array();

		$current_date = date('Y-m-d', time());
		$prev_item_key = null;
		
		foreach($tree_array as $id => $tree_item)
		{	
			$tree_item['is_expanded'] = true;
			
			if(!isset($tree_item['url']))
				$tree_item['url'] = $tree_item['path'];

			if (!$tree_item['is_last_child'])
			{
				$prev_item_parent_id = $tree_item['parent_node_id'];
				$prev_item_key = $tree_item['id'];
			}
				
			if (!isset($tree_item['start_date']))
			{
				$result[$id] = $tree_item;
				continue;
			}	
				
			if (($tree_item['start_date'] <= $current_date) && ($tree_item['finish_date'] >= $current_date))
			{
				$result[$id] = $data;
				continue;
			}
			
			if ($tree_item['is_last_child'] && $tree_item['parent_node_id'] == $prev_item_parent_id)
				$result[$prev_item_key]['is_last_child'] = true;
		}
		
		return $result;
	}
}


?>