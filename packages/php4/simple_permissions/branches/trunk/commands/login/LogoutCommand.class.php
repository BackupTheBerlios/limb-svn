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

class LogoutCommand// implements Command
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $response =& $toolkit->getResponse();

    $user->logout();

    $response->redirect('/');

    return LIMB_STATUS_OK;
  }
}

?>