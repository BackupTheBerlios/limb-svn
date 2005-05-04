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

class RedirectToServiceNodeAtSiteStructurePageCommand
{
  var $service_node;

  function RedirectToServiceNodeAtSiteStructurePageCommand(&$service_node)
  {
    $this->service_node =& $service_node;
  }

  function perform()
  {
    $path = '?id='. $this->service_node->get('oid');
    $redirect_command = new RedirectCommand($path);
    return $redirect_command->perform();
  }
}


?>
