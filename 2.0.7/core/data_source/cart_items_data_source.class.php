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
require_once(LIMB_DIR . 'core/data_source/data_source.class.php');
require_once(LIMB_DIR . 'core/model/shop/cart.class.php');

class cart_items_data_source extends data_source
{
	function cart_items_data_source()
	{
		parent :: data_source();
	}

	function & get_data_set(&$counter, $params=array())
	{
		$cart =& cart :: instance();
		
		$dataset =& $cart->get_items_array_dataset();
		
		$counter = $dataset->get_total_row_count();
		
		return $dataset;
	}		
}


?>
