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
require_once(LIMB_DIR . 'core/model/site_objects/period_object.class.php');

class period_news_object extends period_object
{
	function period_news_object()
	{
		parent :: period_object();
	}
	
	function _define_attributes_definition()
	{
		return array(
			'content' => array('search' => true, 'search_weight' => 1),
			'annotation' => array('search' => true, 'search_weight' => 5)
		);
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 2,
			'can_be_parent' => 0,
			'controller_class_name' => 'period_news_object_controller',
		);
	}
}

?>