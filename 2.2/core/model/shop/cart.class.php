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

define('CART_DEFAULT_ID', session_id());

class cart
{		
	var $_cart_id = null;
	var $_cart_handler = null;
		
	function cart($cart_id = null, $handler = null)
	{
	  if($cart_id === null)
		  $this->_cart_id = CART_DEFAULT_ID;
		else
		  $this->_cart_id = $cart_id;
		
		$this->initialize_cart_handler($handler);
		
		$this->_items = array();
	}

 	function & instance($cart_id = null, $handler = null)
  {  
		$obj =& instantiate_object('cart', array($cart_id, $handler));
		return $obj;
  }
  	
	function initialize_cart_handler(&$handler)
	{
	  if($handler === null)
	  {
	    include_once(LIMB_DIR . '/core/model/shop/handlers/session_cart_handler.class.php');
	    $this->_cart_handler =& new session_cart_handler($this->_cart_id);
	  }
	  else
	    $this->_cart_handler =& $handler;
	}

  function set_cart_handler(&$handler)
  {
    $this->_cart_handler =& $handler;
  }
	
	function get_cart_id()	
	{
		return $this->_cart_id;
	}
	
	function & get_cart_handler()
	{
	  return $this->_cart_handler;
	}
	
	function add_item(&$new_item)
	{
	  $this->_cart_handler->add_item($new_item); 
	}
	
	function & get_item($item_id)
	{
	  return $this->_cart_handler->get_item($item_id); 	  
	}
		
	function get_total_summ()
	{
	  $items =& $this->_cart_handler->get_items();
		$summ = 0;
			
		foreach(array_keys($items) as $key)
			$summ += $items[$key]->get_summ();
		
		return $summ;
	}

	function remove_item($item_id)
	{
	  $this->_cart_handler->remove_item($item_id); 	  
	}
	
	function remove_items($item_ids)
	{
	  $this->_cart_handler->remove_items($item_ids);
	}
	
	function get_items()
	{
		return $this->_cart_handler->get_items();
	}
	
	function get_items_array_dataset()
	{
	  $items =& $this->_cart_handler->get_items();
	
		$result_array = array();
		foreach(array_keys($items) as $key)
		{
			$result_array[$key] = $items[$key]->export_attributes();
			$result_array[$key]['summ'] = $items[$key]->get_summ();
		}
		
		return new array_dataset($result_array);
	}

	function count_items()
	{
	  return $this->_cart_handler->count_items();
	}

	function clear()
	{
	  $this->_cart_handler->clear();
	}
	
	function merge(&$cart)
	{
	  $items =& $cart->get_items();
	  
	  foreach(array_keys($items) as $key)
	    $this->add_item($items[$key]);
	}	
}
?>