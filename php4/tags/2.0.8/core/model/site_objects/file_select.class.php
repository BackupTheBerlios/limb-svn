<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: file_select.class.php 419 2004-02-09 15:12:03Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class file_select extends site_object
{
	function file_select()
	{
		parent :: site_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 0,
			'can_be_parent' => 0,
			'controller_class_name' => 'file_select_controller',
		);
	}
}

?>