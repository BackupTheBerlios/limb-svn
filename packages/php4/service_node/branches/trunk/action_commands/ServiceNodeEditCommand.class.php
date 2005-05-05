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
require_once(LIMB_DIR . '/core/commands/StateMachineCommand.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/commands/state_machines/StateMachineForEditEntityDialog.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseServiceNodeDialogCommand.class.php');

class ServiceNodeEditCommand extends BaseServiceNodeDialogCommand
{
  function perform()
  {
    $state_machine = new StateMachineForEditEntityDialog($this);
    return $state_machine->perform();
  }

  function performInitEntity()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver('tree_based_entity');

    if(!is_object($resolver))
      return LIMB_STATUS_ERROR;

    $this->service_node =& $resolver->resolve($toolkit->getRequest());
    return LIMB_STATUS_OK;
  }

  function performInitDataspace()
  {
    include_once(LIMB_SERVICE_NODE_DIR . '/commands/MapServiceNodeToDataspaceCommand.class.php');
    $command = new MapServiceNodeToDataspaceCommand($this->service_node);
    return $command->perform();
  }
}

?>