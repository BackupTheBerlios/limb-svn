<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/util/ComplexArray.class.php');

class TreeRsProcessor
{
  function makeNested(&$rs, $id_hash = 'id', $parent_hash = 'parent_id')
  {
    if(is_array($rs))
      $tree_array = $rs;
    else
      $tree_array = TreeRsProcessor :: _convertRs2Array($rs);

    $item = reset($tree_array);
    $nested_array = array();
    $parent_id = $item[$parent_hash];

    TreeRsProcessor :: _doMakeNested($tree_array, $nested_array, $parent_id, $id_hash, $parent_hash);

    return new ArrayDataSet($nested_array);
  }

  function sort(&$rs, $sort_params, $id_hash = 'id', $parent_hash = 'parent_id')
  {
    if(is_array($rs))
      $tree_array = $rs;
    else
      $tree_array = TreeRsProcessor :: _convertRs2Array($rs);

    $item = reset($tree_array);
    $parent_id = $item[$parent_hash];

    $sorted_tree_array = array();

    TreeRsProcessor :: _doSort($tree_array, $sorted_tree_array, $sort_params, $parent_id, $id_hash, $parent_hash);

    return new ArrayDataSet($sorted_tree_array);
  }

  function _convertRs2Array(&$rs)
  {
    $tree_array = array();

    for($rs->rewind();$rs->valid();$rs->next())
    {
      $record =& $rs->current();
      $tree_array[] = $record->export();
    }

    return $tree_array;
  }

  function _doMakeNested(&$tree_array, &$nested_array, $parent_id, $id_hash, $parent_hash)
  {
    $prev_item_id = null;

    while($item = current($tree_array))
    {
      if($item[$parent_hash] == $parent_id)
      {
        $nested_array[] = $item;
      }
      elseif($item[$parent_hash] === $prev_item_id)
      {
        $new_nested =& $nested_array[sizeof($nested_array) - 1]['children'];
        TreeRsProcessor :: _doMakeNested($tree_array, $new_nested, $item[$parent_hash], $id_hash, $parent_hash);
      }
      else
      {
        prev($tree_array);
        return;
      }

      $prev_item_id = $item[$id_hash];
      $item = next($tree_array);
    }
  }

  function _doSort($tree_array, &$sorted_tree_array, $sort_params, $parent_id, $id_hash, $parent_hash)
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

    $children = ComplexArray :: sortArray($children, $sort_params);

    if(!$sorted_tree_array)
    {
      $sorted_tree_array = $children;
    }
    else
    {
      $ids = ComplexArray :: getColumnValues($id_hash, $sorted_tree_array);

      $offset = array_search($parent_id, $ids) + 1;

      array_splice($sorted_tree_array, $offset, 0, $children);
    }

    for($i=0; $i < $count; $i++)
    {
      TreeRsProcessor :: _doSort($tree_array, $sorted_tree_array, $sort_params, $children[$i][$id_hash], $id_hash, $parent_hash);
    }
  }
}

?>