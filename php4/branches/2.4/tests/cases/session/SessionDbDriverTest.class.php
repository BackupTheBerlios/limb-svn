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
require_once(LIMB_DIR . '/core/session/SessionDbDriver.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/permissions/User.class.php');

Mock :: generate('User');
Mock :: generate('LimbToolkit');

class SessionDbDriverTest extends LimbTestCase
{
  var $db;
  var $driver;
  var $user;

  function setUp()
  {
    $this->user = new MockUser($this);
    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnReference('getUser', $this->user);

    $this->db =& LimbDbPool :: getConnection();
    $this->toolkit->setReturnReference('getDB', $this->db);

    Limb :: registerToolkit($this->toolkit);

    $this->driver = new SessionDbDriver();
  }

  function tearDown()
  {
    $this->db->sqlDelete('sys_session');

    $this->user->tally();

    Limb :: popToolkit();
  }

  function testStorageOpen()
  {
    $this->assertTrue($this->driver->storageOpen());
  }

  function testStorageClose()
  {
    $this->assertTrue($this->driver->storageClose());
  }

  function testStorageReadOk()
  {
    $this->db->sqlInsert('sys_session',
                          array('session_id' => $id = 'fghprty121as',
                                'session_data' => $data = 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10,
                                'user_id' => 1));

    $this->db->sqlInsert('sys_session',
                          array('session_id' => 'junk',
                                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10,
                                'user_id' => 1));


    $this->assertEqual($data, $this->driver->storageRead($id));
  }

  function testStorageReadBadSessionId()
  {
    $this->assertFalse($this->driver->storageRead("'bad';DROP sys_session;"));
  }

  function testStorageReadFalse()
  {
    $this->db->sqlInsert('sys_session',
                          array('session_id' => 'junk',
                                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10,
                                'user_id' => 1));


    $this->assertIdentical(false, $this->driver->storageRead('no_such_session'));
  }

  function testStorageWriteInsert()
  {
    $value = 'whatever';
    $id = 20;

    $this->user->expectOnce('getId');
    $this->user->setReturnValue('getId', $user_id = 100);

    $this->driver->storageWrite($id, $value);

    $this->db->sqlSelect('sys_session');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 1);

    $record = reset($arr);

    $this->assertEqual($record['session_id'], $id);
    $this->assertEqual($record['session_data'], $value);
    $this->assertEqual($record['user_id'], $user_id);
    $this->assertTrue($record['last_activity_time'] > 0 &&  $record['last_activity_time'] <= time());
  }

  function testStorageWriteUpdate()
  {
    $this->db->sqlInsert('sys_session',
                          array('session_id' => $id = 'fghprty121as',
                                'session_data' => $value = 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => $time = 10,
                                'user_id' => $user_id = 100));

    $this->user->expectNever('getId');

    $this->driver->storageWrite($id, $value);

    $this->db->sqlSelect('sys_session');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 1);

    $record = reset($arr);

    $this->assertEqual($record['session_id'], $id);
    $this->assertEqual($record['session_data'], $value);
    $this->assertEqual($record['user_id'], $user_id);
    $this->assertTrue($record['last_activity_time'] > $time &&  $record['last_activity_time'] <= time());
  }

  function testStorageWriteInsertBadSessionId()
  {
    $id = "'fghprty121as';SELECT * FROM test;";
    $value = "'data';DROP sys_session;";

    $this->driver->storageWrite($id, $value);

    $this->db->sqlSelect('sys_session');
    $record = $this->db->fetchRow();

    $this->assertEqual($record['session_id'], $id);
    $this->assertEqual($record['session_data'], $value);
  }

  function testStorageWriteUpdateBadSessionId()
  {
    $this->db->sqlInsert('sys_session',
                          array('session_id' => $id = "'fghprty121as';SELECT * FROM test;",
                                'session_data' => $value = "'data';DROP sys_session;"));

    $this->driver->storageWrite($id, $value);

    $this->db->sqlSelect('sys_session');
    $record = $this->db->fetchRow();

    $this->assertEqual($record['session_id'], $id);
    $this->assertEqual($record['session_data'], $value);
  }

  function testStorageDestroy()
  {
    $this->db->sqlInsert('sys_session',
                          array('session_id' => $id = "'fghprty121as';SELECT * FROM test;",
                                'session_data' => "data"));

    $this->db->sqlInsert('sys_session',
                          array('session_id' => 'junk',
                                'session_data' => 'junk'));

    $this->driver->storageDestroy($id);

    $this->db->sqlSelect('sys_session');
    $arr = $this->db->getArray();

    $this->assertEqual(1, sizeof($arr));
    $this->assertEqual($arr[0]['session_id'], 'junk');
  }

  function testStorageGcTrue()
  {
    $this->db->sqlInsert('sys_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 301));

    $this->driver->storageGc(300);

    $this->db->sqlSelect('sys_session');
    $this->assertTrue(!$this->db->fetchRow());
  }

  function testStorageGcFalse()
  {
    $this->db->sqlInsert('sys_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 298));

    $this->driver->storageGc(300);

    $this->db->sqlSelect('sys_session');
    $this->assertFalse(!$this->db->fetchRow());
  }

  function testStorageDestroyUser()
  {
    $this->db->sqlInsert('sys_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'user_id' => $user_id = 100));

    $this->db->sqlInsert('sys_session',
                          array('session_id' => "junk",
                                'session_data' => "junk",
                                'user_id' => 200));

    $this->driver->storageDestroyUser($user_id);

    $this->db->sqlSelect('sys_session');
    $arr = $this->db->getArray();

    $this->assertEqual(1, sizeof($arr));
    $this->assertEqual($arr[0]['user_id'], 200);
  }
}

?>
