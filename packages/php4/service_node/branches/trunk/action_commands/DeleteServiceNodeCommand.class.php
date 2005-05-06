<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudMainService.class.php 23 2005-02-26 18:11:24Z server $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/BaseDeleteActionCommand.class.php');

class DeleteServiceNodeCommand extends BaseDeleteActionCommand
{
  function DeleteServiceNodeCommand($resolver_name = 'tree_based_entity')
  {
    parent :: BaseDeleteActionCommand($resolver_name);
  }

  function performDeleteEntity()
  {
    include_once(LIMB_SERVICE_NODE_DIR . '/commands/ServiceNodeDeleteCommand.class.php');
    $command = new ServiceNodeDeleteCommand($this->entity);
    return $command->perform();
  }
}

?>