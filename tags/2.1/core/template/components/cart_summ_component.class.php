<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: poll_component.class.php 45 2004-03-18 16:26:13Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/model/shop/cart.class.php');

class cart_summ_component extends component
{
	function get_cart_summ()
	{
		$cart =& cart :: instance();
		
		return $cart->get_total_summ();
	}
} 

?>