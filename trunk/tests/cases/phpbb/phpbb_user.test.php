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

class test_phpbb_user_test extends UnitTestCase 
{ 
	var $db = null;
	var $object = null; 	
  
  function test_phpbb_user_test() 
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
  
  function tearDown()
  { 
  	$this->_clean_up();

		debug_mock :: tally();
  }
  
  function _clean_up()
  {
		$this->db->sql_delete('phpbb_users');
  }
  
  
  function test_create()
  {
  	$user_data = array(
  		'object_id' => $user_id = 5,
  		'identifier' => 'test_login',
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
		$this->assertEqual($record['user_id'], $user_data['object_id']);
		$this->assertEqual($record['username'], $user_data['identifier']);
		$this->assertEqual($record['user_password'], user :: get_crypted_password($user_data['identifier'], $user_data['password']));
		$this->assertEqual($record['user_email'], $user_data['email']);
  }

  function test_update()
  {
		$this->assertTrue($this->object->update());
  }
  
}

?>