<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: files_folder.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class stats_event extends site_object
{
	function stats_event()
	{
		parent :: site_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 0,
			'can_be_parent' => 0,
			'controller_class_name' => 'stats_event_controller',
		);
	}
}

?>