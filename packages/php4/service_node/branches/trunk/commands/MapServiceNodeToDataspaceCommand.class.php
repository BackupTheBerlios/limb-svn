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
require_once(LIMB_DIR . '/core/commands/StateMachineCommand.class.php');
require_once(LIMB_DIR . '/core/commands/MapObjectToDataspaceCommand.class.php');
require_once(LIMB_DIR . '/core/ServiceLocation.class.php');
require_once(LIMB_DIR . '/core/NodeConnection.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');

class MapServiceNodeToDataspaceCommand
{
  var $service_node;

  function MapServiceNodeToDataspaceCommand(&$service_node)
  {
    $this->service_node =& $service_node;
  }

  function perform()
  {
    if(!is_a($this->service_node, 'ServiceNode'))
      return LIMB_STATUS_ERROR;

    $node =& $this->service_node->getPart('node');
    $service =& $this->service_node->getPart('service');

    $node_map = array('id' => 'node_id',
                      'parent_id' => 'parent_node_id',
                      'identifier' => 'identifier');

    $service_map = array('title' => 'title',
                         'name' => 'service_name');

    $map_node_command = new MapObjectToDataspaceCommand($node_map, $node);
    $map_node_command->perform();

    $map_service_command = new MapObjectToDataspaceCommand($service_map, $service);
    $map_service_command->perform();

    return LIMB_STATUS_OK;
  }
}

?>
