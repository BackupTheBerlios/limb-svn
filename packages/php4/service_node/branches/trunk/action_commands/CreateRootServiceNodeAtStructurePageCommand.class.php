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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/CreateServiceNodeAtStructurePageCommand.class.php');

class CreateRootServiceNodeAtStructurePageCommand extends CreateServiceNodeAtStructurePageCommand
{
  function performInitDataspace()
  {
    return LIMB_STATUS_OK;
  }
}

?>