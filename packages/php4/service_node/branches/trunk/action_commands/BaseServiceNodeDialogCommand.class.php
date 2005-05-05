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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseActionCommand.class.php');

class BaseServiceNodeDialogCommand extends BaseActionCommand
{
  var $form_id;
  var $validator;
  var $template_name;

  function BaseServiceNodeDialogCommand($template_name, $form_id, $validator)
  {
    parent :: BaseActionCommand();

    $this->template_name = $template_name;
    $this->form_id = $form_id;
    $this->validator = $validator;
  }

  function performInitial()
  {
    include_once(LIMB_DIR . '/core/commands/UseViewCommand.class.php');
    $view_command = new UseViewCommand($this->template_name);
    return $view_command->perform();
  }

  function performFormProcessing()
  {
    include_once(LIMB_DIR . '/core/commands/FormProcessingCommand.class.php');
    $form_command = new FormProcessingCommand($this->form_id, false, $this->validator);
    return $form_command->perform();
  }

  function performMapDataspaceToEntity()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/MapDataspaceToServiceNodeCommand.class.php');
    $command = new MapDataspaceToServiceNodeCommand($this->service_node);
    return $command->perform();
  }
}

?>