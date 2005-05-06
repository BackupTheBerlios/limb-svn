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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseDialogActionCommand.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/commands/state_machines/StateMachineForCreateEntityDialog.class.php');

class BaseCreateDialogActionCommand extends BaseDialogActionCommand
{
  var $entity_handle;
  var $extra_dataspace_values;

  function BaseCreateDialogActionCommand($template_name,
                                         $form_id,
                                         &$validator,
                                         $content_map = array(),
                                         $extra_dataspace_values = array(),
                                         &$entity_handle)
  {
    parent :: BaseDialogActionCommand($template_name, $form_id, $validator, $content_map);

    $this->entity_handle =& $entity_handle;
    $this->extra_dataspace_values = $extra_dataspace_values;
  }

  function perform()
  {
    $state_machine = new StateMachineForCreateEntityDialog($this);
    return $state_machine->perform();
  }

  function performInitEntity()
  {
    $this->entity =& Handle :: resolve($this->entity_handle);
    return LIMB_STATUS_OK;
  }

  function performMapDataspaceToEntity()
  {
    $state_machine = new StateMachineCommand($this);

    $state_machine->registerState('PutSomeDataToDataspace', array(LIMB_STATUS_OK => 'DefaultMapDataspaceToEntity',
                                                                  LIMB_STATUS_ERROR => 'Error',
                                                                  ));
    $state_machine->registerState('DefaultMapDataspaceToEntity');

    return $state_machine->perform();
  }

  function performPutSomeDataToDataspace()
  {
    if(!$this->extra_dataspace_values)
      return LIMB_STATUS_OK;

    include_once(LIMB_DIR . '/core/commands/PutValuesToDataspaceCommand.class.php');
    $extra_command = new PutValuesToDataspaceCommand($this->extra_dataspace_values);
    return $extra_command->perform();
  }

  function performDefaultMapDataspaceToEntity()
  {
    include_once(LIMB_DIR .'/core/commands/MapDataspaceToObjectCommand.class.php');
    $command = new MapDataspaceToObjectCommand(array_flip($this->content_map), $this->entity);
    return $command->perform();
  }

  function performRegisterEntity()
  {
    include_once(LIMB_DIR .'/core/commands/RegisterObjectCommand.class.php');
    $command = new RegisterObjectCommand($this->entity);
    return $command->perform();
  }
}

?>