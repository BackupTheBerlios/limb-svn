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

require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . 'core/datasource/fetch_sub_branch_datasource.class.php');
require_once(LIMB_DIR . 'core/tree/tree_sorter.class.php');

class fetch_tree_datasource extends fetch_sub_branch_datasource
{
	function & _fetch(&$counter, $params)
	{
		$tree =& tree :: instance();

		if(isset($params['order']))
		{
			$order = $params['order'];
			unset($params['order']);
		}
		else
			$order = array('priority' => 'ASC');
		
		$tree_array =& parent :: _fetch($counter, $params);
		$tree_array =& tree_sorter :: sort($tree_array, $order, 'node_id', 'parent_node_id');
		
		$path_node = $tree->get_node_by_path($params['path']);
		if (isset($params['include_parent']) && (bool)$params['include_parent'])
			$path_node_level = $path_node['level'] - 1;
		else
			$path_node_level = $path_node['level'];
			
		$levels_status_array = array();
		$size = count($tree_array);
		$current_pos = 0;
		
		$parent_data = array();

		foreach($tree_array as $id => $tree_item)
		{	
			$parent_node_id = $tree_item['parent_node_id'];
			if(!isset($parent_data[$parent_node_id]))
			{
				if($parent_node_id == 0)
					$parent_data[$parent_node_id]['children_amount'] = 1;
				else
					$parent_data[$parent_node_id]['children_amount'] = $tree->count_accessible_children($parent_node_id);
					
				$parent_data[$parent_node_id]['counter'] = 0;
			}
			
			$parent_data[$parent_node_id]['counter']++;
			
			if ($parent_data[$parent_node_id]['counter'] == 1)
				$is_first_child = true;
			else	
				$is_first_child = false;

			if($parent_data[$parent_node_id]['counter'] == $parent_data[$parent_node_id]['children_amount'])
				$is_last_child = true;
			else
				$is_last_child = false;
					
			$tree_array[$id]['level'] = $tree_array[$id]['level'] - $path_node_level;
			$levels_status_array[$tree_item['level'] - $path_node_level] = $is_last_child;		
			
			$tree_array[$id]['level_' . $tree_array[$id]['level']] = 1;
			
			$tree_array[$id]['is_expanded'] = $tree->is_node_expanded($tree_item['node_id']);
			$tree_array[$id]['is_last_child'] = $is_last_child;
			$tree_array[$id]['is_first_child'] = $is_first_child;
			$tree_array[$id]['levels_status'] = $levels_status_array;
			
			if(	$tree_array[$id]['class_name'] == 'image_object' || 
					$tree_array[$id]['class_name'] == 'file_object')
				$tree_array[$id]['icon'] = '/root?node_id=' . $tree_item['node_id'] . '&icon';
			elseif(isset($tree_item['icon']) && $tree_item['icon'])
				$tree_array[$id]['icon'] = $tree_item['icon'];
			else
				$tree_array[$id]['icon'] = '/shared/images/generic.gif';
		}
		
		return $tree_array;
	}		
}


?>