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

class ClassGroupAccessTemplateDatasource// implements Datasource
{
  function getDataset(&$counter, $params = array())
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    if(!$class_id = $request->getRequest()->get('class_id'))
      return new ArrayDataset();

    $db_table = $toolkit->createDBTable('SysClass');
    $class_data = $db_table->getRowById($class_id);

    if (!$class_data)
      return new ArrayDataset();

    $site_object =& $toolkit->createSiteObject($class_data['ClassName']);

    $site_object_controller =& $site_object->getController();

    $actions = $site_object_controller->getActionsDefinitions();

    $user_groups = $this->_getUserGroups();

    $result = array();

    foreach($user_groups as $group_id => $group_data)
    {
      foreach($actions as $action => $action_params)
      {
        if (!isset($action_params['can_have_access_template']) ||  !$action_params['can_have_access_template'])
          continue;

        if(isset($action_params['action_name']))
          $result[$group_id]['actions'][$action]['action_name'] = $action_params['action_name'];
        else
          $result[$group_id]['actions'][$action]['action_name'] = str_replace('_', ' ', strtoupper($action{0}) . substr($action, 1));

        $result[$group_id]['group_name'] = $group_data['identifier'];
        $result[$group_id]['actions'][$action]['access_selector_name'] = 'template[' . $action . '][' . $group_id . ']';
      }
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