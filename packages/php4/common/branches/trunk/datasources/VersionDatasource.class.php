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

class VersionDatasource implements Datasource
{
  function getDataset(&$counter, $params=array())
  {
    $counter = 0;

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    if (!$version = $request->get('version'))
      return new EmptyDataset();

    if (!$node_id = $request->get('version_node_id'))
      return new EmptyDataset();

    $version = (int)$version;
    $node_id = (int)$node_id;

    $datasource =& $toolkit->getDatasource('SingleObjectDatasource');
    $datasource->setNodeId($node_id);

    if(!$site_object = wrapWithSiteObject($datasource->fetch()))
      return new EmptyDataset();

    if(!is_subclass_of($site_object, 'ContentObject'))
      return new EmptyDataset();

    if(($version_data = $site_object->fetchVersion($version)) === false)
      return new EmptyDataset();

    $result = array();

    foreach($version_data as $attrib => $value)
    {
      $data['attribute'] = $attrib;
      $data['value'] = $value;
      $result[] = $data;
    }

    return new ArrayDataset($result);
  }
}


?>
