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
require_once(dirname(__FILE__) . '/../../handlers/CartHandler.class.php');

Mock :: generate('Cart');
Mock :: generate('CartHandler');
Mock :: generate('CartItem');

class CartTest extends LimbTestCase
{
  var $cart;
  var $cart_item;
  var $cart_handler;

  function setUp()
  {
    $this->cart_handler = new MockCartHandler($this);
    $this->cart_handler->expectOnce('setCartId', array(1));
    $this->cart_handler->expectOnce('reset');

    $this->cart = new Cart(1, $h = null);
    $this->cart->setCartHandler($this->cart_handler);
  }

  function tearDown()
  {
    $this->cart_handler->tally();
  }

  function testInstance()
  {
    $this->assertTrue(Cart :: instance(10) === Cart :: instance(10));
  }

  function testGetDefaultCardId()
  {
    $cart = new Cart(null, $h = null);
    $this->assertEqual($cart->getCartId(), session_id());
  }

  function testGetDefaultCardHandler()
  {
    $cart = new Cart(1, $h = null);
    $h = $cart->getCartHandler();

    $this->assertIsA($h, CART_DEFAULT_HANDLER_TYPE . 'CartHandler');

    $this->assertEqual($h->getCartId(), 1);
  }

  function testInitializeCartHandler()
  {
    $cart_handler = new MockCartHandler($this);
    $cart_handler->expectOnce('setCartId', array(1));
    $cart_handler->expectOnce('reset');
    $cart = new Cart(1, $cart_handler);
    $cart_handler->tally();
  }

  function testSetCartId()
  {
  }

  function testGetCardId()
  {
    $this->assertEqual($this->cart->getCartId(), 1);
  }

  function testGetCardHandler()
  {
    $this->assertIsA($this->cart->getCartHandler(), 'MockCartHandler');
  }

  function testAddItem()
  {
    $item = new MockCartItem($this);
    $this->cart_handler->expectOnce('addItem', array(new IsAExpectation('MockCartItem')));
    $this->cart->addItem($item);
  }

  function testGetItem()
  {
    $this->cart_handler->expectOnce('getItem', array($item_id = 100));
    $this->cart->getItem($item_id);
  }

  function testGetTotalSumm()
  {
    $item1 = new MockCartItem($this);
    $item2 = new MockCartItem($this);

    $item1->expectOnce('getSumm');
    $item1->setReturnValue('getSumm', 10);

    $item2->expectOnce('getSumm');
    $item2->setReturnValue('getSumm', 40);

    $this->cart_handler->expectOnce('getItems');
    $this->cart_handler->setReturnValue('getItems', $arr = array($item1, $item2));

    $this->assertEqual($this->cart->getTotalSumm(), 50);

    $item1->tally();
    $item2->tally();
  }

  function testGetTotalSummNoItems()
  {
    $this->cart_handler->expectOnce('getItems');
    $this->cart_handler->setReturnValue('getItems', $arr = array());

    $this->assertEqual($this->cart->getTotalSumm(), 0);
  }

  function testRemoveItem()
  {
    $this->cart_handler->expectOnce('removeItem', array($item_id = 100));
    $this->cart->removeItem($item_id);
  }

  function testRemoveItems()
  {
    $this->cart_handler->expectOnce('removeItems', array(array($item_id1 = 100, $item_id2 = 100)));
    $this->cart->removeItems(array($item_id1, $item_id2));
  }

  function testGetItems()
  {
    $this->cart_handler->expectOnce('getItems');
    $this->cart->getItems();
  }

  function testGetItemsArrayDataset()
  {
    $item1 = new MockCartItem($this);
    $item2 = new MockCartItem($this);

    $item1->expectOnce('getSumm');
    $item1->setReturnValue('getSumm', 10);

    $item2->expectOnce('getSumm');
    $item2->setReturnValue('getSumm', 40);

    $item1->expectOnce('export');
    $item1->setReturnValue('export', array('id' => 'someId1'));

    $item2->expectOnce('export');
    $item2->setReturnValue('export', array('id' => 'someId2'));

    $this->cart_handler->expectOnce('getItems');
    $this->cart_handler->setReturnValue('getItems', $arr = array($item1, $item2));

    $result_array_dataset = new ArrayDataset(array(
        array('id' => 'some_id1', 'summ' => 10),
        array('id' => 'some_id2', 'summ' => 40),
        )
    );

    $this->assertEqual($this->cart->getItemsArrayDataset(), $result_array_dataset);

    $item1->tally();
    $item2->tally();
  }

  function testCountItems()
  {
    $this->cart_handler->expectOnce('countItems');
    $this->cart->countItems();
  }

  function testClear()
  {
    $this->cart_handler->expectOnce('clearItems');
    $this->cart->clear();
  }

  function testMerge()
  {
    $item1 = new MockCartItem($this);
    $item2 = new MockCartItem($this);

    $cart = new MockCart($this);

    $cart->expectOnce('getItems');
    $cart->setReturnValue('getItems', $arr = array($item1, $item2));

    $this->cart_handler->expectArgumentsAt(0, 'addItem', array(new IsAExpectation('MockCartItem')));
    $this->cart_handler->expectArgumentsAt(1, 'addItem', array(new IsAExpectation('MockCartItem')));

    $this->cart->merge($cart);
  }

}

?>