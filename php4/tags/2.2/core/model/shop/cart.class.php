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
require_once(LIMB_DIR . 'core/model/shop/cart_item.class.php');
require_once(LIMB_DIR . 'core/lib/util/array_dataset.class.php');
require_once(LIMB_DIR . 'core/lib/system/objects_support.inc.php');

define('CART_DEFAULT_ID', 1);

class cart
{		
	var $_cart_id = CART_DEFAULT_ID;
	var $_items = array();
	
	function __get_class_path()
	{
		return LIMB_DIR . '/core/model/shop/cart.class.php';
	}
	
	function cart($cart_id = CART_DEFAULT_ID)
	{
		$this->_cart_id = $cart_id;
		$this->_items = array();
	}
	
	function get_cart_id()	
	{
		return $this->_cart_id;
	}
	
 	function & instance($cart_id = CART_DEFAULT_ID)
  {  
		$obj =& instantiate_session_object('cart', array($cart_id));
		return $obj;
  }

	function add_item(&$new_item)
	{
		$id = $new_item->get_id();

		if ($new_item->get_amount() <= 0)
			$new_item->set_amount(1);
		
		if(($item =& $this->get_item($id)) !== false)
			$new_item->summ_amount($item);

		$this->_items[$new_item->get_id()] = &$new_item;
	}
	
	function & get_item($id)
	{
		if(isset($this->_items[$id]))
			return $this->_items[$id];
		else
			return false;
	}
		
	function get_total_summ()
	{
		$summ = 0;
			
		foreach($this->_items as $item)
			$summ += $item->get_summ();
		
		return $summ;
	}

	function remove_item($item_id)
	{
		if (isset($this->_items[$item_id]))
			unset($this->_items[$item_id]);
	}
	
	function remove_items($item_ids)
	{
		foreach($item_ids as $id)
			$this->remove_item($id);
	}
	
	function get_items()
	{
		return $this->_items;		
	}
	
	function get_items_array_dataset()
	{
		$result_array = array();
		foreach($this->_items as $id => $item)
		{
			$result_array[$id] = $item->export_attributes();
			$result_array[$id]['summ'] = $item->get_summ();
		}
		
		return new array_dataset($result_array);
	}

	function count_items()
	{
		if (is_array($this->_items))
			return count($this->_items);	
		else
			return 0;	
	}

	function clear()
	{
		$this->_items = array();		
	}	
}
?>