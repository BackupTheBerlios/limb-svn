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
	public function reset();
		
	public function get_cart_id();
	
	public function set_cart_id($cart_id);
	
	public function add_item($new_item);
	
	public function get_item($id);
		
	public function remove_item($item_id);
	
	public function remove_items($item_ids);
	
	public function get_items();
	
	public function set_items($items);
	
	public function count_items();

	public function clear_items();
}
?>