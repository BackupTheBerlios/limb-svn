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
require_once(LIMB_DIR . '/class/core/commands/create_site_object_command.class.php');

class create_message_command extends create_site_object_command
{
	protected function _define_site_object_class_name()
	{
	  return 'message';
	}  
}

?>