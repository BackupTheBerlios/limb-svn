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
  var $map = array();

  function MapDataspaceToServiceNodeCommand($entity_field_name)
  {
    $this->entity_field_name = $entity_field_name;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    if(!$entity =& $context->getObject($this->entity_field_name))
      return LIMB_STATUS_ERROR;

    if(!$node =& $entity->getPart('node'))
      return LIMB_STATUS_ERROR;

    if(!$service =& $entity->getPart('service'))
      return LIMB_STATUS_ERROR;

    $this->_processPath();

    $node_map = array('parent_node_id' => 'parent_id',
                      'identifier' => 'identifier');

    $map_node_command = new MapDataspaceToObjectCommand($node_map, $node);

    $service_map = array('title' => 'title',
                         'service_name' => 'name');

    $map_service_command = new MapDataspaceToObjectCommand($service_map, $service);

    $map_command = new StateMachineCommand();
    $map_command->registerState('first', $map_node_command, array(LIMB_STATUS_OK => 'second'));
    $map_command->registerState('second', $map_service_command);

    return $map_command->perform($context);
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
