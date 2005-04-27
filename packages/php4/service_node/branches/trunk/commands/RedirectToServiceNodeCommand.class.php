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

class RedirectToServiceNodeCommand
{
  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();

    $resolver =& $toolkit->getRequestResolver('service_node');
    if(!is_object($resolver))
      return LIMB_STATUS_ERROR;

    if($entity = $resolver->resolve($toolkit->getRequest()))
    {
      $path = '?id='. $entity->get('oid');
      $redirect_command = new RedirectCommand($path);
    }
    else
      $redirect_command = new RedirectCommand('/service_nodes');

    return $redirect_command->perform();
  }
}


?>
