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

class ObjectsAccessGroupsFilterDatasource// implements OptionsDatasource
{
  function getOptionsArray()
  {
    $params['order'] = array('priority' => 'ASC');
    $user_groups = $this->_getUserGroups();

    $options_array = array();

    foreach($user_groups as $key => $user)
      $options_array[$key] = $user['title'];

    return $options_array;
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

  function getDefaultOption()
  {
    return null;
  }
}


?>