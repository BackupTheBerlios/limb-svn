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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseEditDialogActionCommand.class.php');

class EditServiceNodeCommand extends BaseEditDialogActionCommand
{
  function EditServiceNodeCommand($template_name,
                                  $form_id,
                                  &$validator,
                                  $content_map = array(),
                                  $resolver_name = 'tree_based_entity')
  {
    parent :: BaseEditDialogActionCommand($template_name, $form_id, &$validator, $content_map, $resolver_name);
  }

  function performInitDataspace()
  {
    include_once(LIMB_SERVICE_NODE_DIR . '/commands/MapServiceNodeToDataspaceCommand.class.php');
    $command = new MapServiceNodeToDataspaceCommand($this->entity);
    return $command->perform();
  }

  function performMapDataspaceToEntity()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/MapDataspaceToServiceNodeCommand.class.php');
    $command = new MapDataspaceToServiceNodeCommand($this->entity);
    return $command->perform();
  }
}

?>