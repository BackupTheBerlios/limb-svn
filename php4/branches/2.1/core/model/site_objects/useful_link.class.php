<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: announce_object.class.php 59 2004-03-22 13:54:41Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class useful_link extends content_object
{
	function useful_link()
	{
		parent :: content_object();
	}

	function _define_attributes_definition()
	{
		return complex_array :: array_merge(
				parent :: _define_attributes_definition(),
				array(
				'identifier' => array('search' => false)
				));
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'useful_link_controller',
			'auto_identifier' => true
		);
	}
}

?>