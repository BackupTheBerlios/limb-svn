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
require_once(LIMB_DIR . '/core/filters/ServiceActionMappingFilter.class.php');
require_once(LIMB_DIR . '/core/services/Service.class.php');
require_once(LIMB_DIR . '/core/request_resolvers/RequestResolver.interface.php');

Mock :: generate('FilterChain');
Mock :: generate('Service');
Mock :: generate('RequestResolver');

class ServiceActionMappingFilterTest extends LimbTestCase
{
  var $action_resolver;
  var $service_resolver;
  var $service;
  var $fc;

  function ServiceActionMappingFilterTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    Limb :: saveToolkit();

    $toolkit =& Limb :: toolkit();

    $this->action_resolver = new MockRequestResolver($this);
    $this->service_resolver = new MockRequestResolver($this);
    $this->service = new MockService($this);

    $toolkit->setRequestResolver('action', $this->action_resolver);
    $toolkit->setRequestResolver('service', $this->service_resolver);

    $this->fc = new MockFilterChain($this);
  }

  function tearDown()
  {
    Limb :: restoreToolkit();

    $this->action_resolver->tally();
    $this->service_resolver->tally();
    $this->service->tally();
    $this->fc->tally();
  }

  function testRunOkActionFound()
  {
    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $response = $toolkit->getResponse();

    $this->fc->expectOnce('next');

    $this->service_resolver->expectOnce('resolve', array($request));
    $this->service_resolver->setReturnReference('resolve', $this->service);

    $this->action_resolver->expectOnce('resolve', array($request));
    $this->action_resolver->setReturnValue('resolve', $action = 'do');

    $this->service->expectOnce('actionExists', array($action));
    $this->service->setReturnValue('actionExists', true);

    $this->service->expectOnce('setCurrentAction', array($action));

    $filter = new ServiceActionMappingFilter();

    $context = new DataSpace();
    $filter->run($this->fc, $request, $response, $context);

    $this->assertIsA($context->getObject('Service'), 'MockService');
  }

  function testRunOkEmptyAction()
  {
    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $response = $toolkit->getResponse();

    $this->fc->expectOnce('next');

    $this->service_resolver->expectOnce('resolve', array($request));
    $this->service_resolver->setReturnReference('resolve', $this->service);

    $this->action_resolver->expectOnce('resolve', array($request));
    $this->action_resolver->setReturnValue('resolve', '');

    $this->service->expectOnce('getDefaultAction');
    $this->service->setReturnValue('getDefaultAction', $action = 'whatever');

    $this->service->expectOnce('setCurrentAction', array($action));

    $filter = new ServiceActionMappingFilter();

    $context = new DataSpace();
    $filter->run($this->fc, $request, $response, $context);

    $this->assertIsA($context->getObject('Service'), 'MockService');
  }

  function testRunOkActionNotFound()
  {
    registerTestingIni('404.service.ini',
                       'default_action = display
                       [display]');

    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $response = $toolkit->getResponse();

    $this->fc->expectOnce('next');

    $this->service_resolver->expectOnce('resolve', array($request));
    $this->service_resolver->setReturnReference('resolve', $this->service);

    $this->action_resolver->expectOnce('resolve', array($request));
    $this->action_resolver->setReturnValue('resolve', $action = 'do');

    $this->service->expectOnce('actionExists', array($action));
    $this->service->setReturnValue('actionExists', false);

    $filter = new ServiceActionMappingFilter();

    $context = new DataSpace();
    $filter->run($this->fc, $request, $response, $context);
    $service404 = $context->getObject('Service');
    $this->assertEqual($service404->getName(), '404');
    $this->assertEqual($service404->getCurrentAction(), 'display');

    clearTestingIni();
  }
}

?>
