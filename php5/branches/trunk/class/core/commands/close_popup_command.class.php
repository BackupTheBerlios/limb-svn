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
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');

class close_popup_command implements Command
{
  public function perform()
  {
    $toolkit = Limb :: toolkit();
    $request = $toolkit->getRequest();

    // maybe we should use some kind of template here instead of close_popup_response($request)
    if($request->has_attribute('popup'))
      $toolkit->getResponse()->write(close_popup_response($request));

    return Limb :: STATUS_OK;
  }

}

?>
