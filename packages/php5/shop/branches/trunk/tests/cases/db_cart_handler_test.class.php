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
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');
require_once(dirname(__FILE__) . '/../../handlers/db_cart_handler.class.php');
require_once(dirname(__FILE__) . '/../../cart_item.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

Mock :: generate('user');
Mock :: generatePartial(
  'db_cart_handler',
  'special_db_cart_handler',
  array('_get_user')
);

class db_cart_handler_test extends LimbTestCase
{
  var $cart_handler;
  var $db;
  var $user;
  
  function setUp()
  {
    $this->db =& db_factory :: instance();
    $this->cart_handler = new special_db_cart_handler($this);
    $this->cart_handler->__construct(10);

    $this->user = new Mockuser($this);
    
    $this->cart_handler->setReturnReference('_get_user', $this->user);
    
    $this->_clean_up();
  }
  
  function tearDown()
  {
    $this->_clean_up();
  }
  
  function _clean_up()
  {
    $this->db->sql_delete('cart');
  }
  
  function test_reset_not_logged_in_user()
  {
    $items = array();
    
    $items[1] = new cart_item(1);
    $items[2] = new cart_item(2);
    
    $items[1]->set_amount(10);
    $items[2]->set_amount(20);
    
    $this->db->sql_insert('cart', array('id' => null, 'cart_id' => 10, 'user_id' => -1, 'cart_items' => serialize($items)));
    
    $this->user->setReturnValue('is_logged_in', false);
    
    $this->cart_handler->reset();
    
    $this->assertEqual($this->cart_handler->get_items(), $items);
  }

  function test_reset_not_logged_in_user_no_db_data()
  {
    $items = array();    
    $items[1] = new cart_item(1);    
    $items[1]->set_amount(10);
    
    $this->db->sql_insert('cart', array('id' => null, 'cart_id' => 1, 'user_id' => -1, 'cart_items' => serialize($items)));
    
    $this->user->setReturnValue('is_logged_in', false);
    
    $this->cart_handler->reset();
    
    $this->assertEqual($this->cart_handler->get_items(), array());
  }
  
  function test_reset_logged_in_user_no_visitor_db_data()
  {
    $items = array();    
    $items[1] = new cart_item(1);    
    $items[1]->set_amount(10);
    
    $this->db->sql_insert('cart', array('id' => null, 'cart_id' => 20, 'user_id' => 1000, 'cart_items' => serialize($items)));
    
    $this->user->setReturnValue('is_logged_in', true);
    $this->user->setReturnValue('get_id', 1000);

    $this->cart_handler->reset();
    
    $this->assertEqual($this->cart_handler->get_items(), $items);
  }

  function test_reset_logged_in_user_db_data()
  {
    $items = array();    
    $items[1] = new cart_item(1);    
    $items[1]->set_amount(10);
    
    $this->db->sql_insert('cart', array('id' => null, 'cart_id' => 10, 'user_id' => 1000, 'cart_items' => serialize($items)));
    
    $this->user->setReturnValue('is_logged_in', true);
    $this->user->setReturnValue('get_id', 1000);

    $this->cart_handler->reset();
    
    $this->assertEqual($this->cart_handler->get_items(), $items);
  }
  
  function test_reset_merge_logged_in_user_db_data()
  {
    $items1 = array();    
    $items1[1] = new cart_item(1);    
    $items1[1]->set_amount(10);

    $items2 = array();    
    $items2[1] = new cart_item(1);    
    $items2[1]->set_amount(20);
    $items2[2] = new cart_item(2);    
    $items2[2]->set_amount(35);
    
    $this->db->sql_insert('cart', array('id' => null, 'cart_id' => 10, 'user_id' => -1, 'cart_items' => serialize($items1)));
    $this->db->sql_insert('cart', array('id' => null, 'cart_id' => 20, 'user_id' => 1000, 'cart_items' => serialize($items2)));

    $this->user->setReturnValue('is_logged_in', true);
    $this->user->setReturnValue('get_id', 1000);

    $this->cart_handler->reset();
    
    $items1[1]->set_amount(30);
    $result_array[1] = $items1[1];
    $result_array[2] = $items2[2];
    
    $this->assertEqual($this->cart_handler->get_items(), $result_array);

    $this->db->sql_select('cart', '*', array('user_id' => 1000));
    $arr = $this->db->get_array();
    $this->assertTrue(empty($arr));
  }
    
  function test_shutdown()
  {
    $item1 = new cart_item(1);
    $item2 = new cart_item(2);
    
    $item1->set_amount(10);
    $item2->set_amount(20);
    
    $this->cart_handler->add_item($item1);
    $this->cart_handler->add_item($item2);
        
    $time = time();
    
    $this->user->setReturnValue('get_id', 1000);

    $this->cart_handler->_db_cart_handler();
    
    $this->db->sql_select('cart');
    $arr = $this->db->get_array();
    
    $this->assertEqual(sizeof($arr), 1);
    
    $record = reset($arr);
    
    $this->assertEqual($record['user_id'], 1000);
    $this->assertEqual($record['cart_id'], 10);//???
    $this->assertTrue($record['last_activity_time'] >= $time);
    $this->assertEqual($record['cart_items'], serialize($this->cart_handler->get_items()));
  }
}

?>