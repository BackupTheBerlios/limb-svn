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

class cart
{		
	var $_catalog_id = array();
	var $_items = array();
	var $_status = '';
	var $_allow_zero_item_amount = false;
	
	function cart($catalog_id)
	{
		$this->_catalog_id = $catalog_id;
		
		$this->_items =& session :: get('cart_'. $catalog_id .'_items');
		$this->_status =& session :: get('cart_'. $catalog_id .'_status');
	}

	function get_catalog_id()	
	{
		return $this->_catalog_id;
	}
	
 	function & instance($catalog_id = '')
  {
  	$class_name = 'session_cart';
  	$class_index = (!$catalog_id ? 'SESSION_CART' : 'SESSION_CART_'. $catalog_id);
  	$class_index = 'global_'. $class_index;
  	
  	$obj =& $GLOBALS[$class_index];

  	if(get_class($obj) != $class_name)
  	{
  		$obj = & new $class_name($catalog_id);
  		$GLOBALS[$class_index] =& $obj;
  	}
  	
  	return $obj;
  }

	function add_items($items = array())
	{
		if (!count($items))
			return;
		
		foreach($items as $item_id => $data)
		{
			$this->add_item($item_id, $data['amount'], $data['price'], $data['description'] , $data['notes']);
		}	
	}

	function add_item($item_id, $amount = 0, $price = '', $description = array(), $notes = '')
	{
		if ((!is_numeric($amount)) || ((integer)$amount < 0))
			$amount = 0;
		
		if ((!$amount) && (!$this->_allow_zero_item_amount))
			return;
		
		if(!isset($this->_items[$item_id]))
			$this->_items[$item_id]['amount'] = $amount;
		else	
			$this->_items[$item_id]['amount'] = $this->_items[$item_id]['amount'] + $amount;

		$this->_items[$item_id]['price'] = $price;
		$this->_items[$item_id]['description'] = $description;
		$this->_items[$item_id]['notes'] = $notes;
	}
	
	function set_items_amounts($items = array())
	{
		if (!count($items))
			return;
		
		foreach($items as $item_id => $new_amount)
			$this->set_item_amount($item_id, $new_amount);
	}

	function set_item_amount($item_id, $new_amount = 0)
	{
		if ((!is_numeric($new_amount)) || ((integer)$new_amount < 0))
			$new_amount = 0;
		
		if ((!$new_amount) && (!$this->_allow_zero_item_amount))
		{
			if (isset($this->_items[$item_id]))
				unset($this->_items[$item_id]);
			return;
		}

		$this->_items[$item_id]['amount'] = $new_amount;
	}

	function set_items_notes($items = array())
	{
		if (!count($items))
			return;
		
		foreach($items as $item_id => $new_note)
			$this->set_item_amount($item_id, $new_note);
	}

	function set_item_note($item_id, $new_note = '')
	{
		if (isset($this->_items[$item_id]))
			$this->_items[$item_id]['note'] = $new_note;
	}

	function update_cart_items_property($propety_name, $items = array())
	{
		if (!count($items) || !$propety_name)
			return;
		foreach($items as $item_id => $new_value)
			$this->update_cart_item_property($propety_name, $item_id, $new_value);
	}

	function update_cart_item_property($propety_name, $item_id, $new_value = '')
	{
		if (!$propety_name)
			return;
		
		if (isset($this->_items[$item_id]))
			$this->_items[$item_id][$propety_name] = $new_value;
	}
	
	function get_total_summ()
	{
		$summ = 0;
		if ((!count($this->_items)) || (!is_array($this->_items)))
			return $summ;
			
		foreach($this->_items as $item_id => $item_data)
			$summ = $item_data['price'] * $item_data['amount'] + $summ;
		
		return $summ;
	}

	function delete_item($item_id)
	{
		if (isset($this->_items[$item_id]))
			unset($this->_items[$item_id]);
	}

	function delete_items($items = array())
	{
		if (!count($items))
			return;
		
		foreach($items as $item_id => $item_data)
			$this->delete_item($item_id);
	}
	
	function get_items()
	{
		return $this->_items;		
	}

	function get_status()
	{
		return $this->_status;		
	}

	function set_status($new_status = '')
	{
		$this->_status = $new_status;
	}

	function get_items_count()
	{
		if (is_array($this->_items))
			return count($this->_items);		
		else
			return 0;	
	}

	function get_pieces_count()
	{
		$result = 0;

		if (!is_array($this->_items))
			return $result;		
		
		foreach($this->_items as $id => $data)
			$result += $data['amount'];
		
		return $result;
	}

	function clear()
	{
		$this->_items =  array();		
	}
}
?>