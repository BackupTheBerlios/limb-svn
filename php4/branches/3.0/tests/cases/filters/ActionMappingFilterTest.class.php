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
require_once(LIMB_DIR . '/core/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/core/filters/ActionMappingFilter.class.php');
require_once(LIMB_DIR . '/core/services/Service.class.php');

Mock :: generate('FilterChain');
Mock :: generate('Service');

class ActionMappingFilterTest extends LimbTestCase
{
  function ActionMappingFilterTest()
  {
    parent :: LimbTestCase('action mapping filter test');
  }

  function setUp()
  {
    Limb :: saveToolkit();

  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testRunOkActionFound()
  {
    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $request->set('action', $action = 'whatever');

    $response = $toolkit->getResponse();

    $service =& new MockService($this);
    $service->expectOnce('actionExists', array($action));
    $service->setReturnValue('actionExists', true);
    $service->expectOnce('setCurrentAction', array($action));

    $toolkit->setRequestResolver($service);

    $filter = new ActionMappingFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $filter->run($fc, $request, $response);

    $fc->tally();
    $service->tally();
  }

  function testRunOkDefaultAction()
  {
    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $response = $toolkit->getResponse();

    $service =& new MockService($this);
    $service->expectOnce('getDefaultAction');
    $service->setReturnValue('getDefaultAction', $action = 'whatever');
    $service->expectOnce('setCurrentAction', array($action));

    $toolkit->setRequestResolver($service);

    $filter = new ActionMappingFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $filter->run($fc, $request, $response);

    $fc->tally();
    $service->tally();
  }

  function testRunOkActionNotFound()
  {
    registerTestingIni(
      '404.service.ini',
      '
      default_action = display

      [display]
      some_properties
      '
    );

    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $request->set('action', $action = 'no_such_action');
    $response = $toolkit->getResponse();

    $service =& new MockService($this);
    $service->expectOnce('actionExists', array($action));
    $service->setReturnValue('actionExists', false);

    $service_404 = new Service('404');

    $toolkit->setRequestResolver($service);

    $filter = new ActionMappingFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $filter->run($fc, $request, $response);

    $expected_service = $toolkit->getRequestResolver();
    $this->assertEqual($expected_service->getName(), $service_404->getName());
    $this->assertEqual($expected_service->getCurrentAction(),
                       $expected_service->getDefaultAction());

    $fc->tally();
    $service->tally();

    clearTestingIni();
  }
}

?>
