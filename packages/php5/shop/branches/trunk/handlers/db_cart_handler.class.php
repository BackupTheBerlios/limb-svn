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
require_once(dirname(__FILE__) . '/cart_handler.class.php');
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'class/core/permissions/user.class.php');
require_once(LIMB_DIR . 'class/db_tables/db_table_factory.class.php');

class db_cart_handler extends cart_handler
{
  protected $cart_db_table = null;
  
	function __construct($cart_id)
	{
	  parent :: __construct($cart_id);
	  
	  $this->cart_db_table = LimbToolsBox :: getToolkit()->createDBTable('cart');
	  
	  register_shutdown_function(array($this, '_db_cart_handler'));
	}
	
	public function reset()
	{
	  $this->clear_items();
	  	  	  
	  $this->_load_items_for_visitor();
	  
	  $user = $this->_get_user();
	  if($user->is_logged_in())
	  {	    
	    $this->_load_items_for_user();
	  }
	}
	
	protected function _load_items_for_user()
	{
	  $user = $this->_get_user();

    $conditions = 'user_id = ' . $user->get_id() . ' AND cart_id <> "'. $this->_cart_id . '"';
    
    if (!$this->_load_items_by_conditions($conditions))
      return;
    
    $this->cart_db_table->delete($conditions); 
	}
	
	protected function _load_items_for_visitor()
	{
    $conditions = array(
      'cart_id' => $this->_cart_id
    );
	  
    return $this->_load_items_by_conditions($conditions);
	}
	
	protected function _load_items_by_conditions($conditions)
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
	
	protected function _get_user()
	{
	  return LimbToolsBox :: getToolkit()->getUser();
	}
	
	public function _db_cart_handler()
	{
	  $user = $this->_get_user();	  

    $cart_data = array(
      'user_id' => $user->get_id(),
      'last_activity_time' => time(),
      'cart_items' => serialize($this->get_items()),
      'cart_id' => $this->_cart_id,
    );
	  
	  $conditions['cart_id'] = $this->_cart_id;
	  $records = $this->cart_db_table->get_list($conditions);
	  
	  if (!count($records))
	  {
	    $this->cart_db_table->insert($cart_data);
	  }
	  else
	  {
	    $record = reset($records);
	    $this->cart_db_table->update_by_id($record['id'], $cart_data);
	  }
	}
}
?>