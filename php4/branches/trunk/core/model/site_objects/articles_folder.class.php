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

class articles_folder extends site_object
{
	function _define_class_properties()
	{
		return  array(
			'class_ordr' => 0,
			'can_be_parent' => 1,
			'icon' => '/shared/images/folder.gif'
		);
	}

}

?>