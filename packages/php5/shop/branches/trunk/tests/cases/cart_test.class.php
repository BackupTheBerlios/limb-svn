<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../../cart.class.php');
require_once(dirname(__FILE__) . '/../../handlers/cart_handler.class.php');

Mock :: generate('cart');
Mock :: generate('cart_handler');
Mock :: generate('cart_item');

class cart_test extends LimbTestCase
{
  var $cart;
  var $cart_item;
  var $cart_handler;
  
  function setUp()
  {
    $this->cart_handler =& new Mockcart_handler($this);
    $this->cart_handler->expectOnce('set_cart_id', array(1));
    $this->cart_handler->expectOnce('reset');
    
    $this->cart =& new cart(1, $h = null);
    $this->cart->set_cart_handler($this->cart_handler);
  }
  
  function tearDown()
  {
    $this->cart_handler->tally();
  } 
  
  function test_instance()
  {
    $this->assertTrue(cart :: instance(10) === cart :: instance(10));
  }
  
  function test_get_default_card_id()
  {
    $cart = new cart(null, $h = null);
    $this->assertEqual($cart->get_cart_id(), session_id());
  }

  function test_get_default_card_handler()
  {
    $cart = new cart(1, $h = null);
    $h = $cart->get_cart_handler();
    
    $this->assertIsA($h, CART_DEFAULT_HANDLER_TYPE . '_cart_handler');
    
    $this->assertEqual($h->get_cart_id(), 1);
  }
  
  function test_initialize_cart_handler()
  {
    $cart_handler =& new Mockcart_handler($this);
    $cart_handler->expectOnce('set_cart_id', array(1));
    $cart_handler->expectOnce('reset');
    $cart = new cart(1, $cart_handler);
    $cart_handler->tally();
  }
  
  function test_set_cart_id()
  {
  }
    
  function test_get_card_id()
  {
    $this->assertEqual($this->cart->get_cart_id(), 1);
  }
  
  function test_get_card_handler()
  {
    $this->assertIsA($this->cart->get_cart_handler(), 'Mockcart_handler');    
  }
  
  function test_add_item()
  {
    $item =& new Mockcart_item($this);
    $this->cart_handler->expectOnce('add_item', array(new IsAExpectation('Mockcart_item')));
    $this->cart->add_item($item);
  }
  
  function test_get_item()
  {
    $this->cart_handler->expectOnce('get_item', array($item_id = 100));
    $this->cart->get_item($item_id);
  }

  function test_get_total_summ()
  {
    $item1 =& new Mockcart_item($this);
    $item2 =& new Mockcart_item($this);
    
    $item1->expectOnce('get_summ');
    $item1->setReturnValue('get_summ', 10);
    
    $item2->expectOnce('get_summ');
    $item2->setReturnValue('get_summ', 40);
    
    $this->cart_handler->expectOnce('get_items');
    $this->cart_handler->setReturnReference('get_items', $arr = array(&$item1, &$item2));
    
    $this->assertEqual($this->cart->get_total_summ(), 50);
    
    $item1->tally();
    $item2->tally();
  }

  function test_get_total_summ_no_items()
  {
    $this->cart_handler->expectOnce('get_items');
    $this->cart_handler->setReturnReference('get_items', $arr = array());
    
    $this->assertEqual($this->cart->get_total_summ(), 0);
  }

  function test_remove_item()
  {
    $this->cart_handler->expectOnce('remove_item', array($item_id = 100));
    $this->cart->remove_item($item_id);
  }

  function test_remove_items()
  {
    $this->cart_handler->expectOnce('remove_items', array(array($item_id1 = 100, $item_id2 = 100)));
    $this->cart->remove_items(array($item_id1, $item_id2));
  }

  function test_get_items()
  {
    $this->cart_handler->expectOnce('get_items');
    $this->cart->get_items();
  }

  function test_get_items_array_dataset()
  {
    $item1 =& new Mockcart_item($this);
    $item2 =& new Mockcart_item($this);
    
    $item1->expectOnce('get_summ');
    $item1->setReturnValue('get_summ', 10);
    
    $item2->expectOnce('get_summ');
    $item2->setReturnValue('get_summ', 40);

    $item1->expectOnce('export');
    $item1->setReturnValue('export', array('id' => 'some_id1'));
    
    $item2->expectOnce('export');
    $item2->setReturnValue('export', array('id' => 'some_id2'));
    
    $this->cart_handler->expectOnce('get_items');
    $this->cart_handler->setReturnReference('get_items', $arr = array(&$item1, &$item2));
    
    $result_array_dataset = new array_dataset(array(
        array('id' => 'some_id1', 'summ' => 10),
        array('id' => 'some_id2', 'summ' => 40),
        )
    );
    
    $this->assertEqual($this->cart->get_items_array_dataset(), $result_array_dataset);
    
    $item1->tally();
    $item2->tally();
  }

  function test_count_items()
  {
    $this->cart_handler->expectOnce('count_items');
    $this->cart->count_items();
  }

  function test_clear()
  {
    $this->cart_handler->expectOnce('clear_items');
    $this->cart->clear();
  }
  
  function test_merge()
  {  
    $item1 =& new Mockcart_item($this);
    $item2 =& new Mockcart_item($this);
  
    $cart =& new Mockcart($this);
    
    $cart->expectOnce('get_items');
    $cart->setReturnReference('get_items', $arr = array(&$item1, &$item2));
    
    $this->cart_handler->expectArgumentsAt(0, 'add_item', array(new IsAExpectation('Mockcart_item')));
    $this->cart_handler->expectArgumentsAt(1, 'add_item', array(new IsAExpectation('Mockcart_item')));
    
    $this->cart->merge($cart);
  }
  
}

?>