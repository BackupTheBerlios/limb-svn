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
require_once(LIMB_DIR . '/class/core/site_objects/ContentObject.class.php');
require_once(LIMB_SHOP_DIR . 'CartItem.class.php');

class CatalogObject extends ContentObject
{
  public function getCartItem()
  {
    $cart_item = new CartItem($this->getNodeId());

    $cart_item->setDescription($this->getTitle());
    $cart_item->setAttribute('image_id', $this->getAttribute('image_id'));

    return $cart_item;
  }
}

?>