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
require_once(LIMB_DIR . '/class/core/commands/state_machine.class.php');  
require_once(LIMB_DIR . '/class/core/request/request.class.php');  
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');

Mock :: generate('StateMachine');
Mock :: generate('request');

Mock :: generatePartial
(
  'site_object_controller',
  'site_object_controller_mock',
  array('get_actions_definitions', 
        '_get_state_machine', 
        '_define_action_test',
        '_start_transaction',
        '_commit_transaction',
        '_rollback_transaction')
); 

Mock :: generatePartial
(
  'site_object_controller',
  'site_object_controller_mock2',
  array('_perform_action',
        '_start_transaction',
        '_commit_transaction',
        '_rollback_transaction')
); 

class site_object_controller_mock3 extends site_object_controller_mock2
{
  //please note, it's public now!!!
  public function _perform_action($request)
  {
    throw new LimbException('catch me');
  }
}

class site_object_controller_test extends LimbTestCase 
{ 
	var $site_object_controller;
  var $state_machine;
	var $request;
	
	var $actions_definition_test = array(
  		'display' => array( //default action
  		),
			'action_test' => array(
				'property1' => 1,
        'property2' => 2,
			)
		); 
	 	  
  function setUp()
  {
  	$this->request = new Mockrequest($this);
    $this->state_machine = new MockStateMachine($this);
  	
  	$this->controller = new site_object_controller_mock($this);
  	$this->controller->setReturnValue('get_actions_definitions', $this->actions_definition_test);
  	$this->controller->__construct();
  }
  
  function tearDown()
  {
		$this->controller->tally();
		$this->request->tally();
    $this->state_machine->tally();
  }
      
  function test_action_exists()
  {
  	$this->assertTrue($this->controller->action_exists('action_test'));
  	$this->assertFalse($this->controller->action_exists('no_such_action_test'));
  }
  
  function test_get_action()
  { 
  	$this->request->setReturnValue('get', 'action_test', array('action'));
  
  	$this->assertEqual($this->controller->get_action($this->request), 'action_test');
  }
  
  function test_default_get_action()
  {
  	$this->assertEqual($this->controller->get_action($this->request), 'display');
  }
  
  
  function test_get_no_such_action()
  {
  	$this->request->setReturnValue('get', 'no_such_action', array('action'));

  	try
  	{
  	  $action =& $this->controller->get_action($this->request);
  	  $this->assertTrue(false);
  	} 
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'action not found');
  	}
  }

  function test_process_action_not_defined()
  { 
    $this->request->setReturnValue('get', 'no_such_action', array('action'));
    
    $this->controller->expectNever('_get_state_machine');
    
    try
    {
      $this->controller->process($this->request);
      $this->assertTrue(false);
    }
    catch(LimbException $e)
    {
    }
  }

  function test_process()
  { 
    $this->request->setReturnValue('get', 'action_test', array('action'));
    
    $this->controller->expectOnce('_get_state_machine');
    $this->controller->setReturnValue('_get_state_machine', $this->state_machine);
    
    $this->controller->expectOnce('_define_action_test', 
                                              array(new IsAExpectation('MockStateMachine')));
    
    $this->state_machine->expectOnce('run');
  	$this->controller->process($this->request);
  }
  
  function test_transaction_calls()
  {
    $controller = new site_object_controller_mock2($this);
    $controller->__construct();
    
    $controller->expectOnce('_start_transaction');
    $controller->expectOnce('_commit_transaction');
    
    $controller->process($this->request);
    
    $controller->tally();
  }
  
  function test_transaction_rollback()
  {
    $controller = new site_object_controller_mock3($this);
    $controller->__construct();
    
    $controller->expectOnce('_start_transaction');
    $controller->expectOnce('_rollback_transaction');
    
    try
    {
      $controller->process($this->request);
      $this->assertTrue(false); 
    }
    catch(LimbException $e)
    {
    }
    
    $controller->tally();
  }  
}
?>
