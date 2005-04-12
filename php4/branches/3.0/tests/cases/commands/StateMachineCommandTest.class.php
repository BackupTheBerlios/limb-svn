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
require_once(LIMB_DIR . '/core/commands/StateMachineCommand.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/commands/Command.interface.php');

Mock :: generate('Command');

class StateMachineCommandTest extends LimbTestCase
{
  var $state_machine;

  function StateMachineCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->state_machine = new StateMachineCommand();
  }

  function tearDown()
  {
    $this->state_machine->reset();
  }

  function testRunNoStatesOk()
  {
    $this->state_machine->perform(new DataSpace());
  }

  function testSimpleFlow()
  {
    $context = new DataSpace();

    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', $result1 = 'some_status');
    $this->state_machine->registerState('initial', $command1, array($result1 => 'next_state'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', $result2 = 'final_status');
    $this->state_machine->registerState('next_state', $command2);

    $command3 = new MockCommand($this);
    $this->state_machine->registerState('extra_state', $command3);

    $command1->expectOnce('perform', array($context));
    $command2->expectOnce('perform', array($context));
    $command3->expectNever('perform', array($context));

    $this->assertEqual($this->state_machine->perform($context), $result2);

    $command1->tally();
    $command2->tally();
    $command3->tally();
  }

  function testRunFromInitialState()
  {
    $context = new DataSpace();

    $command1 = new MockCommand($this);
    $this->state_machine->registerState('initial', $command1, array('some_status' => 'next_state'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', $result1 = 'some_other_status');
    $this->state_machine->registerState('next_state', $command2, array('some_other_status' => 'extra_state'));

    $command3 = new MockCommand($this);
    $command3->setReturnValue('perform', $result2 = 'final_status');
    $this->state_machine->registerState('extra_state', $command3);

    $command1->expectNever('perform', array($context));
    $command2->expectOnce('perform', array($context));
    $command3->expectOnce('perform', array($context));

    $this->state_machine->setInitialState('next_state');
    $this->assertEqual($this->state_machine->perform($context), $result2);

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
    $context = new DataSpace();

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
    $command2->expectOnce('perform', array($context));
    $command3->expectOnce('perform', array($context));

    $this->state_machine->perform($context);
    $this->state_machine->perform($context);

    $command1->tally();
    $command2->tally();
    $command3->tally();
  }

  function testStateByDefault()
  {
    $context = new DataSpace();

    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'some_status');
    $this->state_machine->registerState('initial', $command1, array(STATE_MACHINE_BY_DEFAULT => 'next_state1'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', 'some_other_status');
    $this->state_machine->registerState('next_state1', $command2, array(STATE_MACHINE_BY_DEFAULT => 'next_state2'));

    $command3 = new MockCommand($this);
    $command3->setReturnValue('perform', 'some_other_status_also');
    $this->state_machine->registerState('next_state2', $command3);

    $command1->expectOnce('perform', array($context));
    $command2->expectOnce('perform', array($context));
    $command3->expectOnce('perform', array($context));

    $this->state_machine->perform($context);

    $command1->tally();
    $command2->tally();
    $command3->tally();
  }

  function testStateWithBrokenTransitions()
  {
    $context = new DataSpace();

    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'some_status');
    $this->state_machine->registerState('initial', $command1, array('some_status' => 'no_such_state'));

    $command2 = new MockCommand($this);
    $this->state_machine->registerState('next_state', $command2);

    $this->state_machine->perform($context);
    $this->assertTrue(catch('Exception', $e));
  }

  function testGetStateHistory()
  {
    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'status1');
    $this->state_machine->registerState('initial', $command1, array('status1' => 'state2'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', 'status2');
    $this->state_machine->registerState('state2', $command2, array('status2' => 'no_such_state'));

    $command3 = new MockCommand($this);
    $this->state_machine->registerState('state3', $command3);

    $this->state_machine->perform(new DataSpace());
    $this->assertTrue(catch('Exception', $e));//because of the 'no_such_state'

    $this->assertEqual($this->state_machine->getStateHistory(),
                       array(array('initial' => 'status1'),
                             array('state2' => 'status2')));

    $this->assertEqual($this->state_machine->getEndState(), array('state2' => 'status2'));
  }

  function testGetStateHistoryAllCommandsInHistory()
  {
    $command1 = new MockCommand($this);
    $command1->setReturnValue('perform', 'status1');
    $this->state_machine->registerState('initial', $command1, array('status1' => 'state2'));

    $command2 = new MockCommand($this);
    $command2->setReturnValue('perform', 'status2');
    $this->state_machine->registerState('state2', $command2, array('status2' => 'state3'));

    $command3 = new MockCommand($this);
    $command3->setReturnValue('perform', 'whatever');
    $this->state_machine->registerState('state3', $command3);

    $this->state_machine->perform(new DataSpace());
    $this->assertEqual($this->state_machine->getStateHistory(),
                       array(array('initial' => 'status1'),
                             array('state2' => 'status2'),
                             array('state3' => 'whatever')));

    $this->assertEqual($this->state_machine->getEndState(), array('state3' => 'whatever'));
  }

  function testCatchCircularFlow(){}
}

?>