<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/commands/CreateSiteObjectCommand.class.php');

class CreateMessageCommand extends CreateSiteObjectCommand
{
	protected function _defineSiteObjectClassName()
	{
	  return 'message';
	}  
}

?>