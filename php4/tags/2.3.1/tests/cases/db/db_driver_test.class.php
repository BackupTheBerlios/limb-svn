<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: db_mysql_test.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');

SimpleTestOptions :: ignore('db_driver_test');

class db_driver_test extends LimbTestCase
{
  function _create_db_driver()
  {
    return null;
  }

  function setUp()
  {
    $this->driver =& $this->_create_db_driver();

    $this->driver->sql_delete('founding_fathers');

    $inserts = array(
      array('id' => 1, 'first' => 'George', 'last' => 'Washington', 'dog_name' => '0',  'int_test' => 0),
      array('id' => 2, 'first' => 'Alexander', 'last' => 'Hamilton', 'dog_name' => '0',  'int_test' => 0),
      array('id' => 3, 'first' => 'Benjamin', 'last' => 'Franklin', 'dog_name' => '0',  'int_test' => 0),
      array('id' => 10, 'first' => 'Benjamin', 'last' => 'Zade', 'dog_name' => '0',  'int_test' => 0)
    );

    foreach($inserts as $insert)
    {
      $this->driver->sql_insert('founding_fathers', $insert);
    }
  }

  function test_instance()
  {
    $this->assertReference(db_factory :: instance(), db_factory :: instance());
  }

  function test_execute()
  {
    $this->assertNotNull($this->driver->sql_exec("SELECT * FROM founding_fathers"));
  }

  function test_fetch_row()
  {
    $this->assertNotEqual($this->driver->sql_exec("SELECT * FROM founding_fathers"), array());

    $result = $this->driver->fetch_row();
    $this->assertTrue(is_array($result));

    $this->assertEqual($result['id'], 1);
    $this->assertEqual($result['first'], 'George');

    $this->driver->fetch_row();
    $this->driver->fetch_row();
    $result = $this->driver->fetch_row();
    $this->assertTrue(is_array($result));

    $this->assertEqual($result['id'], 10);
    $this->assertEqual($result['last'], 'Zade');

    $this->driver->sql_exec("SELECT * FROM founding_fathers WHERE last='maugli'");
    $this->assertFalse($this->driver->fetch_row());
  }

  function test_get_array()
  {
    $this->driver->sql_exec("SELECT * FROM founding_fathers");

    $result = $this->driver->get_array();

    $this->assertTrue(is_array($result));
    $this->assertEqual(sizeof($result), 4);

    $this->driver->sql_exec("SELECT * FROM founding_fathers WHERE id=-1");

    $result = $this->driver->get_array();

    $this->assertTrue(is_array($result));
    $this->assertEqual(sizeof($result), 0);
  }

  function test_get_array_fancy_indexed()
  {
    $this->driver->sql_exec("SELECT * FROM founding_fathers");

    $result = $this->driver->get_array('id');

    $this->assertTrue(is_array($result));
    $this->assertEqual(sizeof($result), 4);
    $this->assertTrue(array_key_exists(1, $result));
    $this->assertTrue(array_key_exists(2, $result));
    $this->assertTrue(array_key_exists(3, $result));
    $this->assertTrue(array_key_exists(10, $result));

    $result = $this->driver->get_array('id');
    $this->assertTrue(is_array($result), 'Result should be cleaned');
    $this->assertEqual(sizeof($result), 0);

    $this->driver->sql_exec("SELECT * FROM founding_fathers");
    $result = $this->driver->get_array('first');

    $this->assertTrue(is_array($result));
    $this->assertEqual(sizeof($result), 3);
    $this->assertTrue(array_key_exists('George', $result));
    $this->assertTrue(array_key_exists('Benjamin', $result));
    $this->assertTrue(array_key_exists('Alexander', $result));
  }

  function test_select()
  {
    $this->driver->sql_select("founding_fathers");

    $result = $this->driver->get_array();

    $this->assertEqual(sizeof($result), 4);

    $this->driver->sql_select("founding_fathers", '*', '', '', 2, 2);

    $result = $this->driver->get_array('id');

    $this->assertEqual(sizeof($result), 2);
    $this->assertTrue(array_key_exists(3, $result));
    $this->assertTrue(array_key_exists(10, $result));
  }

