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
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObjectController.class.php');
require_once(LIMB_DIR . '/core/behaviours/Behaviour.class.php');

Mock :: generate('StateMachine');
Mock :: generate('Request');
Mock :: generate('Behaviour');

class BehaviourControllerTestVersion extends Behaviour
{
  function defineTestAction(&$state_machine){}
}

Mock :: generatePartial
(
  'BehaviourControllerTestVersion',
  'BehaviourMock',
  array('defineTestAction')
);

Mock :: generatePartial
(
  'SiteObjectController',
  'SiteObjectControllerMock',
  array('_getStateMachine')
);

class SiteObjectControllerPerformTestVersion extends SiteObjectController
{
  function _performAction(&$request)
  {
    return throw(new LimbException('catch me!'));
  }
}

Mock :: generatePartial
(
  'SiteObjectControllerPerformTestVersion',
  'SiteObjectControllerMock3',
  array('_startTransaction',
        '_commitTransaction',
        '_rollbackTransaction')
);


class SiteObjectControllerTest extends LimbTestCase
{
  var $site_object_controller;
  var $state_machine;
  var $behaviour;
  var $request;

  function SiteObjectControllerTest()
  {
    parent :: LimbTestCase('site object controller test');
  }

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->state_machine = new MockStateMachine($this);
    $this->behaviour = new MockBehaviour($this);

    $this->controller = new SiteObjectControllerMock($this);
    $this->controller->SiteObjectController($this->behaviour);
  }

  function tearDown()
  {
    $this->controller->tally();
    $this->request->tally();
    $this->state_machine->tally();
    $this->behaviour->tally();
  }

  function testGetRequestedActionOk()
  {
    $this->request->setReturnValue('get', $action = 'test_action', array('action'));

    $this->behaviour->expectOnce('actionExists', array($action));
    $this->behaviour->setReturnValue('actionExists', true, array($action));

    $this->assertEqual($this->controller->getRequestedAction($this->request), 'test_action');
  }

  function testDefaultGetAction()
  {
    $this->request->setReturnValue('get', null, array('action'));

    $this->behaviour->expectOnce('getDefaultAction');
    $this->behaviour->setReturnValue('getDefaultAction', $action = 'display');

    $this->behaviour->expectOnce('actionExists', array($action));
    $this->behaviour->setReturnValue('actionExists', true, array($action));

    $this->assertEqual($this->controller->getRequestedAction($this->request), $action);
  }

  function testGetNoSuchAction()
  {
    $this->request->setReturnValue('get', 'noSuchAction', array('action'));
    $this->behaviour->setReturnValue('actionExists', false, array('no_such_action'));

    $this->assertNull($this->controller->getRequestedAction($this->request));
  }

  function testProcess()
  {
    $behaviour = new BehaviourMock($this);

    $controller = new SiteObjectControllerMock($this);
    $controller->SiteObjectController($behaviour);

    $this->request->setReturnValue('get', 'TestAction', array('action'));

    $controller->expectOnce('_getStateMachine');
    $controller->setReturnReference('_getStateMachine', $this->state_machine);

    $behaviour->expectOnce('defineTestAction',
                                 array(new IsAExpectation('MockStateMachine')));

    $this->state_machine->expectOnce('run');
    $controller->process($this->request);

    $controller->tally();
    $behaviour->tally();
  }

  function testProcessNoActionFind()
  {
    $behaviour = new BehaviourMock($this);

    $controller = new SiteObjectControllerMock($this);
    $controller->SiteObjectController($behaviour);

    $this->request->setReturnValue('get', 'noSuchAction', array('action'));

    $controller->expectNever('_getStateMachine');

    $controller->process($this->request);
    $this->assertTrue(catch('Exception', $e));

    $controller->tally();
    $behaviour->tally();
  }
}
?>
