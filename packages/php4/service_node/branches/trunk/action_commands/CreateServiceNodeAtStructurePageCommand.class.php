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
require_once(LIMB_SERVICE_NODE_DIR . '/commands/state_machines/StateMachineForCreateEntityDialog.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/ServiceNodeCreateCommand.class.php');

class CreateServiceNodeAtStructurePageCommand extends ServiceNodeCreateCommand
{
  function CreateServiceNodeAtStructurePageCommand()
  {
    $template_name = '/service_node/create.html';
    $form_id = 'service_node_form';
    $validator =  new LimbHandle(LIMB_SERVICE_NODE_DIR . '/validators/ServiceNodeRegisterValidator');

    parent :: ServiceNodeCreateCommand($template_name, $form_id, $validator);
  }

  function perform()
  {
    $state_machine = new StateMachineForCreateEntityDialog($this);
    return $state_machine->perform();
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

  function performInitEntity()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();
    $this->service_node = $toolkit->createObject($dataspace->get('class_name'));
    return LIMB_STATUS_OK;
  }

  function performRedirect()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/RedirectToServiceNodeAtSiteStructurePageCommand.class.php');
    $command = new RedirectToServiceNodeAtSiteStructurePageCommand($this->service_node);
    return $command->perform();
  }
}

?>