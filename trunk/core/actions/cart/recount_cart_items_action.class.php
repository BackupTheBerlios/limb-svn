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

class recount_cart_items_action extends cart_form_action
{	
	function _valid_perform(&$request, &$response)
	{
		$this->_update_items_amount();
		
		$this->_update_items_notes();
		
		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
		$response->redirect('/root/cart');
	}
}

?>