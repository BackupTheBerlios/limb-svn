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
require_once(dirname(__FILE__) . '/../../cart.class.php');

class cart_summ_component extends component
{
	public function get_cart_summ()
	{
		$locale = Limb :: toolkit()->getLocale();
		
		return number_format(cart :: instance()->get_total_summ(), 
												 $locale->fract_digits,
												 $locale->decimal_symbol,
												 $locale->thousand_separator);
	}
} 

?>