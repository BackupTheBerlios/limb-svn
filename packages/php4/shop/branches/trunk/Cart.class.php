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
require_once(dirname(__FILE__) . '/CartItem.class.php');
require_once(LIMB_DIR . '/class/core/ArrayDataset.class.php');
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');

define('CART_DEFAULT_ID', session_id());
@define('CART_DEFAULT_HANDLER_TYPE', 'session');

class Cart
{
  var $_cart_id = null;
  var $_cart_handler = null;

  function Cart($cart_id, $handler)
  {
    if($cart_id === null)
      $this->_cart_id = CART_DEFAULT_ID;
    else
      $this->_cart_id = $cart_id;

    $this->_initializeCartHandler($handler);
  }

  function & instance($cart_id = null, $handler = null)
  {
    if (!isset($GLOBALS['CartGlobalInstance']) || !is_a($GLOBALS['CartGlobalInstance'], 'Cart'))
      $GLOBALS['CartGlobalInstance'] =& new Cart($cart_id = null, $handler = null);

    return $GLOBALS['CartGlobalInstance'];
  }

  function _initializeCartHandler(&$handler)
  {
    if($handler === null)
    {
      switch(CART_DEFAULT_HANDLER_TYPE)
      {
        case 'session':
          include_once(dirname(__FILE__) . '/handlers/SessionCartHandler.class.php');
          $this->_cart_handler = new SessionCartHandler($this->_cart_id);
        break;

        case 'db':
          include_once(dirname(__FILE__) . '/handlers/DbCartHandler.class.php');
          $this->_cart_handler = new DbCartHandler($this->_cart_id);
        break;

        default:
          throw new LimbException('unknown default cart handler type', array('type' => CART_DEFAULT_HANDLER_TYPE));
      }

      $this->_cart_handler->reset();
    }
    else
    {
      $this->_cart_handler =& $handler;
      $this->_cart_handler->setCartId($this->_cart_id);
      $this->_cart_handler->reset();
    }
  }

  function setCartHandler($handler)
  {
    $this->_cart_handler = $handler;
    $this->_cart_handler->setCartId($this->_cart_id);
    $this->_cart_handler->reset();
  }

  function getCartId()
  {
    return $this->_cart_id;
  }

  function getCartHandler()
  {
    return $this->_cart_handler;
  }

  function addItem($new_item)
  {
    $this->_cart_handler->addItem($new_item);
  }

  function getItem($item_id)
  {
    return $this->_cart_handler->getItem($item_id);
  }

  function getTotalSumm()
  {
    $items = $this->_cart_handler->getItems();
    $summ = 0;

    foreach(array_keys($items) as $key)
      $summ += $items[$key]->getSumm();

    return $summ;
  }

  function removeItem($item_id)
  {
    $this->_cart_handler->removeItem($item_id);
  }

  function removeItems($item_ids)
  {
    $this->_cart_handler->removeItems($item_ids);
  }

  function getItems()
  {
    return $this->_cart_handler->getItems();
  }

  function setItems(&$items)
  {
    return $this->_cart_handler->setItems($items);
  }

  function getItemsArrayDataset()
  {
    $items = $this->_cart_handler->getItems();

    $result_array = array();
    foreach(array_keys($items) as $key)
    {
      $result_array[$key] = $items[$key]->export();
      $result_array[$key]['summ'] = $items[$key]->getSumm();
    }

    return new ArrayDataset($result_array);
  }

  function countItems()
  {
    return $this->_cart_handler->countItems();
  }

  function clear()
  {
    $this->_cart_handler->clearItems();
  }

  function merge($cart)
  {
    $items = $cart->getItems();

    foreach(array_keys($items) as $key)
      $this->addItem($items[$key]);
  }
}
?>