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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');

class DbMysqlTest extends LimbTestCase
{
  function dbMysqlTest($name = 'mysql db test case')
  {
    parent :: LimbTestCase($name);

    if (!mysql_connect(getIniOption('common.ini', 'host', 'DB'), getIniOption('common.ini', 'login', 'DB'), getIniOption('common.ini', 'password', 'DB')))
      die ('Could not connect: ' . mysql_errno() . ' - ' . mysql_errno());
    if (!mysql_select_db(getIniOption('common.ini', 'name', 'DB')))
      die ('Could not connect: ' . mysql_errno() . ' - ' . mysql_errno());

    if (!mysql_query("DROP TABLE IF EXISTS founding_fathers;"))
      die ('Error dropping table: ' . mysql_errno() . ' - ' . mysql_errno());

    $sql = "CREATE TABLE founding_fathers (
      id int(11) NOT NULL auto_increment,
      first varchar(50) NOT NULL default '',
      last varchar(50) NOT NULL default '',
      dog_name varchar(50) default NULL,
      int_test int(11) default 0,
      PRIMARY KEY (id)
    ) type=InnoDB;";

    if (!mysql_query($sql))
      die ('Error creating table: ' . mysql_errno() . ' - ' . mysql_errno());
  }

  function setUp()
  {
    mysql_query("DELETE FROM founding_fathers;");

    $inserts = array(
      "INSERT INTO founding_fathers VALUES (1, 'George', 'Washington', '', 0);",
      "INSERT INTO founding_fathers VALUES (2, 'Alexander', 'Hamilton', '', 0);",
      "INSERT INTO founding_fathers VALUES (3, 'Benjamin', 'Franklin', '', 0);",
      "INSERT INTO founding_fathers VALUES (10, 'Benjamin', 'Zade', '', 0);"
    );

    foreach ($inserts as $insert)
    {
      if (!mysql_query($insert))
        die ('Error inserting ' . mysql_errno() . ' - ' . mysql_errno());
    }
  }

  function tearDown()
  {
    mysql_query("DELETE FROM founding_fathers;");
  }

  function testInstance()
  {
    $this->assertTrue(DbFactory :: instance() === DbFactory :: instance());
  }

  function testExecute()
  {
    $db =& DbFactory :: instance();
    $this->assertNotNull($db->sqlExec("SELECT * FROM founding_fathers"));
  }

  function testFetchRow()
  {
    $db =& DbFactory :: instance();
    $this->assertNotEqual($db->sqlExec("SELECT * FROM founding_fathers"), array());

    $result = $db->fetchRow();
    $this->assertTrue(is_array($result));

    $this->assertEqual($result['id'], 1);
    $this->assertEqual($result['first'], 'George');

    $db->fetchRow();
    $db->fetchRow();
    $result = $db->fetchRow();
    $this->assertTrue(is_array($result));

    $this->assertEqual($result['id'], 10);
    $this->assertEqual($result['last'], 'Zade');

    $db->sqlExec("SELECT * FROM founding_fathers WHERE last='maugli'");
    $this->assertFalse($db->fetchRow());
  }

  function testGetArray()
  {
    $db =& DbFactory :: instance();
    $db->sqlExec("SELECT * FROM founding_fathers");

    $result = $db->getArray();

    $this->assertTrue(is_array($result));
    $this->assertEqual(sizeof($result), 4);

    $db->sqlExec("SELECT * FROM founding_fathers WHERE id=-1");

    $result = $db->getArray();

    $this->assertTrue(is_array($result));
    $this->assertEqual(sizeof($result), 0);
  }

  function testGetArrayFancyIndexed()
  {
    $db =& DbFactory :: instance();
    $db->sqlExec("SELECT * FROM founding_fathers");

    $result = $db->getArray('id');

    $this->assertTrue(is_array($result));
    $this->assertEqual(sizeof($result), 4);
    $this->assertTrue(array_key_exists(1, $result));
    $this->assertTrue(array_key_exists(2, $result));
    $this->assertTrue(array_key_exists(3, $result));
    $this->assertTrue(array_key_exists(10, $result));

    $result = $db->getArray('id');
    $this->assertTrue(is_array($result), 'Result should be cleaned');
    $this->assertEqual(sizeof($result), 0);

    $db->sqlExec("SELECT * FROM founding_fathers");
    $result = $db->getArray('first');

    $this->assertTrue(is_array($result));
    $this->assertEqual(sizeof($result), 3);
    $this->assertTrue(array_key_exists('George', $result));
    $this->assertTrue(array_key_exists('Benjamin', $result));
    $this->assertTrue(array_key_exists('Alexander', $result));
  }

  function testSelect()
  {
    $db =& DbFactory :: instance();
    $db->sqlSelect("founding_fathers");

    $result = $db->getArray();

    $this->assertEqual(sizeof($result), 4);

    $db->sqlSelect("founding_fathers", '*', '', '', 2, 2);

    $result = $db->getArray('id');

    $this->assertEqual(sizeof($result), 2);
    $this->assertTrue(array_key_exists(3, $result));
    $this->assertTrue(array_key_exists(10, $result));
  }

  function testSelectFancyConditions()
  {
    $db =& DbFactory :: instance();

    $db->sqlSelect("founding_fathers", '*', 'first="Benjamin" AND last="Franklin" AND dog_name=NULL');

    $arr1 = $db->getArray();

    $db->sqlSelect("founding_fathers", '*', array('first' => 'Benjamin', 'last' => 'Franklin', 'dog_name' => null));

    $arr2 = $db->getArray();

    $db->sqlSelect("founding_fathers", '*', array('first' => 'Benjamin', 'last' => 'Franklin', 'dog_name' => 'NULL'));

    $arr3 = $db->getArray();

    $this->assertEqual(sizeof(array_diff($arr1, $arr2)), 0);
    $this->assertEqual(sizeof(array_diff($arr1, $arr3)), 0);
  }

  function testCountSelectedRows()
  {
    $db =& DbFactory :: instance();
    $this->assertNotEqual($db->sqlExec("SELECT * FROM founding_fathers"), array());

    $this->assertEqual($db->countSelectedRows(), 4);

    $db->sqlSelect("founding_fathers", '*', '', '', 2, 2);
    $this->assertEqual($db->countSelectedRows(), 2);

    $db->sqlExec("SELECT * FROM founding_fathers WHERE last='maugli'");
    $this->assertEqual($db->countSelectedRows(), 0);
  }

  function testInsert()
  {
    if (!mysql_query('TRUNCATE founding_fathers'))
      die ('Error creating table: ' . mysql_errno() . ' - ' . mysql_errno());

    $db =& DbFactory :: instance();

    $db->sqlInsert("founding_fathers", array('id' => 2, 'first' => 'Wow', 'last' => 'Hey'));
    $db->sqlInsert("founding_fathers", array('id' => 3, 'first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));

    $this->assertEqual($db->getSqlInsertId('founding_fathers'), 3);

    $db->sqlSelect("founding_fathers", '*', 'last="Nixon"');

    $this->assertEqual(sizeof($arr = $db->getArray()), 1);
    $this->assertEqual($arr[0]['last'], 'Nixon');
  }

  function testUpdate()
  {
    $db =& DbFactory :: instance();

    $db->sqlUpdate("founding_fathers", array('first' => 'Wow', 'last' => 'Hey'), array('id' => 10));

    $db->sqlSelect("founding_fathers", '*', 'last="Hey" AND first="Wow"');

    $this->assertEqual(sizeof($arr = $db->getArray()), 1);
    $this->assertEqual($arr[0]['id'], 10);

    $db->sqlUpdate("founding_fathers", "int_test=int_test+10", array('id' => 10));

    $db->sqlSelect("founding_fathers", '*', array('int_test' => 10));
    $this->assertEqual(sizeof($arr = $db->getArray()), 1);
    $this->assertEqual($arr[0]['id'], 10);
  }

  function testDelete()
  {
    $db =& DbFactory :: instance();

    $db->sqlInsert("founding_fathers", array('first' => 'Wow', 'last' => 'Hey'));
    $db->sqlDelete("founding_fathers", array('last' => 'Hey', 'first' => 'Wow'));

    $db->sqlSelect("founding_fathers", '*', 'last="Hey" AND first="Wow"');
    $this->assertEqual(sizeof($arr = $db->getArray()), 0);

    $db->sqlInsert("founding_fathers", array('last' => 'Hey', 'first' => 'Wow'));
    $db->sqlDelete("founding_fathers", 'first="Wow" AND last="Hey"');

    $db->sqlSelect("founding_fathers", '*', 'last="Hey" AND first="Wow"');
    $this->assertEqual(sizeof($arr = $db->getArray()), 0);
  }

  function testTransactions()
  {
    $db =& DbFactory :: instance();

    startUserTransaction();

    $db->sqlInsert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
    $db->sqlInsert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon2', 'dog_name' => null));
    $db->sqlInsert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon3', 'dog_name' => null));

    rollbackUserTransaction();

    $db->sqlSelect("founding_fathers", '*', 'last="Nixon"');
    $this->assertEqual(sizeof($db->getArray()), 0);

    startUserTransaction();

    $db->sqlInsert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
    $db->sqlInsert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon2', 'dog_name' => null));
    $db->sqlInsert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon3', 'dog_name' => null));

    commitUserTransaction();

    $db->sqlSelect("founding_fathers", '*', 'last="Nixon" OR last="Nixon2" OR last="Nixon3"');
    $this->assertEqual(sizeof($db->getArray()), 3);

    startUserTransaction();

    $db->sqlInsert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixxxxx', 'dog_name' => null));

    $db->sqlSelect("founding_fathers", '*', 'last="Nixxxxx"');
    $arr = $db->getArray();
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['last'], 'Nixxxxx');

    rollbackUserTransaction();

    $db->sqlSelect("founding_fathers", '*', 'last="Nixxxxx"');
    $arr = $db->getArray();
    $this->assertEqual(sizeof($arr), 0);
  }
}
?>