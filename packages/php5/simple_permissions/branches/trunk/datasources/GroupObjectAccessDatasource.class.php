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
require_once(LIMB_DIR . '/class/datasources/FetchTreeDatasource.class.php');

class GroupObjectAccessDatasource extends FetchTreeDatasource
{
  protected function _fetch(&$counter, $params)
  {
    $tree_array = parent :: _fetch($counter, $params);

    $group_params['order'] = array('priority' => 'ASC');
    $user_groups = $this->_getUserGroups();

    $dataspace = DataspaceRegistry :: get('set_group_access');
    $groups = $dataspace->get('filter_groups');

    if (!is_array($groups) ||  !count($groups))
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
        $tree_array[$id]['groups'][$group_id]['access_selector_name'] = 'policy[' . $object_id . '][' .  $group_id . ']';
    }

    return $tree_array;
  }

  protected function _getUserGroups()
  {
    $datasource = Limb :: toolkit()->getDatasource('SiteObjectsBranchDatasource');
    $datasource->setPath('/root/user_groups');
    $datasource->setSiteObjectClassName('user_group');
    $datasource->setRestrictByClass();

    return $datasource->fetch();
  }
}


?>