<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: announce_folder.class.php 239 2004-02-29 19:00:20Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class ad_block_object extends content_object
{
	function ad_block_object()
	{
		parent :: content_object();
	}

	function _define_attributes_definition()
	{
		return array(
			'identifier' => array('search' => true, 'auto_identifier' => true)
		);
	}
	
	function _define_class_properties()
	{
		return  array(
			'class_ordr' => 0,
			'can_be_parent' => 0,
			'controller_class_name' => 'ad_block_object_controller',
		);
	}
}

?>