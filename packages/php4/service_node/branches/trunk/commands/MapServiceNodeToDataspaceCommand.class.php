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
  var $entity_field_name;

  function MapServiceNodeToDataspaceCommand($entity_field_name)
  {
    $this->entity_field_name = $entity_field_name;
  }

  function perform(&$context)
  {
    if(!$entity =& $context->getObject($this->entity_field_name))
      return LIMB_STATUS_ERROR;

    if(!$node =& $entity->getPart('node'))
      return LIMB_STATUS_ERROR;

    if(!$service =& $entity->getPart('service'))
      return LIMB_STATUS_ERROR;

    $node_map = array('id' => 'node_id',
                      'parent_id' => 'parent_node_id',
                      'identifier' => 'identifier');

    $service_map = array('title' => 'title',
                         'name' => 'service_name');

    $map_node_command = new MapObjectToDataspaceCommand($node_map, $node);
    $map_service_command = new MapObjectToDataspaceCommand($service_map, $service);

    $map_command = new StateMachineCommand();
    $map_command->registerState('first', $map_node_command, array(LIMB_STATUS_OK => 'second'));
    $map_command->registerState('second', $map_service_command);

    return $map_command->perform(new Dataspace());
  }
}

?>
