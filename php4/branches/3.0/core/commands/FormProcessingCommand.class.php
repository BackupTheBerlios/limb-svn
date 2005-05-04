<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/FormInitCommand.class.php');
require_once(LIMB_DIR . '/core/commands/state_machines/StateMachineForFormProcessing.class.php');

class FormProcessingCommand
{
  var $form_id;
  var $is_multi;
  var $validator;

  function FormProcessingCommand($form_id, $is_multi, &$validator)
  {
    $this->form_id = $form_id;
    $this->is_multi = $is_multi;
    $this->validator =& $validator;
  }

  function perform()
  {
    $state_machine = new StateMachineForFormProcessing($this);
    return $state_machine->perform();
  }

  function performInit()
  {
    include_once(LIMB_DIR . '/core/commands/FormInitCommand.class.php');
    $command = new FormInitCommand($this->form_id, $this->is_multi);
    return $command->perform();
  }

  function performValidate()
  {
    include_once(LIMB_DIR . '/core/commands/FormValidateCommand.class.php');
    $command = new FormValidateCommand($this->form_id, $this->validator);
    return $command->perform();
  }
}
?>
