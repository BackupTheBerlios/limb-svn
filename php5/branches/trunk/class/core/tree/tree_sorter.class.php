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
require_once(LIMB_DIR . 'class/lib/util/complex_array.class.php');

class tree_sorter
{  
  static function sort($tree_array, $sort_params, $id_hash = 'id', $parent_hash = 'parent_id')
  {
		$item = reset($tree_array);
		$parent_id = $item[$parent_hash];
		
		$sorted_tree_array = array();
 		
		tree_sorter :: _do_sort($tree_array, $sorted_tree_array, $sort_params, $parent_id, $id_hash, $parent_hash);
		
		return $sorted_tree_array;
  }
  
  static function _do_sort($tree_array, & $sorted_tree_array, $sort_params, $parent_id, $id_hash, $parent_hash)
  {
 		$children = array();
 		
  	foreach($tree_array as $index => $item)
  	{
  		if($item[$parent_hash] == $parent_id)
  		{
  			$children[] = $item;
  		  unset($tree_array[$index]);
  		}
  	}

  	if(!($count = sizeof($children)))
  		return;
		
		$children = complex_array :: sort_array($children, $sort_params);
		
		if(!$sorted_tree_array)
		{
			$sorted_tree_array = $children;
		}
		else
		{
			$ids = complex_array :: get_column_values($id_hash, $sorted_tree_array);
			
			$offset = array_search($parent_id, $ids) + 1;
			
			array_splice($sorted_tree_array, $offset, 0, $children);
		}
		
    for($i=0; $i < $count; $i++)
    {
	   	tree_sorter :: _do_sort($tree_array, $sorted_tree_array, $sort_params, $children[$i][$id_hash], $id_hash, $parent_hash);
    }
  }  
}

?>