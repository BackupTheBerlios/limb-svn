<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_catalog_object_action.class.php 786 2004-10-12 14:24:43Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/create_site_object_command.class.php');

class create_catalog_object_commands extends create_site_object_command
{
	protected function _define_site_object_class_name()
	{
	  return 'catalog_object';
	}  
}

?>