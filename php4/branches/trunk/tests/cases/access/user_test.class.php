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
require_once(LIMB_DIR . '/tests/cases/db_test.class.php');
require_once(LIMB_DIR . '/core/lib/security/user.class.php');	

class user_test extends db_test 
{  	
	var $dump_file = 'user_login.sql';
	
	function tearDown()
	{
  	$user =& user :: instance();
  	$user->logout();	
	}
	
  function _login_user($id, $groups)
  {
  	$user =& user :: instance();
  	
  	$user->_set_id($id);
  	$user->_set_groups($groups);  	
  }	
  
  function test_login_true()
  {
  	$user =& user :: instance();
  	$this->assertTrue($user->login('vasa', '1'));
  	$this->assertTrue($user->login('sasa', '1'));
  	
  	$this->assertTrue($user->is_logged_in());
  	$this->assertEqual($user->get_id(), 2);
  	$this->assertEqual($user->get_node_id(), 3);
  	$this->assertEqual($user->get_login(), 'sasa');
  }    
  
  function test_login_failure()
  {
  	$user =& user :: instance();
  	
  	$this->assertFalse($user->login('vas', '1'));
  	$this->assertFalse($user->login('vasa', '2'));
  	$this->assertFalse($user->login('sasa', '2'));
  	$this->assertFalse($user->is_logged_in());
  }
  
  function test_logout()
  {
  	$user =& user :: instance();
  	
  	$user->login('vasa', '1');
  	$user->logout();
  	$this->assertFalse($user->is_logged_in());
  }

	function test_default_visitor_group()
	{
		$user =& user :: instance();
		
		$groups = $user->get_groups();
		
		$this->assertTrue(is_array($groups));
		$this->assertTrue(in_array('visitors', $groups));
	}
	
	function test_user_get_groups()
	{
		$user =& user :: instance();
		
		$user->login('vasa', 1);
		$groups = $user->get_groups();
		
		$this->assertTrue(is_array($groups));
		$this->assertEqual(sizeof($groups), 2);
		$this->assertTrue(in_array('visitors', $groups));
		$this->assertTrue(in_array('admins', $groups));
	}
  	
	function test_user_in_groups()
	{
		$user =& user :: instance();
		
		$user->login('vasa', 1);
		$this->assertTrue($user->is_in_groups(array(0 => 'members', 'admins')));
		$this->assertFalse($user->is_in_groups(array(0 => 'members', 'operators')));
		$this->assertFalse($user->is_in_groups(array(0 => 'members')));

		$this->assertTrue($user->is_in_groups(array(0 => 'visitors')));
	}
}
?>
