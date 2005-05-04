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
require_once(LIMB_DIR . '/core/commands/state_machines/StateMachineForCreateEntityDialog.class.php');

class CreateServiceNodeAtStructurePageCommand
{
  var $service_node;

  function perform()
  {
    $state_machine = new StateMachineForCreateEntityDialog($this);
    return $state_machine->perform();
  }

  function performInitial()
  {
    include_once(LIMB_DIR . '/core/commands/UseViewCommand.class.php');
    $view_command = new UseViewCommand('/service_node/create.html');
    return $view_command->perform();
  }

  function performRender()
  {
    include_once(LIMB_DIR . '/core/commands/DisplayViewCommand.class.php');
    $command = new DisplayViewCommand();
    return $command->perform();
  }

  function performInitDataspace()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver('service_node');
    $parent_entity =& $resolver->resolve($toolkit->getRequest());

    if(!is_object($parent_entity))
      return LIMB_STATUS_ERROR;

    $node =& $parent_entity->getPart('node');
    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('parent_node_id', $node->get('id'));

    return LIMB_STATUS_OK;
  }

  function performFormProcessing()
  {
    $form_id = 'service_node_form';
    $validator =  new LimbHandle(LIMB_SERVICE_NODE_DIR . '/validators/ServiceNodeRegisterValidator');

    include_once(LIMB_DIR . '/core/commands/FormProcessingCommand.class.php');
    $form_command = new FormProcessingCommand($form_id, false, $validator);
    return $form_command->perform();
  }

  function performInitEntity()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();
    $this->service_node = $toolkit->createObject($dataspace->get('class_name'));
    return LIMB_STATUS_OK;
  }

  function performMapDataspaceToEntity()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/MapDataspaceToServiceNodeCommand.class.php');
    $command = new MapDataspaceToServiceNodeCommand($this->service_node);
    return $command->perform();
  }

  function performRegisterEntity()
  {
    include_once(LIMB_DIR .'/core/commands/RegisterObjectCommand.class.php');
    $command = new RegisterObjectCommand($this->service_node);
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

  function & getServiceNode()
  {
    return $this->service_node;
  }
}

?>