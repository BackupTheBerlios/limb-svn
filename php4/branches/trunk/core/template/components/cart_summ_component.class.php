<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/model/shop/cart.class.php');

class cart_summ_component extends component
{
  function get_cart_summ()
  {
    $cart =& cart :: instance();
    $locale = locale :: instance();

    return number_format($cart->get_total_summ(), $locale->fract_digits, $locale->decimal_symbol, $locale->thousand_separator);
  }
}

?>