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

if(!defined('CART_DEFAULT_HANDLER_TYPE'))
  define('CART_DEFAULT_HANDLER_TYPE', 'session');

class Cart
{
  protected static $_instance = null;

  protected $_cart_id = null;
  protected $_cart_handler = null;

  function __construct($cart_id, $handler)
  {
    if($cart_id === null)
      $this->_cart_id = CART_DEFAULT_ID;
    else
      $this->_cart_id = $cart_id;

    $this->_initializeCartHandler($handler);
  }

  static public function instance($cart_id = null, $handler = null)
  {
    if (!self :: $_instance)
      self :: $_instance = new Cart($cart_id, $handler);

    return self :: $_instance;
  }

  protected function _initializeCartHandler($handler)
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
      $this->_cart_handler = $handler;
      $this->_cart_handler->setCartId($this->_cart_id);
      $this->_cart_handler->reset();
    }
  }

  public function setCartHandler($handler)
  {
    $this->_cart_handler = $handler;
    $this->_cart_handler->setCartId($this->_cart_id);
    $this->_cart_handler->reset();
  }

  public function getCartId()
  {
    return $this->_cart_id;
  }

  public function getCartHandler()
  {
    return $this->_cart_handler;
  }

  public function addItem($new_item)
  {
    $this->_cart_handler->addItem($new_item);
  }

  public function getItem($item_id)
  {
    return $this->_cart_handler->getItem($item_id);
  }

  public function getTotalSumm()
  {
    $items = $this->_cart_handler->getItems();
    $summ = 0;

    foreach(array_keys($items) as $key)
      $summ += $items[$key]->getSumm();

    return $summ;
  }

  public function removeItem($item_id)
  {
    $this->_cart_handler->removeItem($item_id);
  }

  public function removeItems($item_ids)
  {
    $this->_cart_handler->removeItems($item_ids);
  }

  public function getItems()
  {
    return $this->_cart_handler->getItems();
  }

  public function setItems(&$items)
  {
    return $this->_cart_handler->setItems($items);
  }

  public function getItemsArrayDataset()
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

  public function countItems()
  {
    return $this->_cart_handler->countItems();
  }

  public function clear()
  {
    $this->_cart_handler->clearItems();
  }

  public function merge($cart)
  {
    $items = $cart->getItems();

    foreach(array_keys($items) as $key)
      $this->addItem($items[$key]);
  }
}
?>