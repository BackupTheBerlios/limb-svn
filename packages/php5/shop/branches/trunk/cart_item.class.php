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
	public function __construct($id)
	{
		parent :: __construct();
		
		$this->_set_id($id);
		
		$this->_define_class_path();
	}
	
	private function _define_class_path()
	{
	  $this->__session_class_path = __FILE__;
	}
	
	private function _set_id($id)
	{
		$this->set('id', $id);
	}
	
	public function get_id()
	{
		return (int)$this->get('id');
	}
	
	public function get_price()
	{
		return 1*$this->get('price', 0);
	}
	
	public function set_price($price)
	{
		$this->set('price', $price);
	}
	
	public function get_amount()
	{
		return 1*$this->get('amount', 0);
	}

	public function set_amount($amount)
	{
		$this->set('amount', $amount);
	}

	public function get_description()
	{
		return $this->get('description');
	}
	
	public function set_description($description)
	{
		$this->set('description', $description);
	}
	
	public function get_summ()
	{
		return $this->get_amount() * $this->get_price();
	}
	
	public function summ_amount($item)
	{
		$this->set_amount($this->get_amount() + $item->get_amount());
	}
}

?>