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
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');

class test_user_object_membership extends UnitTestCase 
{  	
	var $db = null;

  function test_user_object_membership() 
  {
  	parent :: UnitTestCase();

 		$this->db = db_factory :: instance();
  	
  }
  
  function setUp()
  {
  	$this->db->sql_delete('user_in_group');

		$this->db->sql_insert('user_in_group', array('id' => 1, 'user_id' => 10, 'group_id' => 18));
		$this->db->sql_insert('user_in_group', array('id' => 2, 'user_id' => 10, 'group_id' => 29));
		$this->db->sql_insert('user_in_group', array('id' => 3, 'user_id' => 11, 'group_id' => 29));
  }
  
  function tearDown()
  {
  	$this->db->sql_delete('user_in_group');
  }

  function test_get_membership()
  {
		$obj =& site_object_factory :: create('user_object');
		
		$arr = $obj->get_membership(10);
		
		$this->assertTrue(is_array($arr));
		$this->assertEqual(count($arr), 2);
		
		reset($arr);
		$this->assertEqual(key($arr), 18);
	}
	
	function test_get_membership_empty()
	{
		$obj =& site_object_factory :: create('user_object');
		
		$arr = $obj->get_membership(1000);
		
		$this->assertTrue(is_array($arr));
		$this->assertEqual(sizeof($arr), 0);
	}
	    
  function test_save_membership()
  {
	 	$membership = array(100 => 1 , 103 => 1, 104 => 1);

		$obj =& site_object_factory :: create('user_object');
		
		$obj->save_membership(10, $membership);
		
		$db_table	=  & db_table_factory :: instance('user_in_group');
		$rows = $db_table->get_list('', 'id');
		
		$this->assertTrue(is_array($rows));
		$this->assertEqual(count($rows), 4);

		$record = reset($rows);
		$this->assertEqual($record['group_id'], 29);
		
		$record = next($rows);
		$this->assertEqual($record['group_id'], 100);
		$this->assertEqual($record['user_id'], 10);
  }
  
  function test_save_wrong_membership()
  {
	 	$membership = array(100 => 1, 103 => 1, ';drop user_in_group;' => 1);

		$obj =& site_object_factory :: create('user_object');
		
		$obj->save_membership(10, $membership);
		
		$db_table	=  & db_table_factory :: instance('user_in_group');
		$rows = $db_table->get_list('', 'id');
		
		$this->assertTrue(is_array($rows));
		$this->assertEqual(count($rows), 4);
		
		$record = end($rows);
		$this->assertEqual($record['group_id'], (int)';drop user_in_group;');
  }
}

?>