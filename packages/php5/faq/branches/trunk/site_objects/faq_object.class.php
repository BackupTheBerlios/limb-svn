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
require_once(LIMB_DIR . '/class/core/site_objects/content_object.class.php');

class faq_object extends content_object
{
	protected function _define_attributes_definition()
	{
		return complex_array :: array_merge(
				parent :: _define_attributes_definition(),
				array(
				'question' => array('search' => true),
				'answer' => array('search' => true)
				));
	}
	
	protected function _define_class_properties()
	{
		return array(
			'class_ordr' => 2,
			'can_be_parent' => 0,
			'controller_class_name' => 'faq_object_controller',
			'auto_identifier' => true
		);
	}
}

?>