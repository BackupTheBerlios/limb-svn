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
require_once(LIMB_DIR . '/core/actions/form_action.class.php');
require_once(LIMB_DIR . '/core/model/shop/cart.class.php');

class cart_form_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'cart_form';
	}
  
	function _update_items_amount()
	{
		$cart =& cart :: instance();
		
		if($item_ids = $this->dataspace->get('amounts'))
		{
			foreach($item_ids as $item_id => $amount)
			{
			  $amount = (int)$amount;
				if(!$item =& $cart->get_item($item_id))
					continue;
				
				if($amount <= 0)
		      $cart->remove_item($item_id);		  
				else
				  $item->set_amount($amount);
			}
		}	
	}
	
	function _update_items_notes()
	{
		$cart =& cart :: instance();
		
		if($item_ids = $this->dataspace->get('notes'))
		{
			foreach($item_ids as $item_id => $note)
			{
				if(!$item =& $cart->get_item($item_id))
					continue;
				
				$item->set_attribute('note', strip_tags($note));
			}
		}	
	}
}

?>