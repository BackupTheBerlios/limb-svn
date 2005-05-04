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

class ServiceNodeCreateCommand
{
  var $service_node;
  var $form_id;
  var $template_name;
  var $extra_dataspace_values;

  function ServiceNodeCreateCommand($template_name, $form_id, $extra_dataspace_values)
  {
    $this->template_name = $template_name;
    $this->form_id = $form_id;
    $this->extra_dataspace_values = $extra_dataspace_values;
  }

  function perform()
  {
    $state_machine = new StateMachineForCreateEntityDialog($this);
    return $state_machine->perform();
  }

  function performInitial()
  {
    include_once(LIMB_DIR . '/core/commands/UseViewCommand.class.php');
    $view_command = new UseViewCommand($this->template_name);
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
    $resolver =& $toolkit->getRequestResolver('tree_based_entity');
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
    $validator =  new LimbHandle(LIMB_SERVICE_NODE_DIR . '/validators/CommonCreateServiceNodeValidator');

    include_once(LIMB_DIR . '/core/commands/FormProcessingCommand.class.php');
    $form_command = new FormProcessingCommand($this->form_id, false, $validator);
    return $form_command->perform();
  }

  function performInitEntity()
  {
    $this->service_node = new ServiceNode();
    return LIMB_STATUS_OK;
  }

  function performMapDataspaceToEntity()
  {
    include_once(LIMB_DIR . '/core/commands/PutValuesToDataspaceCommand.class.php');
    $extra_command = new PutValuesToDataspaceCommand($this->extra_dataspace_values);
    $extra_command->perform();

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

  function & getServiceNode()
  {
    return $this->service_node;
  }
}

?>