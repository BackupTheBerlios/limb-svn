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
require_once(LIMB_SERVICE_NODE_DIR . '/commands/MapDataspaceToServiceNodeCommand.class.php');

class MapDataspaceToContentServiceNodeCommand
{
  var $content_map;
  var $service_node;

  function MapDataspaceToContentServiceNodeCommand(&$service_node, $content_map)
  {
    $this->service_node =& $service_node;
    $this->content_map = $content_map;
  }

  function perform()
  {
    if(!is_a($this->service_node, 'ContentServiceNode'))
      return LIMB_STATUS_ERROR;

    $content =& $entity->getPart('content');

    $map_content_command = new MapDataspaceToObjectCommand($this->content_map, $content);
    $map_content_command->perform();

    $map_service_node_command = new MapDataspaceToServiceNodeCommand($this->service_node);
    $map_service_node_command->perform();

    return LIMB_STATUS_OK;
  }
}

?>
