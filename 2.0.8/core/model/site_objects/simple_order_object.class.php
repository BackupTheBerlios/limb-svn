<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: news_object.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class simple_order_object extends content_object
{
	function simple_order_object()
	{
		parent :: content_object();
	}

	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'simple_order_object_controller',
			'auto_identifier' => true,
		);
	}
}

?>