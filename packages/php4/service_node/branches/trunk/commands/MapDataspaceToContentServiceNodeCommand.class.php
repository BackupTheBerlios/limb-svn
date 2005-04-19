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

  function MapDataspaceToContentServiceNodeCommand($entity_field_name, $content_map)
  {
    $this->entity_field_name = $entity_field_name;
    $this->content_map = $content_map;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    if(!$entity =& $context->getObject($this->entity_field_name))
      return LIMB_STATUS_ERROR;

    if(!$content =& $entity->getPart('content'))
      return LIMB_STATUS_ERROR;

    $map_content_command = new MapDataspaceToObjectCommand($this->content_map, $content);

    $map_service_node_command = new MapDataspaceToServiceNodeCommand($this->entity_field_name);

    $map_command = new StateMachineCommand();
    $map_command->registerState('first', $map_content_command, array(LIMB_STATUS_OK => 'second'));
    $map_command->registerState('second', $map_service_node_command);

    return $map_command->perform($context);
  }
}

?>
