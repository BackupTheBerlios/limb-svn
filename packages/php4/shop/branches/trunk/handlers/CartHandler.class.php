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
require_once(dirname(__FILE__) . '/CartHandlerInterface.interface.php');

class CartHandler implements CartHandlerInterface
{
  var $_cart_id = null;
  var $_items = array();

  function CartHandler($cart_id)
  {
    $this->_cart_id = $cart_id;
  }

  function reset()
  {
    $this->clearItems();
  }

  function getCartId()
  {
    return $this->_cart_id;
  }

  function setCartId($cart_id)
  {
    $this->_cart_id = $cart_id;
  }

  function addItem($new_item)
  {
    $id = $new_item->getId();

    if ($new_item->getAmount() <= 0)
      $new_item->setAmount(1);

    if (isset($this->_items[$id]))
      $new_item->summAmount($this->_items[$id]);

    $this->_items[$id] = $new_item;
  }

  function getItem($id)
  {
    if(isset($this->_items[$id]))
      return $this->_items[$id];
    else
      return false;
  }

  function removeItem($item_id)
  {
    if (isset($this->_items[$item_id]))
      unset($this->_items[$item_id]);
  }

  function removeItems($item_ids)
  {
    foreach($item_ids as $id)
      $this->removeItem($id);
  }

  function getItems()
  {
    return $this->_items;
  }

  function setItems($items)
  {
    $this->_items = $items;
  }

  function countItems()
  {
    if (is_array($this->_items))
      return count($this->_items);
    else
      return 0;
  }

  function clearItems()
  {
    $this->_items = array();
  }
}
?>