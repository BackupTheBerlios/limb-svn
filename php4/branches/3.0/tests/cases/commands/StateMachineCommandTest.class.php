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

class TestCommandsFactory
{
  function performInitial(){}
  function performNextState(){}
  function performExtraState(){}
}

Mock :: generate('TestCommandsFactory', 'MockCommandsFactory');

class StateMachineCommandTest extends LimbTestCase
{
  var $state_machine;

  function StateMachineCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testRunNoStatesOk()
  {
    $factory = new MockCommandsFactory($this);
    $state_machine = new StateMachineCommand($factory);
    $this->assertNull($state_machine->perform($factory));
  }

  function testSimpleFlow()
  {
    $factory = new MockCommandsFactory($this);
    $state_machine = new StateMachineCommand($factory);

    $state_machine->registerState('Initial', array('foo' => 'NextState'));
    $state_machine->registerState('NextState');
    $state_machine->registerState('ExtraState');

    $factory->expectOnce('performInitial');
    $factory->setReturnValue('performInitial', 'foo');
    $factory->expectOnce('performNextState');
    $factory->setReturnValue('performNextState', 'bar');

    $factory->expectNever('performExtraState');

    $this->assertEqual($state_machine->perform(), 'bar');

    $factory->tally();
  }

  function testRunFromInitialState()
  {
    $factory = new MockCommandsFactory($this);
    $state_machine = new StateMachineCommand($factory);

    $state_machine->registerState('Initial', array('foo' => 'NextState'));
    $state_machine->registerState('NextState');
    $state_machine->registerState('ExtraState');

    $factory->expectNever('performInitial');
    $factory->expectOnce('performNextState');
    $factory->setReturnValue('performNextState', 'bar');
    $factory->expectNever('performExtraState');

    $state_machine->setInitialState('NextState');
    $this->assertEqual($state_machine->perform(), 'bar');

    $factory->tally();
  }

  function testRegisterStateTwice()
  {
    $factory = new MockCommandsFactory($this);
    $state_machine = new StateMachineCommand($factory);

    $state_machine->registerState('some_state');
    $this->assertFalse(catch_error('LimbException', $e));
    $state_machine->registerState('some_state');
    $this->assertTrue(catch_error('LimbException', $e));
  }

  function testSeveralStatusesFlow()
  {
    $factory = new MockCommandsFactory($this);
    $state_machine = new StateMachineCommand($factory);

    $state_machine->registerState('Initial', array('some_result' => 'NextState',
                                                   'some_other_result' => 'ExtraState'));
    $state_machine->registerState('NextState');
    $state_machine->registerState('ExtraState');

    $factory->expectOnce('performInitial');
    $factory->setReturnValueAt(0, 'performInitial', 'some_other_result');
    $factory->expectNever('performNextState');
    $factory->setReturnValue('performExtraState', 'bar');

    $factory->expectNever('performNextState');

    $this->assertEqual($state_machine->perform(), 'bar');

    $factory->tally();
  }

  function testStateByDefault()
  {
    $factory = new MockCommandsFactory($this);
    $state_machine = new StateMachineCommand($factory);

    $state_machine->registerState('Initial', array('some_result' => 'NextState',
                                                   STATE_MACHINE_BY_DEFAULT => 'ExtraState'));
    $state_machine->registerState('NextState');
    $state_machine->registerState('ExtraState');

    $factory->expectOnce('performInitial');
    $factory->setReturnValueAt(0, 'performInitial', 'some_other_result');
    $factory->expectNever('performNextState');
    $factory->setReturnValue('performExtraState', 'bar');

    $factory->expectNever('performNextState');

    $this->assertEqual($state_machine->perform(), 'bar');

    $factory->tally();
  }

  function testStateWithBrokenTransitions()
  {
    $factory = new MockCommandsFactory($this);
    $state_machine = new StateMachineCommand($factory);

    $state_machine->registerState('Initial', array('some_result' => 'NoSuchState'));
    $state_machine->registerState('NextState');
    $state_machine->registerState('ExtraState');

    $factory->expectOnce('performInitial');
    $factory->setReturnValueAt(0, 'performInitial', 'some_result');
    $factory->expectNever('performNextState');
    $factory->expectNever('performExtraState');
    $state_machine->perform();
    $this->assertTrue(catch_error('LimbException', $e));
  }

  function testGetStateHistory()
  {
    $factory = new MockCommandsFactory($this);
    $state_machine = new StateMachineCommand($factory);

    $state_machine->registerState('Initial', array('foo' => 'NextState'));
    $state_machine->registerState('NextState');
    $state_machine->registerState('ExtraState');

    $factory->expectOnce('performInitial');
    $factory->setReturnValue('performInitial', 'foo');
    $factory->expectOnce('performNextState');
    $factory->setReturnValue('performNextState', 'bar');

    $factory->expectNever('performExtraState');

    $this->assertEqual($state_machine->perform(), 'bar');

    $this->assertEqual($state_machine->getStateHistory(),
                       array(array('Initial' => 'foo'),
                             array('NextState' => 'bar')));

    $factory->tally();
  }

  function testGetStateHistoryAllCommandsInHistory()
  {
    $factory = new MockCommandsFactory($this);
    $state_machine = new StateMachineCommand($factory);

    $state_machine->registerState('Initial', array('foo' => 'NextState'));
    $state_machine->registerState('NextState', array('bar' => 'ExtraState'));
    $state_machine->registerState('ExtraState');

    $factory->expectOnce('performInitial');
    $factory->setReturnValue('performInitial', 'foo');
    $factory->expectOnce('performNextState');
    $factory->setReturnValue('performNextState', 'bar');
    $factory->expectOnce('performExtraState');
    $factory->setReturnValue('performExtraState', 'some_result');

    $this->assertEqual($state_machine->perform(), 'some_result');

    $this->assertEqual($state_machine->getStateHistory(),
                       array(array('Initial' => 'foo'),
                             array('NextState' => 'bar'),
                             array('ExtraState' => 'some_result')));

    $this->assertEqual($state_machine->getEndState(), array('ExtraState' => 'some_result'));

    $factory->tally();
  }
}

?>