  function test_select_fancy_conditions()
  {
    $this->driver->sql_select("founding_fathers", '*', 'first="Benjamin" AND last="Franklin" AND dog_name=NULL');

    $arr1 = $this->driver->get_array();

    $this->driver->sql_select("founding_fathers", '*', array('first' => 'Benjamin', 'last' => 'Franklin', 'dog_name' => null));

    $arr2 = $this->driver->get_array();

    $this->driver->sql_select("founding_fathers", '*', array('first' => 'Benjamin', 'last' => 'Franklin', 'dog_name' => 'NULL'));

    $arr3 = $this->driver->get_array();

    $this->assertEqual(sizeof(array_diff($arr1, $arr2)), 0);
    $this->assertEqual(sizeof(array_diff($arr1, $arr3)), 0);
  }

  function test_count_selected_rows()
  {
    $this->assertNotEqual($this->driver->sql_exec("SELECT * FROM founding_fathers"), array());

    $this->assertEqual($this->driver->count_selected_rows(), 4);

    $this->driver->sql_select("founding_fathers", '*', '', '', 2, 2);
    $this->assertEqual($this->driver->count_selected_rows(), 2);

    $this->driver->sql_exec("SELECT * FROM founding_fathers WHERE last='maugli'");
    $this->assertEqual($this->driver->count_selected_rows(), 0);
  }

  function test_insert()
  {
    $this->driver->sql_insert("founding_fathers", array('id' => 20, 'first' => 'Wow', 'last' => 'Hey'));
    $this->driver->sql_insert("founding_fathers", array('id' => 30, 'first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));

    $this->assertEqual($this->driver->get_sql_insert_id('founding_fathers'), 30);

    $this->driver->sql_select("founding_fathers", '*', 'last="Nixon"');

    $this->assertEqual(sizeof($arr = $this->driver->get_array()), 1);
    $this->assertEqual($arr[0]['last'], 'Nixon');
  }

  function test_update()
  {
    $this->driver->sql_update("founding_fathers", array('first' => 'Wow', 'last' => 'Hey'), array('id' => 10));

    $this->driver->sql_select("founding_fathers", '*', 'last="Hey" AND first="Wow"');

    $this->assertEqual(sizeof($arr = $this->driver->get_array()), 1);
    $this->assertEqual($arr[0]['id'], 10);

    $this->driver->sql_update("founding_fathers", "int_test=int_test+10", array('id' => 10));

    $this->driver->sql_select("founding_fathers", '*', array('int_test' => 10));
    $this->assertEqual(sizeof($arr = $this->driver->get_array()), 1);
    $this->assertEqual($arr[0]['id'], 10);
  }

  function test_delete()
  {
    $this->driver->sql_insert("founding_fathers", array('first' => 'Wow', 'last' => 'Hey'));
    $this->driver->sql_delete("founding_fathers", array('last' => 'Hey', 'first' => 'Wow'));

    $this->driver->sql_select("founding_fathers", '*', 'last="Hey" AND first="Wow"');
    $this->assertEqual(sizeof($arr = $this->driver->get_array()), 0);

    $this->driver->sql_insert("founding_fathers", array('last' => 'Hey', 'first' => 'Wow'));
    $this->driver->sql_delete("founding_fathers", 'first="Wow" AND last="Hey"');

    $this->driver->sql_select("founding_fathers", '*', 'last="Hey" AND first="Wow"');
    $this->assertEqual(sizeof($arr = $this->driver->get_array()), 0);
  }

  function test_transactions()
  {
    start_user_transaction();

    $this->driver->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
    $this->driver->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon2', 'dog_name' => null));
    $this->driver->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon3', 'dog_name' => null));

    rollback_user_transaction();

    $this->driver->sql_select("founding_fathers", '*', 'last="Nixon"');
    $this->assertEqual(sizeof($this->driver->get_array()), 0);

    start_user_transaction();

    $this->driver->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
    $this->driver->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon2', 'dog_name' => null));
    $this->driver->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon3', 'dog_name' => null));

    commit_user_transaction();

    $this->driver->sql_select("founding_fathers", '*', 'last="Nixon" OR last="Nixon2" OR last="Nixon3"');
    $this->assertEqual(sizeof($this->driver->get_array()), 3);

    start_user_transaction();

    $this->driver->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixxxxx', 'dog_name' => null));

    $this->driver->sql_select("founding_fathers", '*', 'last="Nixxxxx"');
    $arr = $this->driver->get_array();
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['last'], 'Nixxxxx');

    rollback_user_transaction();

    $this->driver->sql_select("founding_fathers", '*', 'last="Nixxxxx"');
    $arr = $this->driver->get_array();
    $this->assertEqual(sizeof($arr), 0);
  }
}
?>