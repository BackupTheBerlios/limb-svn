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
require_once(dirname(__FILE__) . '/../../cart.class.php');
require_once(dirname(__FILE__) . '/../../handlers/cart_handler.class.php');

Mock :: generate('cart_handler');
Mock :: generate('cart_item');

class cart_handler_test extends LimbTestCase
{
  var $cart_handler;

  function setUp()
  {
    $this->cart_handler = new cart_handler(10);
  }

  function tearDown()
  {
  }

  function test_get_cart_id()
  {
    $this->assertEqual($this->cart_handler->get_cart_id(), 10);
  }

  function test_add_item()
  {
    $item1 = new Mockcart_item($this);
    $item2 = new Mockcart_item($this);

    $item1->expectOnce('get_id');
    $item1->setReturnValue('get_id', 1);

    $item1->expectOnce('get_amount');
    $item1->setReturnValue('get_amount', 0);
    $item1->expectOnce('set_amount', array(1));

    $item2->expectOnce('get_amount');
    $item2->expectOnce('get_id');
    $item2->setReturnValue('get_id', 2);

    $this->cart_handler->add_item($item1);
    $this->cart_handler->add_item($item2);

    $item1->tally();
    $item2->tally();
  }

  function test_add_item_already_added()
  {
    $item1 = new Mockcart_item($this);
    $item2 = new Mockcart_item($this);

    $item1->expectOnce('get_id');
    $item1->setReturnValue('get_id', $item_id = 1);

    $item1->expectOnce('get_amount');
    $item1->setReturnValue('get_amount', 5);
    $item1->expectNever('summ_amount');

    $item2->expectOnce('get_amount');
    $item2->setReturnValue('get_amount', 10);
    $item2->expectOnce('get_id');
    $item2->setReturnValue('get_id', $item_id);
    $item2->expectOnce('summ_amount', array(new IsAExpectation('Mockcart_item')));

    $this->cart_handler->add_item($item1);
    $this->cart_handler->add_item($item2);

    $item1->tally();
    $item2->tally();
  }

  function test_get_item_false()
  {
    $this->assertFalse($this->cart_handler->get_item(-1000));
  }

  function test_get_item()
  {
    $item = new Mockcart_item($this);
    $item->setReturnValue('get_id', 1);

    $this->cart_handler->add_item($item);

    $this->assertIsA($this->cart_handler->get_item(1), 'Mockcart_item');
  }

  function test_remove_item()
  {
    $item = new Mockcart_item($this);
    $item->setReturnValue('get_id', 1);

    $this->cart_handler->add_item($item);
    $this->cart_handler->remove_item(1);

    $this->assertFalse($this->cart_handler->get_item(1));
  }

  function test_clear()
  {
    $item = new Mockcart_item($this);
    $item->setReturnValue('get_id', 1);

    $this->cart_handler->add_item($item);
    $this->cart_handler->clear_items();

    $this->assertFalse($this->cart_handler->get_item(1));
  }
}

?>