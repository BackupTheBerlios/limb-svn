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
require_once(LIMB_DIR . 'core/datasource/datasource.class.php');
require_once(LIMB_DIR . 'core/model/shop/cart.class.php');

class cart_items_datasource extends datasource
{
	function cart_items_datasource()
	{
		parent :: datasource();
	}

	function & get_dataset(&$counter, $params=array())
	{
		$cart =& cart :: instance();
		
		$dataset =& $cart->get_items_array_dataset();
		
		$counter = $dataset->get_total_row_count();
		
		return $dataset;
	}		
}


?>
