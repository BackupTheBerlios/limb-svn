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
require_once(LIMB_DIR . '/class/datasources/FetchSubBranchDatasource.class.php');
require_once(LIMB_DIR . '/class/core/tree/TreeSorter.class.php');

class FetchTreeDatasource extends FetchSubBranchDatasource
{
  function fetch()
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $tree->getTree();

    if(isset($params['order']))
    {
      $order = $params['order'];
      unset($params['order']);
    }
    else
      $order = array('priority' => 'ASC');

    $tree_array = parent :: fetch();
    $tree_array = TreeSorter :: sort($tree_array, $order, 'node_id', 'parent_node_id');

    $path_node = $tree->getNodeByPath($params['path']);
    if (isset($params['include_parent']) &&  (bool)$params['include_parent'])
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
          $parent_data[$parent_node_id]['children_amount'] = $tree->countChildren($parent_node_id);

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

      $tree_array[$id]['is_expanded'] = $tree->isNodeExpanded($tree_item['node_id']);
      $tree_array[$id]['is_last_child'] = $is_last_child;
      $tree_array[$id]['is_first_child'] = $is_first_child;
      $tree_array[$id]['levels_status'] = $levels_status_array;

      if(	$tree_array[$id]['class_name'] == 'image_object' ||
          $tree_array[$id]['class_name'] == 'file_object')
        $tree_array[$id]['icon'] = '/root?node_id=' . $tree_item['node_id'] . '&icon';
      elseif(isset($tree_item['icon']) &&  $tree_item['icon'])
        $tree_array[$id]['icon'] = $tree_item['icon'];
      else
        $tree_array[$id]['icon'] = '/shared/images/generic.gif';
    }

    return $tree_array;
  }
}


?>