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
require_once(LIMB_DIR . 'class/core/site_objects/content_object.class.php');

class guestbook_message extends content_object
{
	protected function _define_class_properties()
	{
		return array(
			'class_ordr' => 0,
			'can_be_parent' => 0,
			'controller_class_name' => 'guestbook_message_controller',
		);
	}
}

?>