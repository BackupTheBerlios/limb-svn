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
require_once(LIMB_DIR . '/tests/cases/site_objects/_content_object_template.test.php');
require_once(LIMB_DIR . '/core/model/site_objects/user_object.class.php');

Mock :: generatePartial(
'user_object',
'user_object_test_version',
array('send_activate_password_email')
);

class test_user_manipulation extends test_content_object_template 
{  	
  function test_user_manipulation() 
  {
  	parent :: test_content_object_template();
  }

  function & _create_site_object()
  {
		$obj =& new user_object_test_version($this);
		$obj->user_object();
		
  	return $obj;
  }
  
  function _set_object_initial_attributes()
  {
  	$this->object->set_attribute('name', 'user name');
  	$this->object->set_attribute('lastname', 'user last name');
  	$this->object->set_attribute('password', 'user password');
  	$this->object->set_attribute('email', 'user@here.com');
  	$this->object->set_attribute('generated_password', 'user generated password');
  }
	
	function _set_object_secondary_update_attributes()
	{
  	$this->object->set_attribute('name', 'user name2');
  	$this->object->set_attribute('lastname', 'user last name2');
  	$this->object->set_attribute('password', 'user password2');
  	$this->object->set_attribute('email', 'user@here.com2');
  	$this->object->set_attribute('generated_password', 'user generated password2');
	}

  function test_failed_change_password_no_id()
  { 
  	debug_mock :: expect_write_error('user id not set');
  	
  	$this->assertFalse($this->object->change_password());
  }

  function test_failed_change_password_no_identifier()
  { 
  	$this->object->set_attribute('id', 10);
  	
  	debug_mock :: expect_write_error('user identifier not set');
  	
  	$this->assertFalse($this->object->change_password());
  }
  
  function test_change_password()
  {
  	$this->test_create();
		
  	$this->object->set_attribute('password', 'new password');

  	$this->assertTrue($this->object->change_password());
  	
  	$this->_verify_user_db_record_password('new password');
  }
  
  function test_failed_change_own_password_no_user_node_id()
  {
  	debug_mock :: expect_write_error('user not logged in - node id is not set');
		
		$this->assertFalse($this->object->change_own_password('changed_password'));
  }
  
  function test_failed_change_own_password_no_record_in_db()
  {
 	 	$this->test_create();

		$_SESSION[user :: get_session_identifier()]['login'] = 'haker_login';
		$_SESSION[user :: get_session_identifier()]['node_id'] = $this->object->get_node_id();

		$this->assertFalse($this->object->change_own_password('changed_password'));
  }

  function test_change_own_password()
  {
 	 	$this->test_create();

		$_SESSION[user :: get_session_identifier()]['login'] = $this->object->get_identifier();
		$_SESSION[user :: get_session_identifier()]['node_id'] = $this->object->get_node_id();

		$this->assertTrue($this->object->change_own_password('changed_password'));
		
		$this->_verify_user_db_record_password('changed_password');
	}
		
	function test_failed_activate_password_no_request_params()
	{
		$this->assertFalse($this->object->activate_password());
		
		$_REQUEST['user'] = 'user@here.com';
		$this->assertFalse($this->object->activate_password());

		$_REQUEST['user'] = '';
		$_REQUEST['id'] = 'dfsd4dsa2da3gkvfgd8v';
		$this->assertFalse($this->object->activate_password());
	}
	
	function test_failed_activate_password_no_user_record()
	{
		$_REQUEST['user'] = 'user2@here.com';
		$_REQUEST['id'] = 'dfsd4dsa2da3gkvfgd8v';
		
		$this->assertFalse($this->object->activate_password());
	}
	
	function test_failed_activate_password_wrong_id()
	{
		$this->test_create();
		
		$_REQUEST['user'] = 'user@here.com';
		$_REQUEST['id'] = 'dfsd4dsa2da3gkvfgd8v';
		
		$this->assertFalse($this->object->activate_password());
	}
	
	function test_failed_activate_password_no_generated_password()
	{
		$this->test_create();

		$_REQUEST['user'] = $this->object->get_attribute('email');
		$_REQUEST['id'] = 'dfsd4dsa2da3gkvfgd8v';
		
		$db_table = $this->object->_get_db_table();
		$this->db->sql_update('user', 
													array('generated_password' => ''),
													array('object_id' => $this->object->get_id()));
		
		$this->assertFalse($this->object->activate_password());
	}
	
	function test_activate_password()
	{
		$this->test_create();
		
		$email = $this->object->get_attribute('email');
		$login = $this->object->get_identifier();
		
		$this->db->sql_update('user', 
			array('generated_password' => user :: get_crypted_password($login, 'generated_password')),
			array('object_id' => $this->object->get_id()));

		$_REQUEST['user'] = $email;
		$_REQUEST['id'] = user :: get_crypted_password($login, 'user password');
		
		$db_table = $this->object->_get_db_table();
		$this->assertTrue($this->object->activate_password());

		$this->_verify_user_db_record_password('generated_password');
	}

	function test_failed_generate_password()
	{
		$this->assertFalse($this->object->generate_password('no@such.a.user', $new_password));
	}
	
	function test_generate_password()
	{
		$this->test_create();
		
		$this->assertTrue($this->object->generate_password('user@here.com', $new_password));
		$this->object->expectOnce('send_activate_password_email');

		$this->_verify_user_db_record_field(
			user :: get_crypted_password($this->object->get_identifier(), $new_password), 
			'generated_password'
		);
	}
	
	function _verify_user_db_record_password($password)
	{
  	$this->_verify_user_db_record_field(
  		user :: get_crypted_password($this->object->get_identifier(), $password), 
  		'password'
  	);  	
	}
	
	function _verify_user_db_record_field($value, $field)
	{
		$db_table = $this->object->_get_db_table();
		$arr = $db_table->get_list();
  	$record = current($arr);
  	
  	$this->assertEqual($record[$field], $value);
	}
}

?>