<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: set_group_objects_access.class.php 38 2004-03-13 14:25:46Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/model/shop/cart.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');
require_once(LIMB_DIR . 'core/model/response/redirect_response.class.php');

class add_cart_items_action extends form_action
{
	var $_catalog_object_class_name = 'catalog_object';
	
	function add_cart_items_action($name = 'order_form')
	{		
		parent :: form_action($name);
	}
	
	function _valid_perform()
	{
		if(!$objects_amounts = $this->dataspace->get('amount'))
			return new close_popup_response(RESPONSE_STATUS_FAILURE);
			
		$objects_data =& fetch_by_node_ids(
													array_keys($objects_amounts), 
													$this->_catalog_object_class_name,
													$counter);
	
		if(!$objects_data)
			return new close_popup_response(RESPONSE_STATUS_FAILURE);
			
		$object =& site_object_factory :: create($this->_catalog_object_class_name);

		if(!method_exists($object, 'get_cart_item'))
			return new close_popup_response(RESPONSE_STATUS_FAILURE);

		$cart =& cart :: instance();
		
		foreach($objects_data as $key => $object_data)
		{
			$object->import_attributes($object_data);
			$cart_item =& $object->get_cart_item();
			$cart_item->set_amount($objects_amounts[$key]);
			$cart->add_item($cart_item);
		}
		
		return new redirect_response(RESPONSE_STATUS_SUCCESS, '/root/cart?popup=1');
	}
	
}

?>