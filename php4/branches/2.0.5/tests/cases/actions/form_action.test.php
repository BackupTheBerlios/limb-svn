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
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/size_range_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');

class form_action1 extends form_action
{
	function form_action1($name = '')
	{
		parent :: form_action($name);
	}
	
	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('username'));
		$this->validator->add_rule(new required_rule('password'));
		$this->validator->add_rule(new required_rule('password_confirm'));
		$this->validator->add_rule(new size_range_rule('password', 6, 15));
		$this->validator->add_rule(new size_range_rule('password_confirm', 6, 15));
	}
	
	function _valid_perform()
	{
		return true;
	}
	
	function _first_time_perform()
	{
		return false;
	}
}

class test_form_action extends UnitTestCase 
{
	var $debug = null;
	  	
  function test_form_action() 
  {
  	parent :: UnitTestCase();
  }

  function setUp()
  {
  	debug_mock :: init($this);
  	
  	$dataspace =& dataspace :: instance();
  	$dataspace->import(array());

  	$dataspace =& dataspace :: instance('test1');
  	$dataspace->import(array());
  	
  	unset($_REQUEST['test1']);
  	unset($_REQUEST['submitted']);
  	unset($_REQUEST['username']);
  	unset($_REQUEST['password']);
  	unset($_REQUEST['password_confirm']);
  }
  
  function tearDown()
  {
  	debug_mock :: tally();
  	
  	$dataspace =& dataspace :: instance();
  	$dataspace->import(array());

  	$dataspace =& dataspace :: instance('test1');
  	$dataspace->import(array());

  	unset($_REQUEST['test1']);
  	unset($_REQUEST['submitted']);
  	unset($_REQUEST['username']);
  	unset($_REQUEST['password']);
  	unset($_REQUEST['password_confirm']);
  }
      
  function test_create()
  {
  	$a =& new form_action();
  	
  	$this->assertNotNull($a);
  	$this->assertFalse($a->is_valid());
  }
  
  function test_is_first_time_no_name()
  {
  	$a =& new form_action();
  	
  	$this->assertTrue($a->is_first_time());
  	
  	$_REQUEST['submitted'] = true;
  	
  	$this->assertFalse($a->is_first_time());
  	
  	unset($_REQUEST['submitted']);
  	
  	$this->assertTrue($a->is_first_time());
  }
  
  function test_is_first_time_with_name()
  {	
  	$a =& new form_action('test1');
  	
  	$this->assertTrue($a->is_first_time());
  	
  	$_REQUEST['test1']['submitted'] = true;
  	
  	$this->assertFalse($a->is_first_time());
  	
  	unset($_REQUEST['test1']['submitted']);
  	
  	$this->assertTrue($a->is_first_time());
  }
  
  function test_form_action_validate_no_name()
  {
  	$dataspace =& dataspace :: instance();
  	
  	$dataspace->import(array('username' => 'vasa', 'password' => 'yo', 'password_confirm' => 'yo'));
  	
  	$a1 =& new form_action1();
  	
  	$this->assertFalse($a1->validate());
  	
  	$dataspace->set('password', 'yoyoyoyo');
  	$dataspace->set('password_confirm', 'yoyoyoyo');
  	    	
  	$a2 =& new form_action1();
  	
  	$this->assertTrue($a2->validate());
  }
  
  function test_form_action_validate_with_name()
  {
  	$dataspace =& dataspace :: instance('test1');
  	
  	$dataspace->import(array('username' => 'vasa', 'password' => 'yo', 'password_confirm' => 'yo'));

  	$a1 =& new form_action1('test1');
  	
  	$this->assertFalse($a1->validate());

  	$dataspace->set('password', 'yoyoyoyo');
  	$dataspace->set('password_confirm', 'yoyoyoyo');
  	    	
  	$a2 =& new form_action1('test1');
  	
  	$this->assertTrue($a2->validate());
  }
  
 	function test_perform_no_name()
  {
  	$_REQUEST['username'] = 'vasa';
  	$_REQUEST['password'] = 'yo';
  	$_REQUEST['password_confirm'] = 'yo';

  	$a1 =& new form_action1();
  	
  	debug_mock :: expect_write_error('validation failed');
  	
  	$this->assertFalse($a1->perform());
  	
  	$_REQUEST['submitted'] = true;
  	
  	debug_mock :: expect_write_error('validation failed');
  	
  	$this->assertFalse($a1->perform());
  	
  	$_REQUEST['password'] = 'yoyoyoyo';
  	$_REQUEST['password_confirm'] = 'yoyoyoyo';
  	
  	$this->assertFalse($a1->perform(), 'validation occurs only once');
  	
  	$a2 =& new form_action1();
  	
  	$this->assertTrue($a2->perform());
  }
  
  
  function test_perform_with_name()
  {
  	$_REQUEST['test1']['username'] = 'vasa';
  	$_REQUEST['test1']['password'] = 'yo';
  	$_REQUEST['test1']['password_confirm'] = 'yo';

  	$a1 =& new form_action1('test1');
  	
  	debug_mock :: expect_write_error('validation failed');
  	
  	$this->assertFalse($a1->perform());
  	
  	$_REQUEST['test1']['submitted'] = true;
  	
  	debug_mock :: expect_write_error('validation failed');
  	
  	$this->assertFalse($a1->perform());
  	
  	$_REQUEST['test1']['password'] = 'yoyoyoyo';
  	$_REQUEST['test1']['password_confirm'] = 'yoyoyoyo';
  	
  	$this->assertFalse($a1->perform(), 'validation occurs only once');
  	
  	$a2 =& new form_action1('test1');
  	
  	$this->assertTrue($a2->perform());
  }
}

?>