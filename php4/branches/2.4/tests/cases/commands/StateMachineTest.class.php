<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/StateMachine.class.php');
require_once(LIMB_DIR . '/core/commands/Command.interface.php');

Mock :: generate('Command');

class StateMachineTest extends LimbTestCase
{
  var $state_machine;

  function StateMachineTest()
  {
    parent :: LimbTestCase('state machine test');
  }

  function setUp()
  {
    $this->state_machine = new StateMachine();
  }

  function tearDown()
  {
    $this->state_machine->reset();
  }

  function testRunNoStatesOk()
  {
    $this->state_machine->run();
  }

  function testSimpleFlow()
  {
    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'some_status');
    $this->state_machine->registerState('initial', $command1, array('some_status' => 'next_state'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', 'some_nonexistent_status');
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

  function testRunFromInitialState()
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

  function testRegisterStateTwice()
  {
    $command = new MockCommand($this);

    $this->state_machine->registerState('some_state', $command);
    $this->assertFalse(catch('Exception', $e));
    $this->state_machine->registerState('some_state', $command);
    $this->assertTrue(catch('Exception', $e));
  }

  function testSeveralStatusesFlow()
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

  function testStateByDefault()
  {
    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'some_status');
    $this->state_machine->registerState('initial', $command1, array(STATE_MACHINE_BY_DEFAULT => 'next_state1'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', 'some_other_status');
    $this->state_machine->registerState('next_state1', $command2, array(STATE_MACHINE_BY_DEFAULT => 'next_state2'));

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

  function testStateWithBrokenTransitions()
  {
    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'some_status');
    $this->state_machine->registerState('initial', $command1, array('some_status' => 'no_such_state'));

    $command2 = new MockCommand($this);
    $this->state_machine->registerState('next_state', $command2);

    $this->state_machine->run();
    $this->assertTrue(catch('Exception', $e));
  }

  function testCatchCircularFlow()
  {
  }
}

?>