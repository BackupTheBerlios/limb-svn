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
require_once(LIMB_DIR . 'class/core/object.class.php');

class cart_item extends object
{
	function cart_item($id)
	{
		parent :: object();
		
		$this->_set_id($id);
		
		$this->_define_class_path();
	}
	
	function _define_class_path()
	{
	  //IMPORTANT!!! 
	  $this->__session_class_path = __FILE__;
	}
	
	function _set_id($id)
	{
		$this->set('id', $id);
	}
	
	function get_id()
	{
		return (int)$this->get('id');
	}
	
	function get_price()
	{
		return 1*$this->get('price', 0);
	}
	
	function set_price($price)
	{
		$this->set('price', $price);
	}
	
	function get_amount()
	{
		return 1*$this->get('amount', 0);
	}

	function set_amount($amount)
	{
		$this->set('amount', $amount);
	}

	function get_description()
	{
		return $this->get('description');
	}
	
	function set_description($description)
	{
		$this->set('description', $description);
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