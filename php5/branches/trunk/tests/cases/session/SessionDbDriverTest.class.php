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
require_once(LIMB_DIR . '/class/core/session/session_db_driver.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');

Mock :: generate('user');
Mock :: generate('LimbToolkit');

class session_db_driver_test extends LimbTestCase
{
  var $db;
  var $driver;
  var $user;

  function setUp()
  {
    $this->user = new Mockuser($this);
    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnValue('getUser', $this->user);

    $this->db = db_factory :: instance();
    $this->toolkit->setReturnValue('getDB', $this->db);

    Limb :: registerToolkit($this->toolkit);

    $this->driver = new session_db_driver();
  }

  function tearDown()
  {
    $this->db->sql_delete('sys_session');

    $this->user->tally();

    Limb :: popToolkit();
  }

  function test_storage_open()
  {
    $this->assertTrue($this->driver->storage_open());
  }

  function test_storage_close()
  {
    $this->assertTrue($this->driver->storage_close());
  }

  function test_storage_read_ok()
  {
    $this->db->sql_insert('sys_session',
                          array('session_id' => $id = 'fghprty121as',
                                'session_data' => $data = 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10,
                                'user_id' => 1));

    $this->db->sql_insert('sys_session',
                          array('session_id' => 'junk',
                                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10,
                                'user_id' => 1));


    $this->assertEqual($data, $this->driver->storage_read($id));
  }

  function test_storage_read_bad_session_id()
  {
    $this->assertFalse($this->driver->storage_read("'bad';DROP sys_session;"));
  }

  function test_storage_read_false()
  {
    $this->db->sql_insert('sys_session',
                          array('session_id' => 'junk',
                                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10,
                                'user_id' => 1));


    $this->assertIdentical(false, $this->driver->storage_read('no_such_session'));
  }

  function test_storage_write_insert()
  {
    $value = 'whatever';
    $id = 20;

    $this->user->expectOnce('get_id');
    $this->user->setReturnValue('get_id', $user_id = 100);

    $this->driver->storage_write($id, $value);

    $this->db->sql_select('sys_session');
    $arr = $this->db->get_array();

    $this->assertEqual(sizeof($arr), 1);

    $record = reset($arr);

    $this->assertEqual($record['session_id'], $id);
    $this->assertEqual($record['session_data'], $value);
    $this->assertEqual($record['user_id'], $user_id);
    $this->assertTrue($record['last_activity_time'] > 0 && $record['last_activity_time'] <= time());
  }

  function test_storage_write_update()
  {
    $this->db->sql_insert('sys_session',
                          array('session_id' => $id = 'fghprty121as',
                                'session_data' => $value = 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => $time = 10,
                                'user_id' => $user_id = 100));

    $this->user->expectNever('get_id');

    $this->driver->storage_write($id, $value);

    $this->db->sql_select('sys_session');
    $arr = $this->db->get_array();

    $this->assertEqual(sizeof($arr), 1);

    $record = reset($arr);

    $this->assertEqual($record['session_id'], $id);
    $this->assertEqual($record['session_data'], $value);
    $this->assertEqual($record['user_id'], $user_id);
    $this->assertTrue($record['last_activity_time'] > $time && $record['last_activity_time'] <= time());
  }

  function test_storage_write_insert_bad_session_id()
  {
    $id = "'fghprty121as';SELECT * FROM test;";
    $value = "'data';DROP sys_session;";

    $this->driver->storage_write($id, $value);

    $this->db->sql_select('sys_session');
    $record = $this->db->fetch_row();

    $this->assertEqual($record['session_id'], $id);
    $this->assertEqual($record['session_data'], $value);
  }

  function test_storage_write_update_bad_session_id()
  {
    $this->db->sql_insert('sys_session',
                          array('session_id' => $id = "'fghprty121as';SELECT * FROM test;",
                                'session_data' => $value = "'data';DROP sys_session;"));

    $this->driver->storage_write($id, $value);

    $this->db->sql_select('sys_session');
    $record = $this->db->fetch_row();

    $this->assertEqual($record['session_id'], $id);
    $this->assertEqual($record['session_data'], $value);
  }

  function test_storage_destroy()
  {
    $this->db->sql_insert('sys_session',
                          array('session_id' => $id = "'fghprty121as';SELECT * FROM test;",
                                'session_data' => "data"));

    $this->db->sql_insert('sys_session',
                          array('session_id' => 'junk',
                                'session_data' => 'junk'));

    $this->driver->storage_destroy($id);

    $this->db->sql_select('sys_session');
    $arr = $this->db->get_array();

    $this->assertEqual(1, sizeof($arr));
    $this->assertEqual($arr[0]['session_id'], 'junk');
  }

  function test_storage_gc_true()
  {
    $this->db->sql_insert('sys_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 301));

    $this->driver->storage_gc(300);

    $this->db->sql_select('sys_session');
    $this->assertTrue(!$this->db->fetch_row());
  }

  function test_storage_gc_false()
  {
    $this->db->sql_insert('sys_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 298));

    $this->driver->storage_gc(300);

    $this->db->sql_select('sys_session');
    $this->assertFalse(!$this->db->fetch_row());
  }

  function test_storage_destroy_user()
  {
    $this->db->sql_insert('sys_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'user_id' => $user_id = 100));

    $this->db->sql_insert('sys_session',
                          array('session_id' => "junk",
                                'session_data' => "junk",
                                'user_id' => 200));

    $this->driver->storage_destroy_user($user_id);

    $this->db->sql_select('sys_session');
    $arr = $this->db->get_array();

    $this->assertEqual(1, sizeof($arr));
    $this->assertEqual($arr[0]['user_id'], 200);
  }
}

?>
