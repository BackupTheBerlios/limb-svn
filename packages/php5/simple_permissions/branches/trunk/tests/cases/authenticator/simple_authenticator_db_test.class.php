<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');	
require_once(dirname(__FILE__) . '/../../../simple_authenticator.class.php');

class simple_authenticator_db_test extends LimbTestCase 
{
  var $auth;
    	
	function setUp()
	{
	  $this->auth = new simple_authenticator();
	  
	  load_testing_db_dump(dirname(__FILE__) . '/../../sql/simple_authenticator.sql');
	}
	
	function tearDown()
	{  	
  	clear_testing_db_tables();
  	user :: instance()->logout();
	}
	  
  function test_login_ok()
  { 
    $this->auth->login(array('login' => 'vasa', 'password' => '1', 'locale_id' => 'en'));
    
  	$user = user :: instance();  	

  	$this->assertTrue($user->is_logged_in());
  	$this->assertEqual($user->get_id(), 1);
  	$this->assertEqual($user->get_login(), 'vasa');
  	$this->assertEqual($user->get('node_id'), 2);
  	$this->assertEqual($user->get('groups'), array(3 => 'visitors', 4 => 'admins' ));  	
  	$this->assertEqual($user->get('locale_id'), 'en');  	
  }
  
  function test_logout()
  {
    $this->auth->logout();

  	$user = user :: instance();  	
  	
  	$this->assertEqual($user->get('groups'), array(3 => 'visitors'));  	
  }    
}
?>