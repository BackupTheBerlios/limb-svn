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
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class links_manager_page extends site_object
{
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 10,
			'can_be_parent' => 0,
			'controller_class_name' => 'links_manager_page_controller',
		);
	}
}

?>