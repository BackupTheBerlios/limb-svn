<?php

require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');

class test_db_mysql extends UnitTestCase
{
	function test_db_mysql($name = 'mysql db test case')
	{
		parent :: UnitTestCase($name);
	} 
	
	function setUpAll()
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
	}
	
	function setUp()
	{ 
		if (!mysql_query('TRUNCATE founding_fathers'))
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
	} 
	
	function tearDown()
	{ 
	}
	
	function test_instance()
	{
		$this->assertReference(db_factory :: instance(), db_factory :: instance());
	} 
	
	function test_execute()
	{
		$db = db_factory :: instance();
		$this->assertNotNull($db->sql_exec("SELECT * FROM founding_fathers"));		
	}
	
	function test_fetch_row()
	{
		$db = db_factory :: instance();
		$this->assertNotEqual($db->sql_exec("SELECT * FROM founding_fathers"), array());
		
		$result = $db->fetch_row();
		$this->assertTrue(is_array($result));
		
		$this->assertEqual($result['id'], 1);
		$this->assertEqual($result['first'], 'George');
		
		$db->fetch_row();
		$db->fetch_row();
		$result = $db->fetch_row();
		$this->assertTrue(is_array($result));
		
		$this->assertEqual($result['id'], 10);
		$this->assertEqual($result['last'], 'Zade');
		
		$db->sql_exec("SELECT * FROM founding_fathers WHERE last='maugli'");
		$this->assertFalse($db->fetch_row());
	}
		
	function test_get_array()
	{
		$db = db_factory :: instance();
		$db->sql_exec("SELECT * FROM founding_fathers");
		
		$result = $db->get_array();
		
		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 4);
		
		$db->sql_exec("SELECT * FROM founding_fathers WHERE id=-1");
		
		$result = $db->get_array();
		
		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 0);
	}
	
	function test_get_array_fancy_indexed()
	{
		$db = db_factory :: instance();
		$db->sql_exec("SELECT * FROM founding_fathers");
		
		$result = $db->get_array('id');

		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 4);
		$this->assertTrue(array_key_exists(1, $result));
		$this->assertTrue(array_key_exists(2, $result));
		$this->assertTrue(array_key_exists(3, $result));
		$this->assertTrue(array_key_exists(10, $result));
		
		$result = $db->get_array('id');
		$this->assertTrue(is_array($result), 'Result should be cleaned');
		$this->assertEqual(sizeof($result), 0);
		
		$db->sql_exec("SELECT * FROM founding_fathers");
		$result = $db->get_array('first');
		
		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 3);
		$this->assertTrue(array_key_exists('George', $result));
		$this->assertTrue(array_key_exists('Benjamin', $result));
		$this->assertTrue(array_key_exists('Alexander', $result));
	}
	
	function test_select()
	{
		$db = db_factory :: instance();
		$db->sql_select("founding_fathers");
		
		$result = $db->get_array();
		
		$this->assertEqual(sizeof($result), 4);
		
		$db->sql_select("founding_fathers", '*', '', '', 2, 2);
		
		$result = $db->get_array('id');
		
		$this->assertEqual(sizeof($result), 2);
		$this->assertTrue(array_key_exists(3, $result));
		$this->assertTrue(array_key_exists(10, $result));
	}
	
	function test_select_fancy_conditions()
	{
		$db = db_factory :: instance();
		
		$db->sql_select("founding_fathers", '*', 'first="Benjamin" AND last="Franklin" AND dog_name=NULL');
		
		$arr1 = $db->get_array();
		
		$db->sql_select("founding_fathers", '*', array('first' => 'Benjamin', 'last' => 'Franklin', 'dog_name' => null));
		
		$arr2 = $db->get_array();
		
		$db->sql_select("founding_fathers", '*', array('first' => 'Benjamin', 'last' => 'Franklin', 'dog_name' => 'NULL'));
		
		$arr3 = $db->get_array();
		
		$this->assertEqual(sizeof(array_diff($arr1, $arr2)), 0);
		$this->assertEqual(sizeof(array_diff($arr1, $arr3)), 0);
	}
	
	function test_count_selected_rows()
	{
		$db = db_factory :: instance();
		$this->assertNotEqual($db->sql_exec("SELECT * FROM founding_fathers"), array());
		
		$this->assertEqual($db->count_selected_rows(), 4);
		
		$db->sql_select("founding_fathers", '*', '', '', 2, 2);
		$this->assertEqual($db->count_selected_rows(), 2);
		
		$db->sql_exec("SELECT * FROM founding_fathers WHERE last='maugli'");
		$this->assertEqual($db->count_selected_rows(), 0);
	}

	function test_insert()
	{
		if (!mysql_query('TRUNCATE founding_fathers'))
			die ('Error creating table: ' . mysql_errno() . ' - ' . mysql_error());
						
		$db = db_factory :: instance();
		
		$db->sql_insert("founding_fathers", array('id' => 2, 'first' => 'Wow', 'last' => 'Hey'));
		$db->sql_insert("founding_fathers", array('id' => 3, 'first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
		
		$this->assertEqual($db->get_sql_insert_id('founding_fathers'), 3);
		
		$db->sql_select("founding_fathers", '*', 'last="Nixon"');
		
		$this->assertEqual(sizeof($arr = $db->get_array()), 1);
		$this->assertEqual($arr[0]['last'], 'Nixon');
	}
	
	function test_update()
	{
		$db = db_factory :: instance();
		
		$db->sql_update("founding_fathers", array('first' => 'Wow', 'last' => 'Hey'), array('id' => 10));
		
		$db->sql_select("founding_fathers", '*', 'last="Hey" AND first="Wow"');
		
		$this->assertEqual(sizeof($arr = $db->get_array()), 1);
		$this->assertEqual($arr[0]['id'], 10);
		
		$db->sql_update("founding_fathers", "test_int=test_int+10", array('id' => 10));
		
		$db->sql_select("founding_fathers", '*', array('test_int' => 10));
		$this->assertEqual(sizeof($arr = $db->get_array()), 1);
		$this->assertEqual($arr[0]['id'], 10);
	}
	
	function test_delete()
	{
		$db = db_factory :: instance();
		
		$db->sql_insert("founding_fathers", array('first' => 'Wow', 'last' => 'Hey'));
		$db->sql_delete("founding_fathers", array('last' => 'Hey', 'first' => 'Wow'));
		
		$db->sql_select("founding_fathers", '*', 'last="Hey" AND first="Wow"');
		$this->assertEqual(sizeof($arr = $db->get_array()), 0);
		
		$db->sql_insert("founding_fathers", array('last' => 'Hey', 'first' => 'Wow'));
		$db->sql_delete("founding_fathers", 'first="Wow" AND last="Hey"');
		
		$db->sql_select("founding_fathers", '*', 'last="Hey" AND first="Wow"');
		$this->assertEqual(sizeof($arr = $db->get_array()), 0);
	}
	
	function test_transactions()
	{
		$db =& db_factory :: instance();
		
		start_user_transaction();
		
		$db->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
		$db->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon2', 'dog_name' => null));
		$db->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon3', 'dog_name' => null));
		
		rollback_user_transaction();
		
		$db->sql_select("founding_fathers", '*', 'last="Nixon"');
		$this->assertEqual(sizeof($db->get_array()), 0);
		
		start_user_transaction();
		
		$db->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon', 'dog_name' => null));
		$db->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon2', 'dog_name' => null));
		$db->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixon3', 'dog_name' => null));
		
		commit_user_transaction();
		
		$db->sql_select("founding_fathers", '*', 'last="Nixon" OR last="Nixon2" OR last="Nixon3"');
		$this->assertEqual(sizeof($db->get_array()), 3);
		
		start_user_transaction();
		
		$db->sql_insert("founding_fathers", array('first' => 'Richard', 'last' => 'Nixxxxx', 'dog_name' => null));

		$db->sql_select("founding_fathers", '*', 'last="Nixxxxx"');
		$arr = $db->get_array();
		$this->assertEqual(sizeof($arr), 1);
		$this->assertEqual($arr[0]['last'], 'Nixxxxx');
		
		rollback_user_transaction();

		$db->sql_select("founding_fathers", '*', 'last="Nixxxxx"');
		$arr = $db->get_array();
		$this->assertEqual(sizeof($arr), 0);
	}
} 
?>