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
require_once(LIMB_DIR . '/core/commands/MapDataspaceToObjectCommand.class.php');
require_once(LIMB_DIR . '/core/commands/StateMachineCommand.class.php');

class MapDataspaceToServiceNodeCommand
{
  var $service_node;

  function MapDataspaceToServiceNodeCommand(&$service_node)
  {
    $this->service_node =& $service_node;
  }

  function perform()
  {
    if(!is_a($this->service_node,'ServiceNode'))
      return LIMB_STATUS_ERROR;

    $node =& $this->service_node->getPart('node');
    $service =& $this->service_node->getPart('service');

    $this->_processPath();

    $node_map = array('parent_node_id' => 'parent_id',
                      'identifier' => 'identifier');

    $map_node_command = new MapDataspaceToObjectCommand($node_map, $node);
    $map_node_command->perform();

    $service_map = array('title' => 'title',
                         'service_name' => 'name');

    $map_service_command = new MapDataspaceToObjectCommand($service_map, $service);
    $map_service_command->perform();

    return LIMB_STATUS_OK;
  }

  function _processPath()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    if (!$path = $dataspace->get('path'))
      return;

    $tree =& $toolkit->getTree();
    if(!$node = $tree->getNodeByPath($path))
      return;

    $dataspace->set('parent_node_id', $node['id']);
  }
}

?>
