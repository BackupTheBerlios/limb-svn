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

class ClassListDatasource// implements Datasource
{
  function getDataset(&$counter, $params = array())
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    if(!$arr = $datasource->fetch())
      return new ArrayDataset();

    $db_table =& $toolkit->createDBTable('SysClass');
    $classes = $db_table->getList('', 'class_name');

    $result = array();
    $params = array();

    foreach($classes as $class_id => $class_data)
    {
      $result[$class_id] = $class_data;
      $result[$class_id]['path'] = $arr['path'];
      $params['class_id'] = $class_id;
      $result[$class_id]['node_id'] = $arr['node_id'];

      foreach($arr['actions'] as $action_name => $action_params)
        $arr['actions'][$action_name]['extra'] = $params;

      $result[$class_id]['actions'] = $arr['actions'];
    }

    $counter = sizeof($result);
    return new ArrayDataset($result);
  }
}


?>