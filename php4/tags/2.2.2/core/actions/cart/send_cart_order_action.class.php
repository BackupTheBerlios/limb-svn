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
require_once(LIMB_DIR . 'core/actions/cart/cart_form_action.class.php');

class send_cart_order_action extends cart_form_action
{
	function _define_dataspace_name()
	{
	  return 'cart_form';
	}

	function _valid_perform(&$request, &$response)
	{
		$this->_update_items_amount();
		
		$this->_update_items_notes();
		
		$cart =& cart :: instance();
		
		if($cart->count_items() == 0)
		{
			message_box :: write_error(strings :: get('no_items_in_cart', 'cart'));

			$request->set_status(REQUEST_STATUS_FAILURE);
			
  		if($request->has_attribute('popup'))
  			$response->write(close_popup_response($request));
  			
  		return;			
		}

		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
		$response->redirect('/root/cart?action=checkout&popup=1');
	}
}

?>