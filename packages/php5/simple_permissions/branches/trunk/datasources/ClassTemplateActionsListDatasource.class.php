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

class ClassTemplateActionsListDatasource implements Datasource
{
  public function getDataset(&$counter, $params = array())
  {
    $request = Limb :: toolkit()->getRequest();

    if(!$class_id = $request->get('class_id'))
      return new ArrayDataset();

    $db_table = Limb :: toolkit()->createDBTable('SysClass');
    $class_data = $db_table->getRowById($class_id);

    if (!$class_data)
      return new ArrayDataset();

    $site_object = Limb :: toolkit()->createSiteObject($class_data['ClassName']);

    $site_object_controller = $site_object->getController();

    $actions = $site_object_controller->getActionsDefinitions();

    $result = array();
    foreach($actions as $action => $action_params)
    {
      if (!isset($action_params['can_have_access_template']) ||  !$action_params['can_have_access_template'])
        continue;

      if(isset($action_params['action_name']))
        $result[$action]['action_name'] = $action_params['action_name'];
      else
        $result[$action]['action_name'] = str_replace('_', ' ', strtoupper($action{0}) . substr($action, 1));
    }

    $counter = sizeof($result);
    return new ArrayDataset($result);
  }
}


?>