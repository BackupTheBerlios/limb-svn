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

require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');

class tree_sorter
{  
  function sort($tree_array, $sort_params)
  {
		$item = reset($tree_array);
		$parent_id = $item['parent_id'];
		
		$sorted_tree_array = array();
 		
		tree_sorter :: _do_sort($tree_array, $sorted_tree_array, $sort_params, $parent_id);
		
		return $sorted_tree_array;
  }
  
  function _do_sort(& $tree_array, & $sorted_tree_array, $sort_params, $parent_id)
  {
 		$children = array();
 		
  	foreach($tree_array as $index => $item)
  	{
  		if($item['parent_id'] == $parent_id)
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
			$ids = complex_array :: get_column_values('id', $sorted_tree_array);
			
			$offset = array_search($parent_id, $ids) + 1;
			
			array_splice($sorted_tree_array, $offset, 0, $children);
		}
		
    for($i=0; $i < $count; $i++)
    {
	   	tree_sorter :: _do_sort($tree_array, $sorted_tree_array, $sort_params, $children[$i]['id']);
    }
  }  
}

?>