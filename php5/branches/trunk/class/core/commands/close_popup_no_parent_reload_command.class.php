<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');

class close_popup_no_parent_reload_command implements Command
{
	public function perform()
	{
    $toolkit = Limb :: toolkit();
    $request = $toolkit->getRequest();
    
    // maybe we should use some kind of template here instead of close_popup_no_parent_reload_response()
		if($request->has_attribute('popup'))
			$toolkit->getResponse()->write(close_popup_no_parent_reload_response();
    
    return Limb :: STATUS_OK;
  }
  
}

?> 
