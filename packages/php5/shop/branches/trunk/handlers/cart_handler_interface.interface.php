<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: cart_handler.class.php 566 2004-09-03 14:06:08Z pachanga $
*
***********************************************************************************/
require_once dirname(__FILE__) . '/cart_handler_interface.interface.php';

interface cart_handler_interface
{		
	abstract public function reset();
		
	abstract public function get_cart_id();
	
	abstract public function set_cart_id($cart_id);
	
	abstract public function add_item($new_item);
	
	abstract public function get_item($id);
		
	abstract public function remove_item($item_id);
	
	abstract public function remove_items($item_ids);
	
	abstract public function get_items();
	
	private public function set_items($items);
	
	abstract public function count_items();

	abstract public function clear_items();
}
?>