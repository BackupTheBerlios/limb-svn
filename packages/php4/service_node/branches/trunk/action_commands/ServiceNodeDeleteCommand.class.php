<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudMainService.class.php 23 2005-02-26 18:11:24Z server $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/commands/state_machines/StateMachineForDeleteEntity.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseActionCommand.class.php');

class ServiceNodeDeleteCommand extends BaseActionCommand
{
  function perform()
  {
    $state_machine = new StateMachineForDeleteEntity($this);
    return $state_machine->perform();
  }

  function performInitEntity()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver('tree_based_entity');
    $this->service_node =& $resolver->resolve($toolkit->getRequest());
    return LIMB_STATUS_OK;
  }

  function performDeleteEntity()
  {
    include_once(LIMB_SERVICE_NODE_DIR . '/commands/DeleteServiceNodeCommand.class.php');
    $command = new DeleteServiceNodeCommand($this->service_node);
    return $command->perform();
  }
}

?>