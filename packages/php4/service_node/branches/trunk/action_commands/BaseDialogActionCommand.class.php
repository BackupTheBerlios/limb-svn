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

class BaseDialogActionCommand extends BaseActionCommand
{
  var $entity;
  var $form_id;
  var $validator;
  var $template_name;
  var $content_map;

  function BaseDialogActionCommand($template_name, $form_id, &$validator, $content_map = array())
  {
    parent :: BaseActionCommand();

    $this->template_name = $template_name;
    $this->form_id = $form_id;
    $this->validator =& $validator;
    $this->content_map = $content_map;
  }

  function performInitDialog()
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

  function performInitDataspace()
  {
    return LIMB_STATUS_OK;
  }

  function performMapDataspaceToEntity()
  {
    include_once(LIMB_DIR .'/core/commands/MapDataspaceToObjectCommand.class.php');
    $command = new MapDataspaceToObjectCommand($this->content_map, $this->entity);
    return $command->perform();
  }

  function & getEntity()
  {
    return $this->entity;
  }
}

?>