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
require_once(LIMB_DIR . 'core/model/phpbb_user.class.php');

class phpbb_user_test extends UnitTestCase 
{ 
	var $db = null;
	var $object = null; 	
  
  function phpbb_user_test() 
  {
  	parent :: UnitTestCase();
  	
  	$this->db = db_factory :: instance();
  }
  
  function setUp()
  {
		$this->_clean_up();
		
  	parent :: setUp();
  	
  	debug_mock :: init($this);

  	$this->object =& new phpbb_user();
  }
  
  function _create_user()
  {
  	$user_data = array(
  		'id' => $user_id = 5,
  		'identifier' => 'login_test',
  		'password' => 'test',
  		'email' => 'test@here.com',
  	);
  	
  	$this->object->import_attributes($user_data);
  	
		$this->object->create();
  }
  
  function tearDown()
  { 
  	$this->_clean_up();

		debug_mock :: tally();
  }
  
  function _clean_up()
  {
		$this->db->sql_delete('phpbb_users');
		$this->db->sql_delete('phpbb_sessions');
  }
  
  function test_create()
  {
  	$user_data = array(
  		'id' => $user_id = 5,
  		'identifier' => 'login_test',
  		'password' => 'test',
  		'email' => 'test@here.com',
  	);
  	
  	$this->object->import_attributes($user_data);
  	
		$this->assertTrue($this->object->create());
		
		$db_table	=& db_table_factory :: instance('phpbb_users');

		$conditions['user_id'] = $user_id;

		$rows = $db_table->get_list($conditions, '', null);
		
		$this->assertEqual(count($rows), 1);
		
		$record = current($rows);
		$this->assertEqual($record['user_id'], $user_data['id']);
		$this->assertEqual($record['username'], $user_data['identifier']);
		$this->assertEqual($record['user_password'], user :: get_crypted_password($user_data['identifier'], $user_data['password']));
		$this->assertEqual($record['user_email'], $user_data['email']);
  }

  function test_update()
  {
  	$this->_create_user();
  	
  	$user_data = array(
  		'id' => $user_id = 5,
  		'identifier' => 'login_test',
  		'email' => 'test@here.com',
  	);
  	
  	$this->object->import_attributes($user_data);
  	
		$this->assertTrue($this->object->update());
		
		$db_table	=& db_table_factory :: instance('phpbb_users');

		$conditions['user_id'] = $user_id;
		$rows = $db_table->get_list($conditions, '', null);
		
		$this->assertEqual(count($rows), 1);
		
		$record = current($rows);
		$this->assertEqual($record['user_id'], $user_data['id']);
		$this->assertEqual($record['username'], $user_data['identifier']);
		$this->assertEqual($record['user_email'], $user_data['email']);
  }
  
  function test_change_password()
  {
  	$this->_create_user();

  	$user_data = array(
  		'id' => $user_id = 5,
  		'password' => 'crypted_password_test',
  	);
  	
  	$this->object->import_attributes($user_data);
  	
		$this->assertTrue($this->object->change_password());
  
		$db_table	=& db_table_factory :: instance('phpbb_users');

		$conditions['user_id'] = $user_id;
		$rows = $db_table->get_list($conditions, '', null);
		$record = current($rows);
		$this->assertEqual($record['user_password'], $user_data['password']);

		return true;
  }
  
  function test_generate_password()
  {
  	$this->_create_user();

  	$user_data = array(
  		'id' => $user_id = 5,
  		'generated_password' => 'crypted_password_test',
  	);
  	
  	$this->object->import_attributes($user_data);
  	
		$this->assertTrue($this->object->generate_password('test@here.com'));
  
		$db_table	=& db_table_factory :: instance('phpbb_users');

		$conditions['user_id'] = $user_id;
		$rows = $db_table->get_list($conditions, '', null);
		$record = current($rows);
		$this->assertEqual($record['user_newpasswd'], $user_data['generated_password']);

		return true;
  }
  
  function test_login()
  {
  	$this->_login_user(5, array());
		$this->assertTrue($this->object->login('', ''));
		
		$db_table	=& db_table_factory :: instance('phpbb_sessions');
		
		$conditions['session_user_id'] = 5;
		$rows = $db_table->get_list($conditions, '', null);
		$record = current($rows);
		$this->assertEqual($record['session_ip'], ip :: encode_ip(sys :: client_ip()));
		$this->assertEqual($record['session_logged_in'], 1);
  }

  function _login_user($id, $groups)
  {
  	$user =& user :: instance();
  	
  	$user->_set_id($id);
  	$user->_set_groups($groups);  	
  }
}

?>