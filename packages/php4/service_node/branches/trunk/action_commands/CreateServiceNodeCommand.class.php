<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudMainBehaviour.class.php 23 2005-02-26 18:11:24Z server $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseCreateDialogActionCommand.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');

class CreateServiceNodeCommand extends BaseCreateDialogActionCommand
{
  function performInitDataspace()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver('tree_based_entity');
    $parent_entity =& $resolver->resolve($toolkit->getRequest());

    if(!is_object($parent_entity))
      return LIMB_STATUS_ERROR;

    $node =& $parent_entity->getNodePart();
    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('parent_node_id', $node->get('id'));

    return LIMB_STATUS_OK;
  }

  function performDefaultMapDataspaceToEntity()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/MapDataspaceToServiceNodeCommand.class.php');
    $command = new MapDataspaceToServiceNodeCommand($this->entity);
    return $command->perform();
  }
}

?>