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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseServiceNodeDialogCommand.class.php');

class ServiceNodeCreateCommand extends BaseServiceNodeDialogCommand
{
  var $extra_dataspace_values;

  function ServiceNodeCreateCommand($template_name, $form_id, &$validator, $extra_dataspace_values = array())
  {
    parent :: BaseServiceNodeDialogCommand($template_name, $form_id, $validator);

    $this->extra_dataspace_values = $extra_dataspace_values;
  }

  function perform()
  {
    $state_machine = new StateMachineForCreateEntityDialog($this);
    return $state_machine->perform();
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

  function performInitEntity()
  {
    $this->service_node = new ServiceNode();
    return LIMB_STATUS_OK;
  }

  function performMapDataspaceToEntity()
  {
    if($this->extra_dataspace_values)
    {
      include_once(LIMB_DIR . '/core/commands/PutValuesToDataspaceCommand.class.php');
      $extra_command = new PutValuesToDataspaceCommand($this->extra_dataspace_values);
      $extra_command->perform();
    }

    return parent :: performMapDataspaceToEntity();
  }

  function performRegisterEntity()
  {
    include_once(LIMB_DIR .'/core/commands/RegisterObjectCommand.class.php');
    $command = new RegisterObjectCommand($this->service_node);
    return $command->perform();
  }
}

?>