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
require_once(LIMB_DIR . 'core/actions/cart/cart_form_action.class.php');

class recount_cart_items_action extends cart_form_action
{
	function recount_cart_items_action($name = 'cart_form')
	{		
		parent :: cart_form_action($name);
	}
	
	function _valid_perform()
	{
		$this->_update_items_amount();
		
		$this->_update_items_notes();
		
		return new redirect_response(RESPONSE_STATUS_FORM_SUBMITTED, '/root/cart');
	}
}

?>