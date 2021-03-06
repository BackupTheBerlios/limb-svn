<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: school_photogallery_object.class.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class paragraph extends content_object
{
	function paragraph()
	{
		parent :: content_object();
	}
	
	function _define_attributes_definition()
	{
		return complex_array :: array_merge(
					parent :: _define_attributes_definition(),
					array(
						'identifier' => array('search' => false),
						'content' => array('search' => true, 'search_weight' => 1),
					)
				);
	}

	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'paragraph_controller',
			'auto_identifier' => true
		);
	}
}

?>