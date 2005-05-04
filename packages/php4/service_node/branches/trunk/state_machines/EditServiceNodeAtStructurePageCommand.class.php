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
require_once(LIMB_DIR . '/core/commands/state_machines/StateMachineForEditEntityDialog.class.php');

class EditServiceNodeAtStructurePageCommand
{
  var $service_node;

  function perform()
  {
    $state_machine = new StateMachineForEditEntityDialog($this);
    return $state_machine->perform();
  }

  function performInitial()
  {
    include_once(LIMB_DIR . '/core/commands/UseViewCommand.class.php');
    $view_command = new UseViewCommand('/service_node/edit.html');
    return $view_command->perform();
  }

  function performInitEntity()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver('service_node');

    if(!is_object($resolver))
      return LIMB_STATUS_ERROR;

    $this->service_node =& $resolver->resolve($toolkit->getRequest());
    return LIMB_STATUS_OK;
  }

  function performFormProcessing()
  {
    $form_id = 'service_node_form';
    $validator =  new LimbHandle(LIMB_SERVICE_NODE_DIR . '/validators/ServiceNodeEditValidator');

    include_once(LIMB_DIR . '/core/commands/FormProcessingCommand.class.php');
    $form_command = new FormProcessingCommand($form_id, false, $validator);
    return $form_command->perform();
  }

  function performInitDataspace()
  {
    include_once(LIMB_SERVICE_NODE_DIR . '/commands/MapServiceNodeToDataspaceCommand.class.php');
    $command = new MapServiceNodeToDataspaceCommand($this->service_node);
    return $command->perform();
  }

  function performMapDataspaceToEntity()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/MapDataspaceToServiceNodeCommand.class.php');
    $command = new MapDataspaceToServiceNodeCommand($this->service_node);
    return $command->perform();
  }

  function performRedirect()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/RedirectToServiceNodeAtSiteStructurePageCommand.class.php');
    $command = new RedirectToServiceNodeAtSiteStructurePageCommand($this->service_node);
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