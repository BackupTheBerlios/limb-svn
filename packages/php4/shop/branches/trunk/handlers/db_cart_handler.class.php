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
require_once(dirname(__FILE__) . '/handlers/cart_handler.class.php');
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'class/core/user.class.php');
require_once(LIMB_DIR . 'class/db_tables/db_table_factory.class.php');

class db_cart_handler extends cart_handler
{
  var $cart_db_table = null;
  
	function db_cart_handler($cart_id)
	{
	  parent :: cart_handler($cart_id);
	  
	  $this->cart_db_table =& db_table_factory :: instance('cart');
	  
	  register_shutdown_function(array(&$this, '_db_cart_handler'));
	}
	
	function reset()
	{
	  $user =& $this->_get_user();
	  $this->_items = array();
	  	  	  
	  $this->_load_items_for_visitor();
	  
	  if($user->is_logged_in())
	  {	    
	    $this->_load_items_for_user();
	  }
	}
	
	function _load_items_for_user()
	{
	  $user =& $this->_get_user();

    $conditions = 'user_id = ' . $user->get_id() . ' AND cart_id <> "'. $this->_cart_id . '"';
    
    $result = $this->_load_items_by_conditions($conditions);
    
    if (!$result)
      return;
    
     $this->cart_db_table->delete($conditions); 
	}
	
	function _load_items_for_visitor()
	{
    $conditions = array(
      'cart_id' => $this->_cart_id
    );
	  
    return $this->_load_items_by_conditions($conditions);
	}
	
	function _load_items_by_conditions($conditions)
	{
    if($arr = $this->cart_db_table->get_list($conditions))
    {
      $record = reset($arr);
      $items = unserialize($record['cart_items']);
      
      foreach(array_keys($items) as $key)
        $this->add_item($items[$key]);
      
      return true;  
    }
    
    return false;
	}
	
	function &_get_user()
	{
	  return user :: instance();
	}
	
	function _db_cart_handler()
	{
	  $user =& $this->_get_user();	  

    $cart_data = array(
      'user_id' => $user->get_id(),
      'last_activity_time' => time(),
      'cart_items' => serialize($this->get_items()),
      'cart_id' => $this->_cart_id,
    );
	  
	  $conditions['cart_id'] = $this->_cart_id;
	  $records = $this->cart_db_table->get_list($conditions);
	  
	  if (!count($records))
	    $this->cart_db_table->insert($cart_data);
	  else
	  {
	    $record = reset($records);
	    $this->cart_db_table->update_by_id($record['id'], $cart_data);
	  }
	}
}
?>