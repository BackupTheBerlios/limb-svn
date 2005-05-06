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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/EditServiceNodeCommand.class.php');

class EditContentServiceNodeCommand extends EditServiceNodeCommand
{
  var $content_map;

  function EditContentServiceNodeCommand($template_path,
                                         $form_id,
                                         &$validator,
                                         $content_map)
  {

    parent :: EditServiceNodeCommand($template_path, $form_id, $validator);

    $this->content_map = $content_map;
  }

  function performInitDataspace()
  {
    include_once(LIMB_SERVICE_NODE_DIR . '/commands/MapContentServiceNodeToDataspaceCommand.class.php');
    $command = new MapContentServiceNodeToDataspaceCommand($this->entity, array_flip($this->content_map));
    return $command->perform();
  }

  function performMapDataspaceToEntity()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/MapDataspaceToContentServiceNodeCommand.class.php');
    $command = new MapDataspaceToContentServiceNodeCommand($this->entity, $this->content_map);
    return $command->perform();
  }
}

?>