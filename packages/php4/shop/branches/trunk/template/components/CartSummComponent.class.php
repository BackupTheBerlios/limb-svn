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
  function getCartSumm()
  {
    $toolkit =& Limb :: toolkit();
    $locale =& $toolkit->getLocale();

    $inst =& Cart :: instance();
    return number_format($inst->getTotalSumm(),
                         $locale->fract_digits,
                         $locale->decimal_symbol,
                         $locale->thousand_separator);
  }
}

?>