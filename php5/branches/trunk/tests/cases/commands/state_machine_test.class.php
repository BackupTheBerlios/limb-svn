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
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');

Mock::generate('Command');

class state_machine_test extends LimbTestCase 
{
  protected $state_machine;
  
  function setUp()
  {
    $this->state_machine = new StateMachine();
  }
  
  function tearDown()
  {
    $this->state_machine->reset();
  }

  function test_run_no_states_ok()
  {
    $this->state_machine->run();
  }
      
  function test_simple_flow()
  {
    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'some_status');
  	$this->state_machine->registerState('initial', $command1, array('some_status' => 'next_state'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', 'some_other_status');
  	$this->state_machine->registerState('next_state', $command2);

    $command3 = new MockCommand($this);
  	$this->state_machine->registerState('extra_state', $command3);
    
    $command1->expectOnce('perform'); 
    $command2->expectOnce('perform'); 
    $command3->expectNever('perform'); 
    
    $this->state_machine->run();
    
    $command1->tally();
    $command2->tally();
    $command3->tally();
  }
  
  function test_run_from_initial_state()
  {
    $command1 = new MockCommand($this);
  	$this->state_machine->registerState('initial', $command1, array('some_status' => 'next_state'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', 'some_other_status');
  	$this->state_machine->registerState('next_state', $command2, array('some_other_status' => 'extra_state'));

    $command3 = new MockCommand($this);
  	$this->state_machine->registerState('extra_state', $command3);
    
    $command1->expectNever('perform'); 
    $command2->expectOnce('perform'); 
    $command3->expectOnce('perform'); 
    
    $this->state_machine->setInitialState('next_state');
    $this->state_machine->run();
    
    $command1->tally();
    $command2->tally();
    $command3->tally();
  }
  
  function test_register_state_twice()
  {
    $command = new MockCommand($this);

    try
    {
      $this->state_machine->registerState('some_state', $command);
      $this->state_machine->registerState('some_state', $command);
      
      $this->assertTrue(false, 'Exception must be thrown here');
    }
    catch(LimbException $e)
    {
      $this->assertEqual($e->getAdditionalParams() , array('state_name' => 'some_state'));
    }
  }
  
  function test_several_statuses_flow()
  {
    $command1 = new MockCommand($this);
    $command1->setReturnValueAt(0, 'perform', 'some_status');
    $command1->setReturnValueAt(1, 'perform', 'some_other_status');
  	$this->state_machine->registerState('initial', $command1, 
                                        array('some_status' => 'variant1_state', 
                                              'some_other_status' => 'variant2_state'));

    $command2 = new MockCommand($this);
  	$this->state_machine->registerState('variant1_state', $command2);

    $command3 = new MockCommand($this);
  	$this->state_machine->registerState('variant2_state', $command3);

    $command1->expectCallCount('perform', 2); 
    $command2->expectOnce('perform'); 
    $command3->expectOnce('perform'); 
    
    $this->state_machine->run();
    $this->state_machine->run();
    
    $command1->tally();
    $command2->tally();
    $command3->tally();
  }    

  function test_state_by_default()
  {  
    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'some_status');
  	$this->state_machine->registerState('initial', $command1, array(StateMachine :: BY_DEFAULT => 'next_state1'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', 'some_other_status');
  	$this->state_machine->registerState('next_state1', $command2, array(StateMachine :: BY_DEFAULT => 'next_state2'));

    $command3 = new MockCommand($this);
    $command3->setReturnValue('perform', 'some_other_status_also');
  	$this->state_machine->registerState('next_state2', $command3);

    $command1->expectOnce('perform'); 
    $command2->expectOnce('perform');
    $command3->expectOnce('perform');
    
    $this->state_machine->run();

    $command1->tally();
    $command2->tally();
    $command3->tally();
  }  

  function test_state_with_broken_transitions()
  {
    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'some_status');
  	$this->state_machine->registerState('initial', $command1, array('some_status' => 'no_such_state'));

    $command2 = new MockCommand($this);
  	$this->state_machine->registerState('next_state', $command2);
    
    try
    {
      $this->state_machine->run();
      
      $this->assertTrue(false, 'Exception must be thrown here');
    }
    catch(LimbException $e)
    {
      $this->assertEqual($e->getAdditionalParams() , array('state_name' => 'no_such_state',
                                                           ));
    }
  }
  
  function test_catch_circular_flow()
  {
  }
  
}

?>