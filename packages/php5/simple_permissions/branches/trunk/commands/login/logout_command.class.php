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

class logout_command implements Command
{
	public function perform()
	{
    $toolkit = Limb :: toolkit();
		
    $toolkit->getUser()->logout();
    
		$toolkit->getResponse()->redirect('/');
    
    return LIMB :: STATUS_OK;
	}
}

?>