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

class ClosePopupCommand implements Command
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $response =& $toolkit->getResponse();

    // maybe we should use some kind of template here instead of close_popup_response($request)
    if($request->hasAttribute('popup'))
      $response->write(closePopupResponse($request));

    return Limb :: getSTATUS_OK();
  }

}

?>
