<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: cart_handler.class.php 566 2004-09-03 14:06:08Z pachanga $
*
***********************************************************************************/
require_once dirname(__FILE__) . '/cart_handler_interface.interface.php';

interface CartHandlerInterface
{
  public function reset();

  public function getCartId();

  public function setCartId($cart_id);

  public function addItem($new_item);

  public function getItem($id);

  public function removeItem($item_id);

  public function removeItems($item_ids);

  public function getItems();

  public function setItems($items);

  public function countItems();

  public function clearItems();
}
?>