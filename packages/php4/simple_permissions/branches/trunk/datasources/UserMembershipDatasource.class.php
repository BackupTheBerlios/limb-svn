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

class UserMembershipDatasource implements Datasource
{
  function getDataset(&$counter, $params = array())
  {
    $user_groups = $this->_getUserGroups();

    $result = array();
    foreach($user_groups as $id => $group_data)
    {
      $result[$group_data['id']] = $group_data;
      $result[$group_data['id']]['selector_name'] = 'membership[' . $group_data['id'] . ']';
    }

    $counter = sizeof($result);
    return new ArrayDataset($result);
  }

  function _getUserGroups()
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('SiteObjectsBranchDatasource');
    $datasource->setPath('/root/user_groups');
    $datasource->setSiteObjectClassName('user_group');
    $datasource->setRestrictByClass();

    return $datasource->fetch();
  }
}
?>