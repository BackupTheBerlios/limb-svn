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
require_once(LIMB_DIR . 'core/model/object.class.php');

class cart_item extends object
{
	function cart_item($id)
	{
		parent :: object();
		
		$this->_set_id($id);
		
		//IMPORTANT!!! 
		$this->__session_class_path = LIMB_DIR . '/core/model/shop/cart_item.class.php';
	}
	
	function _set_id($id)
	{
		$this->set_attribute('id', $id);
	}
	
	function get_id()
	{
		return (int)$this->get_attribute('id');
	}
	
	function get_price()
	{
		return 1*$this->get_attribute('price', 0);
	}
	
	function set_price($price)
	{
		$this->set_attribute('price', $price);
	}
	
	function get_amount()
	{
		return 1*$this->get_attribute('amount', 0);
	}

	function set_amount($amount)
	{
		$this->set_attribute('amount', $amount);
	}

	function get_description()
	{
		return $this->get_attribute('description');
	}
	
	function set_description($description)
	{
		$this->set_attribute('description', $description);
	}
	
	function get_summ()
	{
		return $this->get_amount() * $this->get_price();
	}
	
	function summ_amount($item)
	{
		$this->set_amount($this->get_amount() + $item->get_amount());
	}

}

?>