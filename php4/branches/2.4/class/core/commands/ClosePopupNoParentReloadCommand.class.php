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

class ClosePopupNoParentReloadCommand// implements Command
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $response =& $toolkit->getResponse();

    // maybe we should use some kind of template here instead of close_popup_no_parent_reload_response()
    if($request->hasAttribute('popup'))
      $response->write(closePopupNoParentReloadResponse();

    return LIMB_STATUS_OK;
  }

}

?>
