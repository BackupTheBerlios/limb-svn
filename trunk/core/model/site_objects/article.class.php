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

class article extends content_object
{
	function article()
	{
		parent :: content_object();
	}

	function _define_attributes_definition()
	{
		return array(
			'content' => array('search' => true, 'search_weight' => 1),
			'annotation' => array('search' => true, 'search_weight' => 5),
			'author' => array('search' => true, 'search_weight' => 10),
			'source' => array('search' => true, 'search_weight' => 10),
		);
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'article_controller',
		);
	}
}

?>