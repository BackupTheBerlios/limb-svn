<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');

class test_mysql_connection extends UnitTestCase
{
	var $connection;
	
	function test_mysql_connection($name = 'mysql db test case')
	{
		parent :: UnitTestCase($name);		
	} 
		
	function setUp()
	{ 
		if (!mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD))
			die ('Could not connect: ' . mysql_errno() . ' - ' . mysql_error());
		if (!mysql_select_db(DB_NAME))
			die ('Could not connect: ' . mysql_errno() . ' - ' . mysql_error());
			
		if (!mysql_query("DROP TABLE IF EXISTS founding_fathers;"))
			die ('Error dropping table: ' . mysql_errno() . ' - ' . mysql_error());
			
		$sql = "CREATE TABLE founding_fathers (
		  id int(11) NOT NULL auto_increment,
		  first varchar(50) NOT NULL default '',
		  last varchar(50) NOT NULL default '',
		  dog_name varchar(50) default NULL,
		  test_int int(11) default 0,
		  PRIMARY KEY (id)
		) type=InnoDB;";
		
		if (!mysql_query($sql))
			die ('Error creating table: ' . mysql_errno() . ' - ' . mysql_error());

		$inserts = array(
			"INSERT INTO founding_fathers VALUES (1, 'George', 'Washington', '', 0);",
			"INSERT INTO founding_fathers VALUES (2, 'Alexander', 'Hamilton', '', 0);",
			"INSERT INTO founding_fathers VALUES (3, 'Benjamin', 'Franklin', '', 0);",
			"INSERT INTO founding_fathers VALUES (10, 'Benjamin', 'Zade', '', 0);"
		);

		foreach ($inserts as $insert)
		{
			if (!mysql_query($insert))
				die ('Error inserting ' . mysql_errno() . ' - ' . mysql_error());
		}
		
		$this->connection =& db_factory :: get_connection(); 
	} 
	
	function tearDown()
	{ 
	}
	
	function test_get_id_generator()
	{
		$this->assertIsA($this->connection->get_id_generator(), 'mysql_id_generator');
	}

	function test_get_db_info()
	{
		$this->assertIsA($this->connection->get_db_info(), 'mysql_db_info');
	}
	
	function test_create_statement()
	{
		$stmt = $this->connection->create_statement();
		$this->assertIsA($stmt, 'mysql_statement');
	}

	function test_prepare_statement()
	{
		$sql = 'SELECT * FROM founding_fathers';
		$stmt = $this->connection->prepare_statement($sql);
		$this->assertIsA($stmt, 'mysql_prepared_statement');
	}
	
	function test_execute_query_error()
	{
		$rs = $this->connection->execute_query("SELECT *1 FROM founding_fathers");
		$this->assertError($rs->to_string());		
	}
	
	function test_execute_query()
	{
		$rs = $this->connection->execute_query("SELECT * FROM founding_fathers");
		$this->assertIsA($rs, 'mysql_result_set');		
	}
	
	function test_execute_update()
	{
		/*$this->connection->execute_update();
		
		$connection->sql_update("founding_fathers", array('first' => 'Wow', 'last' => 'Hey'), array('id' => 10));
		
		$connection->sql_select("founding_fathers", '*', 'last="Hey" AND first="Wow"');
		
		$this->assertEqual(sizeof($arr = $connection->get_array()), 1);
		$this->assertEqual($arr[0]['id'], 10);
		
		$connection->sql_update("founding_fathers", "test_int=test_int+10", array('id' => 10));
		
		$connection->sql_select("founding_fathers", '*', array('test_int' => 10));
		$this->assertEqual(sizeof($arr = $connection->get_array()), 1);
		$this->assertEqual($arr[0]['id'], 10);*/
	}

	
