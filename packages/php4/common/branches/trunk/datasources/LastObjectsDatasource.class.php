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
require_once(LIMB_DIR . '/core/datasources/FetchDatasource.class.php');

class LastObjectsDatasource extends FetchDatasource
{
  function _fetch(&$counter, $params)
  {
    $result = parent :: _fetch($counter, $params);

    if (!count($result))
      return $result;

    $this->_processLoadedItems($result);

    return $result;
  }

  function _processLoadedItems(&$items)
  {
    if (!count($items))
      return $items;

    $parent_node_ids = array();

    foreach($items as $key => $data)
    {
      if (!isset($parent_node_ids[$data['parent_node_id']]))
      {
        $parent_node_ids[$data['parent_node_id']] = $data['parent_node_id'];
      }
    }

    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('SingleObjectsByNodeIdsDatasource');
    $datasource->setUseNodeIdsAsKeys();
    $datasource->setNodeIds($parent_node_ids);

    $parents = $datasource->fetch();

    foreach($items as $key => $data)
    {
      $parent_data = $parents[$data['parent_node_id']];
      $items[$key]['parent_title'] = $parent_data['title'];
      $items[$key]['parent_path'] = $parent_data['path'];
    }
  }
}


?>