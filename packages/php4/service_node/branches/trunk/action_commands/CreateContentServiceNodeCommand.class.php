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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/CreateServiceNodeCommand.class.php');

class CreateContentServiceNodeCommand extends CreateServiceNodeCommand
{
  function performDefaultMapDataspaceToEntity()
  {
    include_once(LIMB_SERVICE_NODE_DIR .'/commands/MapDataspaceToContentServiceNodeCommand.class.php');
    $command = new MapDataspaceToContentServiceNodeCommand($this->entity, $this->content_map);
    return $command->perform();
  }
}

?>