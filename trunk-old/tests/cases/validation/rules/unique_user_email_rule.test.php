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
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/unique_user_email_rule.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');

class test_unique_email_user_rule extends test_single_field_rule
{
	var $connection = null;
	
	function test_unique_email_user_rule()
	{
		parent::UnitTestCase();
		
 		$this->connection= db_factory :: get_connection();
  }
  
  function setUp()
  {
  	parent :: setUp();
  	
  	$this->connection->sql_delete('user');
  	$this->connection->sql_delete('sys_site_object');
  	
		$this->connection->sql_insert('sys_site_object', array('id' => 1, 'identifier' => 'vasa', 'class_id' => '1', 'current_version' => '1'));
		$this->connection->sql_insert('sys_site_object', array('id' => 2, 'identifier' => 'sasa', 'class_id' => '1', 'current_version' => '1'));
		$this->connection->sql_insert('user', array('id' => 1, 'name' => 'Vasa',' email' => '1@1.1', 'password' => '1', 'version' => '1', 'object_id' => '1'));
		$this->connection->sql_insert('user', array('id' => 2, 'name' => 'Sasa', 'email' => '2@2.2', 'password' => '1', 'version' => '1', 'object_id' => '2'));
		$this->connection->sql_insert('user', array('id' => 3, 'name' => 'Sasa', 'email' => '3@3.3', 'password' => '1', 'version' => '2', 'object_id' => '2'));
  }
  
  function tearDown()
  {
  	parent :: tearDown();
  	
  	$this->connection->sql_delete('user');
  	$this->connection->sql_delete('sys_site_object');
  }

	function test_unique_user_email_rule_correct()
	{
		$this->validator->add_rule(new unique_user_email_rule('test'));

		$data =& new dataspace();
		$data->set('test', '3@3.3');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

	function test_unique_user_email_rule_error()
	{
		$this->validator->add_rule(new unique_user_email_rule('test'));

		$data =& new dataspace();
		$data->set('test', '2@2.2');

		$this->error_list->expectOnce('add_error', array('test', 'DUPLICATE_USER', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
	
	
	function test_unique_user_email_rule_correct_edit()
	{
		$this->validator->add_rule(new unique_user_email_rule('test', '2@2.2'));

		$data =& new dataspace();
		$data->set('test', '2@2.2');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
} 

?>