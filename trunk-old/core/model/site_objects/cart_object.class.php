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
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class cart_object extends site_object
{
	function cart_object()
	{
		parent :: site_object();
	}
	
	function _define_class_properties()
	{
		return  array(
			'class_ordr' => 0,
			'can_be_parent' => 0,
			'controller_class_name' => 'cart_object_controller',
			'icon' => '/shared/images/cart.gif'
		);
	}

}

?>