/*	function test_fetch_row()
	{
		$connection= db_factory :: get_connection();
		$this->assertNotEqual($connection->sql_exec("SELECT * FROM founding_fathers"), array());
		
		$result = $connection->fetch_row();
		$this->assertTrue(is_array($result));
		
		$this->assertEqual($result['id'], 1);
		$this->assertEqual($result['first'], 'George');
		
		$connection->fetch_row();
		$connection->fetch_row();
		$result = $connection->fetch_row();
		$this->assertTrue(is_array($result));
		
		$this->assertEqual($result['id'], 10);
		$this->assertEqual($result['last'], 'Zade');
		
		$connection->sql_exec("SELECT * FROM founding_fathers WHERE last='maugli'");
		$this->assertFalse($connection->fetch_row());
	}
		
	function test_get_array()
	{
		$connection= db_factory :: get_connection();
		$connection->sql_exec("SELECT * FROM founding_fathers");
		
		$result = $connection->get_array();
		
		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 4);
		
		$connection->sql_exec("SELECT * FROM founding_fathers WHERE id=-1");
		
		$result = $connection->get_array();
		
		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 0);
	}
	
	function test_get_array_fancy_indexed()
	{
		$connection= db_factory :: get_connection();
		$connection->sql_exec("SELECT * FROM founding_fathers");
		
		$result = $connection->get_array('id');

		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 4);
		$this->assertTrue(array_key_exists(1, $result));
		$this->assertTrue(array_key_exists(2, $result));
		$this->assertTrue(array_key_exists(3, $result));
		$this->assertTrue(array_key_exists(10, $result));
		
		$result = $connection->get_array('id');
		$this->assertTrue(is_array($result), 'Result should be cleaned');
		$this->assertEqual(sizeof($result), 0);
		
		$connection->sql_exec("SELECT * FROM founding_fathers");
		$result = $connection->get_array('first');
		
		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 3);
		$this->assertTrue(array_key_exists('George', $result));
		$this->assertTrue(array_key_exists('Benjamin', $result));
		$this->assertTrue(array_key_exists('Alexander', $result));
	}
	
	function test_select()
	{
		$connection= db_factory :: get_connection();
		$connection->sql_select("founding_fathers");
		
		$result = $connection->get_array();
		
		$this->assertEqual(sizeof($result), 4);
		
		$connection->sql_select("founding_fathers", '*', '', '', 2, 2);
		
		$result = $connection->get_array('id');
		
		$this->assertEqual(sizeof($result), 2);
		$this->assertTrue(array_key_exists(3, $result));
		$this->assertTrue(array_key_exists(10, $result));
	}
	
	function test_select_fancy_conditions()
	{
		$connection= db_factory :: get_connection();
		
		$connection->sql_select("founding_fathers", '*', 'first="Benjamin" AND last="Franklin" AND dog_name=NULL');
		
		$arr1 = $connection->get_array();
		
		$connection->sql_select("founding_fathers", '*', array('first' => 'Benjamin', 'last' => 'Franklin', 'dog_name' => null));
		
		$arr2 = $connection->get_array();
		
		$connection->sql_select("founding_fathers", '*', array('first' => 'Benjamin', 'last' => 'Franklin', 'dog_name' => 'NULL'));
		
		$arr3 = $connection->get_array();
		
		$this->assertEqual(sizeof(array_diff($arr1, $arr2)), 0);
		$this->assertEqual(sizeof(array_diff($arr1, $arr3)), 0);
	}
	
	function test_count_selected_rows()
	{
		$connection= db_factory :: get_connection();
		$this->assertNotEqual($connection->sql_exec("SELECT * FROM founding_fathers"), array());
		
		$this->assertEqual($connection->count_selected_rows(), 4);
		
		$connection->sql_select("founding_fathers", '*', '', '', 2, 2);
		$this->assertEqual($connection->count_selected_rows(), 2);
		
		$connection->sql_exec("SELECT * FROM founding_fathers WHERE last='maugli'");
		$this->assertEqual($connection->count_selected_rows(), 0);
	}

	function test_insert()
	{
		if (!mysql_query('TRUNCATE founding_fathers'))
			die ('Error creating table: ' . mysql_errno() . ' - ' . mysql_error());
						
		$connection= db_factory :: get_connection();
		
		$connection->sql_insert("founding_fathers", array('id' => 2, 'first' => 'Wow', 'last' => 'Hey'));
		$connection->sql_insert("founding_fathers", array('id' => 3, 'first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
		
		$this->assertEqual($connection->get_sql_insert_id('founding_fathers'), 3);
		
		$connection->sql_select("founding_fathers", '*', 'last="Nixon"');
		
		$this->assertEqual(sizeof($arr = $connection->get_array()), 1);
		$this->assertEqual($arr[0]['last'], 'Nixon');
	}
		
	function test_delete()
	{
		$connection= db_factory :: get_connection();
		
		$connection->sql_insert("founding_fathers", array('first' => 'Wow', 'last' => 'Hey'));
		$connection->sql_delete("founding_fathers", array('last' => 'Hey', 'first' => 'Wow'));
		
		$connection->sql_select("founding_fathers", '*', 'last="Hey" AND first="Wow"');
		$this->assertEqual(sizeof($arr = $connection->get_array()), 0);
		
		$connection->sql_insert("founding_fathers", array('last' => 'Hey', 'first' => 'Wow'));
		$connection->sql_delete("founding_fathers", 'first="Wow" AND last="Hey"');
		
		$connection->sql_select("founding_fathers", '*', 'last="Hey" AND first="Wow"');
		$this->assertEqual(sizeof($arr = $connection->get_array()), 0);
	}
	
	function test_transactions()
	{
		$connection = & db_factory :: get_connection();
		
		start_user_transaction();
		
		$connection->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
		$connection->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon2', 'dog_name' => null));
		$connection->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon3', 'dog_name' => null));
		
		rollback_user_transaction();
		
		$connection->sql_select("founding_fathers", '*', 'last="Nixon"');
		$this->assertEqual(sizeof($connection->get_array()), 0);
		
		start_user_transaction();
		
		$connection->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
		$connection->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon2', 'dog_name' => null));
		$connection->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon3', 'dog_name' => null));
		
		commit_user_transaction();
		
		$connection->sql_select("founding_fathers", '*', 'last="Nixon" OR last="Nixon2" OR last="Nixon3"');
		$this->assertEqual(sizeof($connection->get_array()), 3);
		
		start_user_transaction();
		
		$connection->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixxxxx', 'dog_name' => null));

		$connection->sql_select("founding_fathers", '*', 'last="Nixxxxx"');
		$arr = $connection->get_array();
		$this->assertEqual(sizeof($arr), 1);
		$this->assertEqual($arr[0]['last'], 'Nixxxxx');
		
		rollback_user_transaction();

		$connection->sql_select("founding_fathers", '*', 'last="Nixxxxx"');
		$arr = $connection->get_array();
		$this->assertEqual(sizeof($arr), 0);
	}*/
} 
?>