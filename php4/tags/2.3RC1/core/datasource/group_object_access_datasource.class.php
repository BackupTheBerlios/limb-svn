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

class group_object_access_datasource extends fetch_tree_datasource
{
  function & _fetch(&$counter, $params)
  {
    $tree_array = parent :: _fetch($counter, $params);
    $group_params['order'] = array('priority' => 'ASC');
    $user_groups =& fetch_sub_branch('/root/admin/user_groups', 'user_group', $counter, $group_params);

    $dataspace =& dataspace_registry :: get('set_group_access');

    $groups = $dataspace->get('filter_groups');

    if (!is_array($groups) || !count($groups))
      return $tree_array;

    foreach(array_keys($user_groups) as $key)
    {
      if (!in_array($key, $groups))
        unset($user_groups[$key]);
    }

    foreach($tree_array as $id => $node)
    {
      $object_id = $node['id'];
      foreach($user_groups as $group_id => $group_data)
      {
        $tree_array[$id]['groups'][$group_id]['selector_name'] = 'policy[' . $object_id . '][' .  $group_id . ']';
      }
    }

    return $tree_array;
  }

}


?>