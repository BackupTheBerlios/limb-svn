<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: site_structure.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class site_param_object extends site_object
{
	function site_param_object()
	{
		parent :: site_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 1,
			'controller_class_name' => 'site_param_object_controller',
		);
	}
}

?>