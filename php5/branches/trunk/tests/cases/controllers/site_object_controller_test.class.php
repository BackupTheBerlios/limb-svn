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
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');

Mock :: generate('StateMachine');
Mock :: generate('request');
Mock :: generate('site_object_behaviour');

class site_object_behaviour_controller_test_version extends site_object_behaviour
{
  public function define_test_action($state_machine){}  
}

Mock :: generatePartial
(
  'site_object_behaviour_controller_test_version',
  'site_object_behaviour_mock',
  array('define_test_action')
); 

Mock :: generatePartial
(
  'site_object_controller',
  'site_object_controller_mock',
  array('_get_state_machine',
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

class site_object_controller_perform_test_version extends site_object_controller
{
  protected function _perform_action($request)
  {
    throw new LimbException('catch me!');
  }
}

Mock :: generatePartial
(
  'site_object_controller_perform_test_version',
  'site_object_controller_mock3',
  array('_start_transaction',
        '_commit_transaction',
        '_rollback_transaction')
);


class site_object_controller_test extends LimbTestCase 
{ 
	var $site_object_controller;
  var $state_machine;
  var $behaviour;
	var $request;
	
  function setUp()
  {
  	$this->request = new Mockrequest($this);
    $this->state_machine = new MockStateMachine($this);
    $this->behaviour = new Mocksite_object_behaviour($this);
  	
  	$this->controller = new site_object_controller_mock($this);
  	$this->controller->__construct($this->behaviour);
  }
  
  function tearDown()
  {
		$this->controller->tally();
		$this->request->tally();
    $this->state_machine->tally();
    $this->behaviour->tally();
  }
        
  function test_get_requested_action_ok()
  { 
  	$this->request->setReturnValue('get', $action = 'test_action', array('action'));
    
    $this->behaviour->expectOnce('action_exists', array($action));
    $this->behaviour->setReturnValue('action_exists', true, array($action));
        
  	$this->assertEqual($this->controller->get_requested_action($this->request), 'test_action');
  }
  
  function test_default_get_action()
  {
    $this->request->setReturnValue('get', null, array('action'));

    $this->behaviour->expectOnce('get_default_action');
    $this->behaviour->setReturnValue('get_default_action', $action = 'display');
    
    $this->behaviour->expectOnce('action_exists', array($action));
    $this->behaviour->setReturnValue('action_exists', true, array($action));    
    
  	$this->assertEqual($this->controller->get_requested_action($this->request), $action);
  }
    
  function test_get_no_such_action()
  {
  	$this->request->setReturnValue('get', 'no_such_action', array('action'));
    $this->behaviour->setReturnValue('action_exists', false, array('no_such_action'));    
    
    $this->assertNull($this->controller->get_requested_action($this->request));
  }

  function test_process()
  { 
    $behaviour = new site_object_behaviour_mock($this);
    
  	$controller = new site_object_controller_mock($this);
  	$controller->__construct($behaviour);
        
    $this->request->setReturnValue('get', 'test_action', array('action'));
    
    $controller->expectOnce('_get_state_machine');
    $controller->setReturnValue('_get_state_machine', $this->state_machine);
    
    $behaviour->expectOnce('define_test_action', 
                                 array(new IsAExpectation('MockStateMachine')));
        
    $this->state_machine->expectOnce('run');
  	$controller->process($this->request);
    
    $controller->tally();
    $behaviour->tally();
  }

  function test_process_no_action_find()
  { 
    $behaviour = new site_object_behaviour_mock($this);
    
  	$controller = new site_object_controller_mock($this);
  	$controller->__construct($behaviour);
        
    $this->request->setReturnValue('get', 'no_such_action', array('action'));
    
    $controller->expectNever('_get_state_machine');
    
    try
    {
      $controller->process($this->request);
      $this->assertTrue(false);
    }  
    catch(LimbException $e)
    {
    }
    
    $controller->tally();
    $behaviour->tally();
  }
  
  function test_transaction_calls()
  {
    $controller = new site_object_controller_mock2($this);
    
    $controller->expectOnce('_start_transaction');
    $controller->expectOnce('_commit_transaction');
    $controller->process($this->request);
    
    $controller->tally();
  }
 
  function test_transaction_rollback()
  {
    $controller = new site_object_controller_mock3($this);
    
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
