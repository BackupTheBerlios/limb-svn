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

class add_cart_item_action extends action
{	
	function perform(&$request, &$response)
	{
		$request->set_status(REQUEST_STATUS_FAILURE);

		if($request->has_attribute('popup'))
			$response->write(close_popup_response($request));
			
		if (!$id = $request->get_attribute('id'))
  		return;			
		
		if (!$object_data =& fetch_one_by_node_id((int)$id))
  		return;
		
		$object =& site_object_factory :: create($object_data['class_name']);

		if(!method_exists($object, 'get_cart_item'))
  		return;
		
		$object->import_attributes($object_data);

		$cart_item =& $object->get_cart_item();
		
		$cart =& cart :: instance();
		
		if ($quantity = (int)$request->get_attribute('quantity'))
			$cart_item->set_amount($quantity);
			
		$cart->add_item($cart_item);
		
		$request->set_status(REQUEST_STATUS_SUCCESS);
		$response->redirect('/root/cart?popup=1');
	}
}

?>