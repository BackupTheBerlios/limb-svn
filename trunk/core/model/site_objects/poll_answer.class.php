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

class poll_answer extends content_object
{
	function poll_answer()
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
		return array(
			'class_ordr' => 2,
			'can_be_parent' => 0,
			'controller_class_name' => 'poll_answer_controller',
			'icon' => '/shared/images/folder.gif'
		);
	}
}

?>