<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: unique_user_rule.test.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/user_old_password_rule.class.php');

class test_user_old_password_rule extends test_single_field_rule
{
	function test_user_old_password_rule()
	{
		parent::UnitTestCase();
  }
  
  function setUp()
  {
  	parent :: setUp();

		$_SESSION[user :: get_session_identifier()]['login'] = 'admin';
		$_SESSION[user :: get_session_identifier()]['password'] = '66d4aaa5ea177ac32c69946de3731ec0';
		$_SESSION[user :: get_session_identifier()]['node_id'] = 1;
		$_SESSION[user :: get_session_identifier()]['is_logged_in'] = true;
  }
  
  function tearDown()
  {
  	parent :: tearDown();
  	
  	user :: logout();
  }

	function test_user_old_password_rule_correct()
	{
		$this->validator->add_rule(new user_old_password_rule('old_password'));

		$data =& new dataspace();
		$data->set('old_password', 'test');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
	
	function test_user_old_password_rule_wrong_password()
	{
		$this->validator->add_rule(new user_old_password_rule('old_password'));

		$data =& new dataspace();
		$data->set('old_password', 'wrong_pass');

		$this->error_list->expectOnce('add_error', array('old_password', 'WRONG_OLD_PASSWORD', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}

	function test_user_old_password_rule_empty_password()
	{
		$this->validator->add_rule(new user_old_password_rule('old_password'));

		$data =& new dataspace();
		$data->set('old_password', '');

		$this->error_list->expectOnce('add_error', array('old_password', 'WRONG_OLD_PASSWORD', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
} 

?>