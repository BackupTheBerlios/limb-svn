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
require_once(LIMB_DIR . '/class/datasources/datasource.interface.php');

class user_membership_datasource implements datasource
{
  public function get_dataset(&$counter, $params = array())
  {
    $user_groups = $this->_get_user_groups();

    $result = array();
    foreach($user_groups as $id => $group_data)
    {
      $result[$group_data['id']] = $group_data;
      $result[$group_data['id']]['selector_name'] = 'membership[' . $group_data['id'] . ']';
    }

    $counter = sizeof($result);
    return new array_dataset($result);
  }

  protected function _get_user_groups()
  {
    $datasource = Limb :: toolkit()->getDatasource('site_objects_branch_datasource');
    $datasource->set_path('/root/user_groups');
    $datasource->set_site_object_class_name('user_group');
    $datasource->set_restrict_by_class();

    return $datasource->fetch();
  }
}
?>