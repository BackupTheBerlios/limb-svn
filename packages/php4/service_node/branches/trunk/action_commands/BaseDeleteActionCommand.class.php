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
require_once(LIMB_SERVICE_NODE_DIR . '/commands/state_machines/StateMachineForDeleteEntity.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseActionCommand.class.php');

class BaseDeleteActionCommand extends BaseActionCommand
{
  var $entity;
  var $resolver_name;

  function BaseDeleteActionCommand($resolver_name)
  {
    parent :: BaseActionCommand();

    $this->resolver_name = $resolver_name;
  }

  function perform()
  {
    $state_machine = new StateMachineForDeleteEntity($this);
    return $state_machine->perform();
  }

  function performInitEntity()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver($this->resolver_name);

    if(!is_object($resolver))
      die($this->resolver_name . ' resolver is not set!');

    $this->entity =& $resolver->resolve($toolkit->getRequest());
    return LIMB_STATUS_OK;
  }

  function performDeleteEntity()
  {
    include_once(LIMB_DIR . '/core/commands/DeleteEntityCommand.class.php');
    $command = new DeleteEntityCommand($this->entity);
    return $command->perform();
  }

  function & getEntity()
  {
    return $this->entity;
  }

}

?>