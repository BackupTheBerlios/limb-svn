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
require_once(LIMB_DIR . '/core/permissions/User.class.php');
require_once(dirname(__FILE__) . '/../../handlers/DbCartHandler.class.php');
require_once(dirname(__FILE__) . '/../../CartItem.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

Mock :: generate('User');
Mock :: generatePartial(
  'DbCartHandler',
  'SpecialDbCartHandler',
  array('_getUser')
);

class DbCartHandlerTest extends LimbTestCase
{
  var $cart_handler;
  var $db;
  var $user;

  function setUp()
  {
    $this->db =& LimbDbPool :: getConnection();
    $this->cart_handler = new SpecialDbCartHandler($this);
    $this->cart_handler->DbCartHandler(10);

    $this->user = new MockUser($this);

    $this->cart_handler->setReturnReference('_getUser', $this->user);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('cart');
  }

  function testResetNotLoggedInUser()
  {
    $items = array();

    $items[1] = new CartItem(1);
    $items[2] = new CartItem(2);

    $items[1]->setAmount(10);
    $items[2]->setAmount(20);

    $this->db->sqlInsert('cart', array('id' => null, 'cart_id' => 10, 'user_id' => -1, 'cart_items' => serialize($items)));

    $this->user->setReturnValue('isLoggedIn', false);

    $this->cart_handler->reset();

    $this->assertEqual($this->cart_handler->getItems(), $items);
  }

  function testResetNotLoggedInUserNoDbData()
  {
    $items = array();
    $items[1] = new CartItem(1);
    $items[1]->setAmount(10);

    $this->db->sqlInsert('cart', array('id' => null, 'cart_id' => 1, 'user_id' => -1, 'cart_items' => serialize($items)));

    $this->user->setReturnValue('isLoggedIn', false);

    $this->cart_handler->reset();

    $this->assertEqual($this->cart_handler->getItems(), array());
  }

  function testResetLoggedInUserNoVisitorDbData()
  {
    $items = array();
    $items[1] = new CartItem(1);
    $items[1]->setAmount(10);

    $this->db->sqlInsert('cart', array('id' => null, 'cart_id' => 20, 'user_id' => 1000, 'cart_items' => serialize($items)));

    $this->user->setReturnValue('isLoggedIn', true);
    $this->user->setReturnValue('getId', 1000);

    $this->cart_handler->reset();

    $this->assertEqual($this->cart_handler->getItems(), $items);
  }

  function testResetLoggedInUserDbData()
  {
    $items = array();
    $items[1] = new CartItem(1);
    $items[1]->setAmount(10);

    $this->db->sqlInsert('cart', array('id' => null, 'cart_id' => 10, 'user_id' => 1000, 'cart_items' => serialize($items)));

    $this->user->setReturnValue('isLoggedIn', true);
    $this->user->setReturnValue('getId', 1000);

    $this->cart_handler->reset();

    $this->assertEqual($this->cart_handler->getItems(), $items);
  }

  function testResetMergeLoggedInUserDbData()
  {
    $items1 = array();
    $items1[1] = new CartItem(1);
    $items1[1]->setAmount(10);

    $items2 = array();
    $items2[1] = new CartItem(1);
    $items2[1]->setAmount(20);
    $items2[2] = new CartItem(2);
    $items2[2]->setAmount(35);

    $this->db->sqlInsert('cart', array('id' => null, 'cart_id' => 10, 'user_id' => -1, 'cart_items' => serialize($items1)));
    $this->db->sqlInsert('cart', array('id' => null, 'cart_id' => 20, 'user_id' => 1000, 'cart_items' => serialize($items2)));

    $this->user->setReturnValue('isLoggedIn', true);
    $this->user->setReturnValue('getId', 1000);

    $this->cart_handler->reset();

    $items1[1]->setAmount(30);
    $result_array[1] = $items1[1];
    $result_array[2] = $items2[2];

    $this->assertEqual($this->cart_handler->getItems(), $result_array);

    $this->db->sqlSelect('cart', '*', array('user_id' => 1000));
    $arr = $this->db->getArray();
    $this->assertTrue(empty($arr));
  }

  function testShutdown()
  {
    $item1 = new CartItem(1);
    $item2 = new CartItem(2);

    $item1->setAmount(10);
    $item2->setAmount(20);

    $this->cart_handler->addItem($item1);
    $this->cart_handler->addItem($item2);

    $time = time();

    $this->user->setReturnValue('getId', 1000);

    $this->cart_handler->_dbCartHandler();

    $this->db->sqlSelect('cart');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 1);

    $record = reset($arr);

    $this->assertEqual($record['user_id'], 1000);
    $this->assertEqual($record['cart_id'], 10);//???
    $this->assertTrue($record['last_activity_time'] >= $time);
    $this->assertEqual($record['cart_items'], serialize($this->cart_handler->getItems()));
  }
}

?>