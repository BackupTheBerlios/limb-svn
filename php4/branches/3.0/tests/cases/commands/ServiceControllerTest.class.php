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
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/ServiceController.class.php');
require_once(LIMB_DIR . '/core/behaviours/Behaviour.class.php');
require_once(LIMB_DIR . '/core/commands/Command.interface.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

Mock :: generate('Request');
Mock :: generate('Behaviour');
Mock :: generate('Command');
Mock :: generate('LimbBaseToolkit');

class ServiceControllerPerformTestVersion extends ServiceController
{
  function _performAction(&$request)
  {
    return throw(new LimbException('catch me!'));
  }
}

Mock :: generatePartial
(
  'ServiceControllerPerformTestVersion',
  'ServiceControllerMock3',
  array('_startTransaction',
        '_commitTransaction',
        '_rollbackTransaction')
);


class ServiceControllerTest extends LimbTestCase
{
  var $object_controller;
  var $behaviour;
  var $request;
  var $toolkit;

  function ServiceControllerTest()
  {
    parent :: LimbTestCase('site object controller test');
  }

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->behaviour = new MockBehaviour($this);

    $this->toolkit = new MockLimbBaseToolkit($this);
    $this->toolkit->setReturnReference('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->request->tally();
    $this->behaviour->tally();

    Limb :: restoreToolkit();
  }

  function testGetRequestedActionOk()
  {
    $controller = new ServiceController($this->behaviour);

    $this->request->setReturnValue('get', $action = 'test_action', array('action'));

    $this->behaviour->expectOnce('actionExists', array($action));
    $this->behaviour->setReturnValue('actionExists', true, array($action));

    $this->assertEqual($controller->getRequestedAction(), 'test_action');
  }

  function testDefaultGetAction()
  {
    $controller = new ServiceController($this->behaviour);

    $this->request->setReturnValue('get', null, array('action'));

    $this->behaviour->expectOnce('getDefaultAction');
    $this->behaviour->setReturnValue('getDefaultAction', $action = 'display');

    $this->behaviour->expectOnce('actionExists', array($action));
    $this->behaviour->setReturnValue('actionExists', true, array($action));

    $this->assertEqual($controller->getRequestedAction(), $action);
  }

  function testGetNoSuchAction()
  {
    $controller = new ServiceController($this->behaviour);

    $this->request->setReturnValue('get', 'noSuchAction', array('action'));
    $this->behaviour->setReturnValue('actionExists', false, array('no_such_action'));

    $this->assertNull($controller->getRequestedAction());
  }

  function testProcess()
  {
    $controller = new ServiceController($this->behaviour);

    $this->request->setReturnValue('get', $action = 'test_action', array('action'));

    $this->behaviour->setReturnValue('actionExists', true, array($action));
    $this->behaviour->expectOnce('getActionCommand', array($action));

    $command = new MockCommand($this);
    $this->behaviour->setReturnReference('getActionCommand', $command);

    $command->expectOnce('perform');

    $controller->process();

    $command->tally();
  }

  function testProcessNoActionFind()
  {
    $controller = new ServiceController($this->behaviour);

    $this->request->setReturnValue('get', $action = 'noSuchAction', array('action'));

    $this->behaviour->setReturnValue('actionExists', false, array($action));
    $this->behaviour->expectNever('getActionCommand');

    $controller->process($this->request);
    $this->assertTrue(catch('Exception', $e));
  }
}
?>
