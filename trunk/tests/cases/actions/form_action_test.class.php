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
require_once(LIMB_DIR . 'core/lib/util/dataspace_registry.class.php');

require_once(LIMB_DIR . 'core/request/request.class.php');
require_once(LIMB_DIR . 'core/request/response.class.php');

class form_action_stub extends form_action
{
	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('username'));
		$this->validator->add_rule(new required_rule('password'));
		$this->validator->add_rule(new size_range_rule('password', 6, 15));
	}	
}

Mock::generatePartial(
  'form_action_stub', 
  'form_action_test_version', 
  array('_define_dataspace_name', '_init_dataspace')
);

Mock::generate(
  'request'
);

Mock::generate(
  'response'
);

class form_action_test extends UnitTestCase 
{
	var $debug = null;
	var $form_action = null;
	var $request = null;
	var $response = null;
	var $dataspace = null;
		  	
  function setUp()
  {
  	debug_mock :: init($this);
  	
  	$this->dataspace =& dataspace_registry :: get('test1');
  	  	
  	$this->form_action = new form_action_test_version($this);

  	$this->request = new Mockrequest($this);
  	$this->response = new Mockresponse($this);
  }
  
  function tearDown()
  {
  	debug_mock :: tally();
  	
  	$this->dataspace->reset();
  	
  	$this->form_action->tally();
  	$this->request->tally();
  	$this->response->tally();
  	
  }
      
  function test_is_valid()
  {
  	$this->assertFalse($this->form_action->is_valid());
  }
    
  function test_is_first_time()
  {	
  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();

  	$this->request->expectOnce('get_attribute');
  	$this->request->setReturnValue('get_attribute', array(), array('test1'));

  	$this->assertTrue($this->form_action->is_first_time($this->request), '%s ' . __LINE__);
  }
  
  function test_is_first_time_false() 	
  {
  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();

  	$this->request->expectOnce('get_attribute');
  	$this->request->setReturnValue('get_attribute', array('submitted' => true), array('test1'));

  	$this->assertFalse($this->form_action->is_first_time($this->request), '%s ' . __LINE__);
  }
  
  function test_form_action_validate()
  {
  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  
  	$this->dataspace->import(array('username' => 'vasa', 'password' => 'yoyoyo'));
  	
  	$this->assertTrue($this->form_action->validate());  	
  }
  
  function test_double_validation()
  {
  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  
   	$this->dataspace->set('username', 'vasa');
   	$this->dataspace->set('password', 'yoyoyoyo');

  	$this->assertTrue($this->form_action->validate());  	

   	$this->dataspace->set('password', 'yo');

  	$this->assertTrue($this->form_action->validate(), 'validation occurs only once!');
  }

 	function test_perform_first_time()
  {
    $request_data = array(
      'username' => 'vasa',
      'password' => 'yoyoyo',
    );
    
  	$this->request->setReturnValue('get_attribute', $request_data, array('test1'));

  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  	
  	$this->form_action->expectOnce('_init_dataspace', array(new IsAexpectation('Mockrequest'))); 	
  	$this->request->expectOnce('set_status', array(REQUEST_STATUS_FORM_DISPLAYED));
  	
  	$this->form_action->perform($this->request, $this->response);  	
  }
    
 	function test_perform_validation_failed()
  {
    $request_data = array(
      'username' => 'vasa',
      'password' => 'yo',
      'submitted' => 1
    );
    
  	$this->request->setReturnValue('get_attribute', $request_data, array('test1'));

  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  	
  	$this->request->expectOnce('set_status', array(REQUEST_STATUS_FORM_NOT_VALID));
  	$this->form_action->perform($this->request, $this->response);  	
  }

  function test_perform_transfer_dataspace()
  {
    $request_data = array(
      'username' => 'vasa',
      'password' => 'yoyoyo',
      'submitted' => 1
    );

  	$this->request->expectCallCount('get_attribute', 2);
  	$this->request->setReturnValue('get_attribute', $request_data, array('test1'));

  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();

  	$this->form_action->perform($this->request, $this->response);
  	
  	$this->assertEqual($this->dataspace->export(), $request_data);
  }
  
 	function test_perform_validation_ok()
  {
    $request_data = array(
      'username' => 'vasa',
      'password' => 'yoyoyo',
      'submitted' => 1
    );
    
  	$this->request->setReturnValue('get_attribute', $request_data, array('test1'));

  	$this->form_action->setReturnValue('_define_dataspace_name', 'test1');
  	$this->form_action->form_action();
  	
  	$this->request->expectOnce('set_status', array(REQUEST_STATUS_FORM_SUBMITTED));
  	$this->form_action->perform($this->request, $this->response);  	
  }
}

?>