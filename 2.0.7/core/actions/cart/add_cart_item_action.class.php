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
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/model/shop/cart.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');
require_once(LIMB_DIR . 'core/model/response/redirect_response.class.php');

class add_cart_item_action extends action
{
	function add_cart_item_action()
	{		
		parent :: action();
	}
	
	function perform()
	{
		if (!isset($_REQUEST['id']))
			return new close_popup_response(RESPONSE_STATUS_FAILURE);
		
		if (!$object_data =& fetch_one_by_node_id((int)$_REQUEST['id']))
			return new close_popup_response(RESPONSE_STATUS_FAILURE);
		
		$object =& site_object_factory :: create($object_data['class_name']);

		if(!method_exists($object, 'get_cart_item'))
			return new close_popup_response(RESPONSE_STATUS_FAILURE);
		
		$object->import_attributes($object_data);

		$cart_item =& $object->get_cart_item();
		
		$cart =& cart :: instance();
		
		$cart->add_item($cart_item);
		
		return new redirect_response(RESPONSE_STATUS_SUCCESS, '/root/cart?popup=1');
	}

}

?>