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
  protected $_cart_id = null;
  protected $_items = array();

  function __construct($cart_id)
  {
    $this->_cart_id = $cart_id;
  }

  public function reset()
  {
    $this->clearItems();
  }

  public function getCartId()
  {
    return $this->_cart_id;
  }

  public function setCartId($cart_id)
  {
    $this->_cart_id = $cart_id;
  }

  public function addItem($new_item)
  {
    $id = $new_item->getId();

    if ($new_item->getAmount() <= 0)
      $new_item->setAmount(1);

    if (isset($this->_items[$id]))
      $new_item->summAmount($this->_items[$id]);

    $this->_items[$id] = $new_item;
  }

  public function getItem($id)
  {
    if(isset($this->_items[$id]))
      return $this->_items[$id];
    else
      return false;
  }

  public function removeItem($item_id)
  {
    if (isset($this->_items[$item_id]))
      unset($this->_items[$item_id]);
  }

  public function removeItems($item_ids)
  {
    foreach($item_ids as $id)
      $this->removeItem($id);
  }

  public function getItems()
  {
    return $this->_items;
  }

  public function setItems($items)
  {
    $this->_items = $items;
  }

  public function countItems()
  {
    if (is_array($this->_items))
      return count($this->_items);
    else
      return 0;
  }

  public function clearItems()
  {
    $this->_items = array();
  }
}
?>