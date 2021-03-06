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
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class photogallery_object extends content_object
{
	function _define_attributes_definition()
	{
		return complex_array :: array_merge(
					parent :: _define_attributes_definition(),
					array(
						'identifier' => array('search' => false),
						'annotation' => array('search' => true, 'search_weight' => 5),
					)
				);
	}

	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'photogallery_object_controller',
			'auto_identifier' => true
		);
	}
}

?>