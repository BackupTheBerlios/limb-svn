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
require_once(LIMB_SERVICE_NODE_DIR . '/commands/MapServiceNodeToDataspaceCommand.class.php');

class MapContentServiceNodeToDataspaceCommand
{
  var $service_node;
  var $content_map;

  function MapContentServiceNodeToDataspaceCommand(&$service_node, $content_map)
  {
    $this->service_node =& $service_node;
    $this->content_map = $content_map;
  }

  function perform()
  {
    if(!is_a($this->service_node, 'ContentServiceNode'))
      return LIMB_STATUS_ERROR;

    $content =& $this->service_node->getContentPart();

    $map_content_command = new MapObjectToDataspaceCommand($this->content_map, $content);
    $map_content_command->perform();

    $map_service_node_command = new MapServiceNodeToDataspaceCommand($this->service_node);
    $map_service_node_command->perform();

    return LIMB_STATUS_OK;
  }
}

?>
