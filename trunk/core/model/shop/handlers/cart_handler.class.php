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

class cart_handler
{		
	var $_cart_id = null;
	var $_items = array();
		
	function cart_handler($cart_id)
	{
		$this->_cart_id = $cart_id;
	}
	
	function reset()
	{
	  $this->_items = array();
	}
		
	function get_cart_id()	
	{
		return $this->_cart_id;
	}
	
	function set_cart_id($cart_id)
	{
	  $this->_cart_id = $cart_id;
	}
	
	function add_item(&$new_item)
	{
		$id = $new_item->get_id();

		if ($new_item->get_amount() <= 0)
			$new_item->set_amount(1);
		
		if (isset($this->_items[$id]))
		  $new_item->summ_amount($this->_items[$id]);

		$this->_items[$id] =& $new_item;
	}
	
	function & get_item($id)
	{
		if(isset($this->_items[$id]))
			return $this->_items[$id];
		else
			return false;
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
	
	function set_items(&$items)
	{
	  $this->_items =& $items;
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