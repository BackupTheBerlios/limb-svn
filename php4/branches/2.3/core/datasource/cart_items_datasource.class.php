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
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');
require_once(LIMB_DIR . '/core/model/shop/cart.class.php');

class cart_items_datasource extends datasource
{
  function & get_dataset(&$counter, $params=array())
  {
    $cart =& cart :: instance();

    $dataset =& $cart->get_items_array_dataset();

    return $dataset;
  }
}


?>
