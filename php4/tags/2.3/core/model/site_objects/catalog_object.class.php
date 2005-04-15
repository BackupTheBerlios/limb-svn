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
require_once(LIMB_DIR . '/core/model/site_objects/content_object.class.php');
require_once(LIMB_DIR . '/core/model/shop/cart_item.class.php');

class catalog_object extends content_object
{
  function _define_class_properties()
  {
    return array(
      'ordr' => 1,
      'can_be_parent' => 0,
    );
  }

  function & get_cart_item()
  {
    $cart_item = new cart_item($this->get_node_id());

    $cart_item->set_description($this->get_title());
    $cart_item->set_attribute('image_id', $this->get_attribute('image_id'));

    return $cart_item;
  }
}

?>