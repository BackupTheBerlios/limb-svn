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

class class_template_actions_list_datasource implements datasource
{
  public function get_dataset(&$counter, $params = array())
  {
    $request = Limb :: toolkit()->getRequest();

    if(!$class_id = $request->get('class_id'))
      return new array_dataset();

    $db_table = Limb :: toolkit()->createDBTable('sys_class');
    $class_data = $db_table->get_row_by_id($class_id);

    if (!$class_data)
      return new array_dataset();

    $site_object = Limb :: toolkit()->createSiteObject($class_data['class_name']);

    $site_object_controller = $site_object->get_controller();

    $actions = $site_object_controller->get_actions_definitions();

    $result = array();
    foreach($actions as $action => $action_params)
    {
      if (!isset($action_params['can_have_access_template']) || !$action_params['can_have_access_template'])
        continue;

      if(isset($action_params['action_name']))
        $result[$action]['action_name'] = $action_params['action_name'];
      else
        $result[$action]['action_name'] = str_replace('_', ' ', strtoupper($action{0}) . substr($action, 1));
    }

    $counter = sizeof($result);
    return new array_dataset($result);
  }
}


?>