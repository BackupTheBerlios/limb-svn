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
require_once(LIMB_DIR . '/core/services/Service.class.php');

class ServiceTest extends LimbTestCase
{
  function ServiceTest()
  {
    parent :: LimbTestCase('service tests');
  }

  function tearDown()
  {
    clearTestingIni();
  }

  function testGetActionsList()
  {
    registerTestingIni(
      'test.service.ini',
      '
      [action1]
      some_properties
      [action2]
      some_properties
      '
    );

    $service = new Service('test');
    $this->assertEqual(array('action1', 'action2'),
                       $service->getActionsList());
  }

  function testActionExists()
  {
    registerTestingIni(
      'test.service.ini',
      '
      [action1]
      some_properties
      [action2]
      some_properties
      '
    );

    $service = new Service('test');
    $this->assertTrue($service->actionExists('action1'));
    $this->assertFalse($service->actionExists('no_such_action'));
  }

  function testSetCurrentAction()
  {
    $service = new Service('test');
    $this->assertFalse($service->getCurrentAction());

    $service->setCurrentAction($action = 'test action');

    $this->assertEqual($service->getCurrentAction(),
                       $action);
  }

  function testGetActionProps()
  {
    registerTestingIni(
      'test.service.ini',
      '
      [action1]
      p = 1
      '
    );

    $service = new Service('test');
    $this->assertEqual($service->getActionProperties('action1'),
                      array('p' => 1));

    $this->assertEqual($service->getActionProperties('no-such-action'),
                       array());
  }

  function testGetDefaultAction()
  {
    registerTestingIni(
      'test.service.ini',
      '
      default_action = admin_display

      [admin_display]
      some_properties
      '
    );

    $service = new Service('test');
    $this->assertTrue($service->getDefaultAction(), 'admin_display');
  }
}

?>