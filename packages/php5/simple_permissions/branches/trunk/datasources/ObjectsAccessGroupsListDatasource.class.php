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
require_once(LIMB_DIR . '/class/datasources/Datasource.interface.php');

class ObjectsAccessGroupsListDatasource implements Datasource
{
  public function getDataset(&$counter, $params = array())
  {
    $params['order'] = array('priority' => 'ASC');
    $groups = $this->_getUserGroups();

    $dataspace = DataspaceRegistry :: get('set_group_access');
    $filter_groups = $dataspace->get('filter_groups');

    if (!is_array($filter_groups) ||  !count($filter_groups))
      return false;

    foreach(array_keys($groups) as $key)
    {
      if (!in_array($key, $filter_groups))
        unset($groups[$key]);
    }

    return new ArrayDataset($groups);
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