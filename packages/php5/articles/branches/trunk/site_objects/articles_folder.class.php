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
require_once(LIMB_DIR . 'class/core/site_objects/site_object.class.php');

class articles_folder extends site_object
{
	protected function _define_class_properties()
	{
		return  array(
			'class_ordr' => 0,
			'can_be_parent' => 1,
			'controller_class_name' => 'articles_folder_controller',
			'icon' => '/shared/images/folder.gif'
		);
	}

}

?>