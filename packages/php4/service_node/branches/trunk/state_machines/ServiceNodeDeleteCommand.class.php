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
require_once(LIMB_DIR . '/core/commands/state_machines/StateMachineForDeleteEntity.class.php');

class ServiceNodeDeleteCommand
{
  var $service_node;

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

  function performRedirect()
  {
    include_once(LIMB_DIR .'/core/commands/RedirectCommand.class.php');
    $command = new RedirectCommand();
    return $command->perform();
  }

  function performError()
  {
    include_once(LIMB_DIR .'/core/commands/UseViewCommand.class.php');
    $command = new UseViewCommand('/error.html');
    return $command->perform();
  }

  function performRender()
  {
    include_once(LIMB_DIR . '/core/commands/DisplayViewCommand.class.php');
    $command = new DisplayViewCommand();
    return $command->perform();
  }

  function & getServiceNode()
  {
    return $this->service_node;
  }
}

?>