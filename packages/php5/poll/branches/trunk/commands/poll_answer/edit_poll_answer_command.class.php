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
require_once(LIMB_DIR . '/class/core/commands/edit_site_object_command.class.php');

class edit_poll_answer_command extends edit_site_object_command
{
	protected function _define_site_object_class_name()
	{
	  return 'poll_answer';
	}  
}

?>