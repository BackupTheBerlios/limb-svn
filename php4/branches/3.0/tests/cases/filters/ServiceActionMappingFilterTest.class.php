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
  function ServiceActionMappingFilterTest()
  {
    parent :: LimbTestCase('service action mapping filter test');
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
    $response = $toolkit->getResponse();

    $resolver = new MockRequestResolver($this);
    $service = new MockService($this);

    $toolkit->setRequestResolver($resolver);

    $filter = new ServiceActionMappingFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $resolver->expectOnce('getRequestedService', array($request));
    $resolver->setReturnReference('getRequestedService', $service);

    $resolver->expectOnce('getRequestedAction', array($request));
    $resolver->setReturnValue('getRequestedAction', $action = 'do');

    $resolver->expectOnce('getRequestedEntity', array($request));
    $resolver->setReturnReference('getRequestedEntity', $entity = new Object());

    $service->expectOnce('actionExists', array($action));
    $service->setReturnValue('actionExists', true);

    $service->expectOnce('setCurrentAction', array($action));

    $filter->run($fc, $request, $response);

    $service =& $toolkit->getCurrentService();

    $this->assertIsA($service, 'MockService');

    $this->assertEqual($toolkit->getCurrentEntity(), $entity);

    $fc->tally();
    $resolver->tally();
    $service->tally();
  }

  function testRunOkEmptyAction()
  {
    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $response = $toolkit->getResponse();

    $resolver = new MockRequestResolver($this);
    $service = new MockService($this);

    $toolkit->setRequestResolver($resolver);

    $filter = new ServiceActionMappingFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $resolver->expectOnce('getRequestedService', array($request));
    $resolver->setReturnReference('getRequestedService', $service);

    $resolver->expectOnce('getRequestedAction', array($request));
    $resolver->setReturnValue('getRequestedAction', '');

    $service->expectOnce('getDefaultAction');
    $service->setReturnValue('getDefaultAction', $action = 'whatever');

    $service->expectOnce('setCurrentAction', array($action));

    $filter->run($fc, $request, $response);

    $service =& $toolkit->getCurrentService();

    $this->assertIsA($service, 'MockService');

    $fc->tally();
    $resolver->tally();
    $service->tally();
  }

  function testRunOkActionNotFound()
  {
    registerTestingIni('404.service.ini',
                       'default_action = display
                       [display]');

    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $response = $toolkit->getResponse();

    $resolver = new MockRequestResolver($this);
    $service = new MockService($this);

    $toolkit->setRequestResolver($resolver);

    $filter = new ServiceActionMappingFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $resolver->expectOnce('getRequestedService', array($request));
    $resolver->setReturnReference('getRequestedService', $service);

    $resolver->expectOnce('getRequestedEntity', array($request));
    $resolver->setReturnReference('getRequestedEntity', $entity = new Object());

    $resolver->expectOnce('getRequestedAction', array($request));
    $resolver->setReturnValue('getRequestedAction', $action = 'do');

    $service->expectOnce('actionExists', array($action));
    $service->setReturnValue('actionExists', false);

    $filter->run($fc, $request, $response);

    $service404 =& $toolkit->getCurrentService();

    $this->assertEqual($service404->getName(), '404');
    $this->assertEqual($service404->getCurrentAction(), 'display');

    $this->assertEqual($toolkit->getCurrentEntity(), $entity);

    $fc->tally();
    $resolver->tally();
    $service->tally();

    clearTestingIni();
  }
}

?>
