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

class navigation_item extends content_object
{
	function navigation_item()
	{
		parent :: content_object();
		
    $this->_attributes_definition['title'] = array('search' => true, 'search_weight' => 1);
    $this->_attributes_definition['identifier'] = array('search' => true, 'search_weight' => 1);
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 1,
			'controller_class_name' => 'navigation_item_controller',
		);
	}
}

?>