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

class form_action_stub extends form_action
{
	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('username'));
		$this->validator->add_rule(new required_rule('password'));
		$this->validator->add_rule(new required_rule('password_confirm'));
		$this->validator->add_rule(new size_range_rule('password', 6, 15));
		$this->validator->add_rule(new size_range_rule('password_confirm', 6, 15));
	}
	
	function _define_dataspace_name()
	{
	  return 'test1';
	}
	
	function _valid_perform()
	{
		return new response(RESPONSE_STATUS_FORM_SUBMITTED);
	}
	
	function _first_time_perform()
	{
		return new failed_response();
	}
}

Mock::generatePartial(
  'form_action_stub', 
  'form_action_test_version', 
  array('_define_dataspace_name')
);

class form_action_test extends UnitTestCase 
{
	var $debug = null;
	var $form_action = null;
		  	
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
  	
  	$this->form_action = new form_action_test_version($this);
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
  	
  	$this->form_action->tally();
  }
      
  function test_is_valid()
  {
  	$this->assertFalse($this->form_action->is_valid());
  }
  
  function test_is_first_time_no_name()
  {
  	$this->form_action->setReturnValue('_define_dataspace_name', '');
  	$this->form_action->form_action();
  	
  	$this->assertTrue($this->form_action->is_first_time());
  	
  	$_REQUEST['submitted'] = true;
  	
  	$this->assertFalse($this->form_action->is_first_time());
  	
  	unset($_REQUEST['submitted']);
  	
  	$this->assertTrue($this->form_action->is_first_time());
  }
  
  function test_is_first_time_with_name()
  {	
  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  	
  	$this->assertTrue($this->form_action->is_first_time(), '%s ' . __LINE__);
  	
  	$_REQUEST['test1']['submitted'] = true;
  	$this->assertFalse($this->form_action->is_first_time(), '%s ' . __LINE__);
  	
  	unset($_REQUEST['test1']['submitted']);
  	
  	$this->assertTrue($this->form_action->is_first_time(), '%s ' . __LINE__);
  }
  
  function test_form_action_validate_no_name()
  {
  	$this->form_action->setReturnValue('_define_dataspace_name', '');
  	$this->form_action->form_action();
  
  	$dataspace =& dataspace :: instance();
  	
  	$dataspace->import(array('username' => 'vasa', 'password' => 'yoyoyo', 'password_confirm' => 'yoyoyo'));
  	
  	$this->assertTrue($this->form_action->validate());  	
  }
  
  function test_form_action_validate_with_name()
  {
  	$dataspace =& dataspace :: instance('test1');
  	
  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  
  	$dataspace->import(array('username' => 'vasa', 'password' => 'yoyoyo', 'password_confirm' => 'yoyoyo'));
  	
  	$this->assertTrue($this->form_action->validate());  	
  }
  
  function test_double_validation_no_name()
  {
  	$this->form_action->setReturnValue('_define_dataspace_name', '');
  	$this->form_action->form_action();
  
  	$_REQUEST['username'] = 'vasa';
  	$_REQUEST['password'] = 'yo';
  	$_REQUEST['password_confirm'] = 'yo';
  	
  	$this->assertFalse($this->form_action->validate());  	

  	$_REQUEST['password'] = 'yoyoyoyo';
  	$_REQUEST['password_confirm'] = 'yoyoyoyo';

  	$this->assertFalse($this->form_action->validate(), 'validation occurs only once!');
  }
  
 	function test_perform_no_name_validation_fails()
  {
  	$_REQUEST['username'] = 'vasa';
  	$_REQUEST['password'] = 'yo';
  	$_REQUEST['password_confirm'] = 'yo';

  	$this->form_action->setReturnValue('_define_dataspace_name', '');
  	$this->form_action->form_action();
  	
  	$this->assertIsA($this->form_action->perform(), 'failed_response');
  	
  	$_REQUEST['submitted'] = true;
  	
  	debug_mock :: expect_write_error('validation failed');
  	
  	$this->assertIsA($this->form_action->perform(), 'not_valid_response');
  }
  
  function test_perform_no_name()
  {
  	$_REQUEST['username'] = 'vasa';
  	$_REQUEST['password'] = 'yoyoyo';
  	$_REQUEST['password_confirm'] = 'yoyoyo';
  	$_REQUEST['submitted'] = true;

  	$this->form_action->setReturnValue('_define_dataspace_name', '');
  	$this->form_action->form_action();
  	
  	$this->assertIsA($this->form_action->perform(), 'response');
  }

  function test_double_validation_with_name()
  {
  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  
  	$_REQUEST['test1']['username'] = 'vasa';
  	$_REQUEST['test1']['password'] = 'yo';
  	$_REQUEST['test1']['password_confirm'] = 'yo';
  	
  	$this->assertFalse($this->form_action->validate());  	

  	$_REQUEST['test1']['password'] = 'yoyoyoyo';
  	$_REQUEST['test1']['password_confirm'] = 'yoyoyoyo';

  	$this->assertFalse($this->form_action->validate(), 'validation occurs only once!');
  }
  
 	function test_perform_with_name_validation_fails()
  {
  	$_REQUEST['test1']['username'] = 'vasa';
  	$_REQUEST['test1']['password'] = 'yo';
  	$_REQUEST['test1']['password_confirm'] = 'yo';

  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  	
  	$this->assertIsA($this->form_action->perform(), 'failed_response');
  	
  	$_REQUEST['test1']['submitted'] = true;
  	
  	debug_mock :: expect_write_error('validation failed');
  	
  	$this->assertIsA($this->form_action->perform(), 'not_valid_response');
  }
  
  function test_perform_with_name()
  {
  	$_REQUEST['test1']['username'] = 'vasa';
  	$_REQUEST['test1']['password'] = 'yoyoyo';
  	$_REQUEST['test1']['password_confirm'] = 'yoyoyo';
  	$_REQUEST['test1']['submitted'] = true;

  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  	
  	$this->assertIsA($this->form_action->perform(), 'response');
  }
  
}

?>