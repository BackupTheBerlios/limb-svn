<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/cart_item.class.php');
require_once(LIMB_DIR . 'class/core/array_dataset.class.php');
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');

define('CART_DEFAULT_ID', session_id());

if(!defined('CART_DEFAULT_HANDLER_TYPE'))
  define('CART_DEFAULT_HANDLER_TYPE', 'session');

class cart
{	
	protected static $_instance = null;
	
	protected $_cart_id = null;
	protected $_cart_handler = null;

	function __construct($cart_id, $handler)
	{
	  if($cart_id === null)
		  $this->_cart_id = CART_DEFAULT_ID;
		else
		  $this->_cart_id = $cart_id;
		
		$this->_initialize_cart_handler($handler);
	}

	static public function instance($cart_id = null, $handler = null)
	{
    if (!self :: $_instance)
      self :: $_instance = new cart($cart_id, $handler);

    return self :: $_instance;
	}
		
	protected function _initialize_cart_handler($handler)
	{
	  if($handler === null)
	  {
	    switch(CART_DEFAULT_HANDLER_TYPE)
	    {
	      case 'session':
	        include_once(dirname(__FILE__) . '/handlers/session_cart_handler.class.php');
	        $this->_cart_handler = new session_cart_handler($this->_cart_id);
	      break;
	        
	      case 'db':
	        include_once(dirname(__FILE__) . '/handlers/db_cart_handler.class.php');
	        $this->_cart_handler = new db_cart_handler($this->_cart_id);
        break;
        
        default:
          throw new LimbException('unknown default cart handler type', array('type' => CART_DEFAULT_HANDLER_TYPE));
	    }
	    
	    $this->_cart_handler->reset();
	  }
	  else
	  {
	    $this->_cart_handler = $handler;
	    $this->_cart_handler->set_cart_id($this->_cart_id);
	    $this->_cart_handler->reset();
	  }
	}

  public function set_cart_handler($handler)
  {
    $this->_cart_handler = $handler;
    $this->_cart_handler->set_cart_id($this->_cart_id);
    $this->_cart_handler->reset();
  }
	
	public function get_cart_id()	
	{
		return $this->_cart_id;
	}
	
	public function get_cart_handler()
	{
	  return $this->_cart_handler;
	}
	
	public function add_item($new_item)
	{
	  $this->_cart_handler->add_item($new_item); 
	}
	
	public function get_item($item_id)
	{
	  return $this->_cart_handler->get_item($item_id); 	  
	}
		
	public function get_total_summ()
	{
	  $items = $this->_cart_handler->get_items();
		$summ = 0;
			
		foreach(array_keys($items) as $key)
			$summ += $items[$key]->get_summ();
		
		return $summ;
	}

	public function remove_item($item_id)
	{
	  $this->_cart_handler->remove_item($item_id); 	  
	}
	
	public function remove_items($item_ids)
	{
	  $this->_cart_handler->remove_items($item_ids);
	}
	
	public function get_items()
	{
		return $this->_cart_handler->get_items();
	}
	
	public function set_items(&$items)
	{
	  return $this->_cart_handler->set_items($items);
	}
	
	public function get_items_array_dataset()
	{
	  $items = $this->_cart_handler->get_items();
	
		$result_array = array();
		foreach(array_keys($items) as $key)
		{
			$result_array[$key] = $items[$key]->export();
			$result_array[$key]['summ'] = $items[$key]->get_summ();
		}
		
		return new array_dataset($result_array);
	}

	public function count_items()
	{
	  return $this->_cart_handler->count_items();
	}

	public function clear()
	{
	  $this->_cart_handler->clear_items();
	}
	
	public function merge($cart)
	{
	  $items = $cart->get_items();
	  
	  foreach(array_keys($items) as $key)
	    $this->add_item($items[$key]);
	}	
}
?>