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
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/model/shop/cart.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');
require_once(LIMB_DIR . 'core/model/response/redirect_response.class.php');

class remove_cart_items_action extends form_action
{
	function remove_cart_items_action($name = 'cart_form')
	{		
		parent :: form_action($name);
	}
	
	function _valid_perform()
	{
		$cart =& cart :: instance();
		
		if($item_ids = $this->dataspace->get('ids'))
			$cart->remove_items($item_ids);
		
		return new redirect_response(RESPONSE_STATUS_FORM_SUBMITTED, '/root/cart');
	}

}

?>