<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: news_object.class.php 422 2004-02-10 09:50:25Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class news_object extends content_object
{
	function news_object()
	{
		parent :: content_object();
	}
	
	function _define_attributes_definition()
	{
		return array(
			'content' => array('search' => true),
			'annotation' => array('search' => true)
		);
	}
	
	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'news_object_controller',
		);
	}
}

?>