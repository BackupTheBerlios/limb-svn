<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/Command.interface.php');

class LogoutCommand implements Command
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $response =& $toolkit->getResponse();

    $user->logout();

    $response->redirect('/');

    return LIMB :: STATUS_OK;
  }
}

?>