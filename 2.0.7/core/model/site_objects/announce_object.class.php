<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: announce_object.class.php 239 2004-02-29 19:00:20Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/period_object.class.php');

class announce_object extends period_object
{
	function announce_object()
	{
		parent :: period_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'announce_object_controller',
		);
	}
}

?>