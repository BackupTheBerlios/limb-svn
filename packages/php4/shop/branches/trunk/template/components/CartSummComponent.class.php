<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../../Cart.class.php');

class CartSummComponent extends Component
{
  public function getCartSumm()
  {
    $locale = Limb :: toolkit()->getLocale();

    return number_format(Cart :: instance()->getTotalSumm(),
                         $locale->fract_digits,
                         $locale->decimal_symbol,
                         $locale->thousand_separator);
  }
}

?>