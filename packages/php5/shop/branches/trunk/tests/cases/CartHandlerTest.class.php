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

Mock :: generate('CartHandler');
Mock :: generate('CartItem');

class CartHandlerTest extends LimbTestCase
{
  var $cart_handler;

  function setUp()
  {
    $this->cart_handler = new CartHandler(10);
  }

  function tearDown()
  {
  }

  function testGetCartId()
  {
    $this->assertEqual($this->cart_handler->getCartId(), 10);
  }

  function testAddItem()
  {
    $item1 = new MockCartItem($this);
    $item2 = new MockCartItem($this);

    $item1->expectOnce('getId');
    $item1->setReturnValue('getId', 1);

    $item1->expectOnce('getAmount');
    $item1->setReturnValue('getAmount', 0);
    $item1->expectOnce('setAmount', array(1));

    $item2->expectOnce('getAmount');
    $item2->expectOnce('getId');
    $item2->setReturnValue('getId', 2);

    $this->cart_handler->addItem($item1);
    $this->cart_handler->addItem($item2);

    $item1->tally();
    $item2->tally();
  }

  function testAddItemAlreadyAdded()
  {
    $item1 = new MockCartItem($this);
    $item2 = new MockCartItem($this);

    $item1->expectOnce('getId');
    $item1->setReturnValue('getId', $item_id = 1);

    $item1->expectOnce('getAmount');
    $item1->setReturnValue('getAmount', 5);
    $item1->expectNever('summAmount');

    $item2->expectOnce('getAmount');
    $item2->setReturnValue('getAmount', 10);
    $item2->expectOnce('getId');
    $item2->setReturnValue('getId', $item_id);
    $item2->expectOnce('summAmount', array(new IsAExpectation('MockCartItem')));

    $this->cart_handler->addItem($item1);
    $this->cart_handler->addItem($item2);

    $item1->tally();
    $item2->tally();
  }

  function testGetItemFalse()
  {
    $this->assertFalse($this->cart_handler->getItem(-1000));
  }

  function testGetItem()
  {
    $item = new MockCartItem($this);
    $item->setReturnValue('getId', 1);

    $this->cart_handler->addItem($item);

    $this->assertIsA($this->cart_handler->getItem(1), 'MockCartItem');
  }

  function testRemoveItem()
  {
    $item = new MockCartItem($this);
    $item->setReturnValue('getId', 1);

    $this->cart_handler->addItem($item);
    $this->cart_handler->removeItem(1);

    $this->assertFalse($this->cart_handler->getItem(1));
  }

  function testClear()
  {
    $item = new MockCartItem($this);
    $item->setReturnValue('getId', 1);

    $this->cart_handler->addItem($item);
    $this->cart_handler->clearItems();

    $this->assertFalse($this->cart_handler->getItem(1));
  }
}

?>