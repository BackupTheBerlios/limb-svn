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
require_once(LIMB_DIR . '/core/services/Service.class.php');
require_once(LIMB_DIR . '/core/commands/Command.interface.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

Mock :: generate('Request');
Mock :: generate('Service');
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
  var $service;
  var $request;
  var $toolkit;

  function ServiceControllerTest()
  {
    parent :: LimbTestCase('site object controller test');
  }

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->service = new MockService($this);

    $this->toolkit = new MockLimbBaseToolkit($this);
    $this->toolkit->setReturnReference('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->request->tally();
    $this->service->tally();

    Limb :: restoreToolkit();
  }

  function testGetRequestedActionOk()
  {
    $controller = new ServiceController($this->service);

    $this->request->setReturnValue('get', $action = 'test_action', array('action'));

    $this->service->expectOnce('actionExists', array($action));
    $this->service->setReturnValue('actionExists', true, array($action));

    $this->assertEqual($controller->getRequestedAction(), 'test_action');
  }

  function testDefaultGetAction()
  {
    $controller = new ServiceController($this->service);

    $this->request->setReturnValue('get', null, array('action'));

    $this->service->expectOnce('getDefaultAction');
    $this->service->setReturnValue('getDefaultAction', $action = 'display');

    $this->service->expectOnce('actionExists', array($action));
    $this->service->setReturnValue('actionExists', true, array($action));

    $this->assertEqual($controller->getRequestedAction(), $action);
  }

  function testGetNoSuchAction()
  {
    $controller = new ServiceController($this->service);

    $this->request->setReturnValue('get', 'noSuchAction', array('action'));
    $this->service->setReturnValue('actionExists', false, array('no_such_action'));

    $this->assertNull($controller->getRequestedAction());
  }

  function testProcess()
  {
    $controller = new ServiceController($this->service);

    $this->request->setReturnValue('get', $action = 'test_action', array('action'));

    $this->service->setReturnValue('actionExists', true, array($action));
    $this->service->expectOnce('getActionCommand', array($action));

    $command = new MockCommand($this);
    $this->service->setReturnReference('getActionCommand', $command);

    $command->expectOnce('perform');

    $controller->process();

    $command->tally();
  }

  function testProcessNoActionFind()
  {
    $controller = new ServiceController($this->service);

    $this->request->setReturnValue('get', $action = 'noSuchAction', array('action'));

    $this->service->setReturnValue('actionExists', false, array($action));
    $this->service->expectNever('getActionCommand');

    $controller->process($this->request);
    $this->assertTrue(catch('Exception', $e));
  }
}
?>
