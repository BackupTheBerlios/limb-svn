<?php

require_once(TEST_CASES_DIR . '/validation/rules/test_single_field_rule.php');
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/unique_user_rule.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');

class test_unique_user_rule extends test_single_field_rule
{
	var $db = null;
	
	function test_unique_user_rule()
	{
		parent::UnitTestCase();
		
 		$this->db = db_factory :: instance();
  }
  
  function setUp()
  {
  	parent :: setUp();
  	
  	$this->db->sql_delete('user');
  	$this->db->sql_delete('sys_site_object');
  	
		$this->db->sql_insert('sys_site_object', array('id' => 1, 'identifier' => 'vasa', 'class_id' => '1', 'current_version' => '1'));
		$this->db->sql_insert('sys_site_object', array('id' => 2, 'identifier' => 'sasa', 'class_id' => '1', 'current_version' => '1'));
		$this->db->sql_insert('user', array('id' => 1, 'name' => 'Vasa', 'password' => '1', 'version' => '1', 'object_id' => '1'));
		$this->db->sql_insert('user', array('id' => 2, 'name' => 'Sasa', 'password' => '1', 'version' => '1', 'object_id' => '2'));
  }
  
  function tearDown()
  {
  	parent :: tearDown();
  	
  	$this->db->sql_delete('user');
  	$this->db->sql_delete('sys_site_object');
  }

	function test_unique_user_rule_correct()
	{
		$this->validator->add_rule(new unique_user_rule('test'));

		$data =& new dataspace();
		$data->set('test', 'maso');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

	function test_unique_user_rule_error()
	{
		$this->validator->add_rule(new unique_user_rule('test'));

		$data =& new dataspace();
		$data->set('test', 'vasa');

		$this->error_list->expectOnce('add_error', array('test', 'DUPLICATE_USER', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
	
	function test_unique_user_rule_correct_edit()
	{
		$this->validator->add_rule(new unique_user_rule('test', 'maso'));

		$data =& new dataspace();
		$data->set('test', 'maso');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
} 

?>