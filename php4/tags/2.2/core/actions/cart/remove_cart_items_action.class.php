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

class remove_cart_items_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'cart_form';
	}
	
	function _valid_perform(&$request, &$response)
	{
		$cart =& cart :: instance();
		
		if($item_ids = $this->dataspace->get('ids'))
			$cart->remove_items($item_ids);

		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
		$response->redirect('/root/cart');
	}

}

?>