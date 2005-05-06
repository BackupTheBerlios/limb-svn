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
require_once(LIMB_DIR . '/core/commands/StateMachineCommand.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseDialogActionCommand.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/commands/state_machines/StateMachineForEditEntityDialog.class.php');

class BaseEditDialogActionCommand extends BaseDialogActionCommand
{
  var $resolver_name;

  function BaseEditDialogActionCommand($template_name, $form_id, &$validator, $content_map, $resolver_name)
  {
    parent :: BaseDialogActionCommand($template_name, $form_id, $validator, $content_map);

    $this->resolver_name = $resolver_name;
  }

  function perform()
  {
    $state_machine = new StateMachineForEditEntityDialog($this);
    return $state_machine->perform();
  }

  function performInitEntity()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver($this->resolver_name);

    if(!is_object($resolver))
      return LIMB_STATUS_ERROR;

    $this->entity =& $resolver->resolve($toolkit->getRequest());
    return LIMB_STATUS_OK;
  }


}

?>