<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: RedirectCommand.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/RedirectCommand.class.php');

class RedirectToMappedServiceNodeCommand
{
  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit('service_node_toolkit');
    $locator =& $toolkit->getServiceNodeLocator();

    if(!$entity =& $locator->getCurrentServiceNode())
      return LIMB_STATUS_ERROR;

    $path = '?id='. $entity->get('oid');
    $redirect_command = new RedirectCommand($path);

    return $redirect_command->perform();
  }
}


?>
