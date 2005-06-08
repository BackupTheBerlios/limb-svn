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
require_once(LIMB_DIR . '/core/request_resolvers/IniBasedServiceRequestResolver.class.php');

class IniBasedServiceRequestResolverTest extends LimbTestCase
{
  function IniBasedServiceRequestResolverTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function tearDown()
  {
    clearTestingIni();
  }

  function testGetRequestedService404IfNoIni()
  {
    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $resolver = new IniBasedServiceRequestResolver();

    die_on_error(false);
    $service = $resolver->resolve($request);
    die_on_error();

    $this->assertTrue(catch_error('LimbException', $e));
  }

  function testGetRequestedService404NotMatches()
  {
    registerTestingIni('services.ini',
                       '
                       [AnyService]
                       path = /news
                       service_name = NewsService
                       ');

    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $resolver = new IniBasedServiceRequestResolver();

    $service = $resolver->resolve($request);

    $this->assertEqual($service->getName(), '404');
  }

  function testGetRequestedServiceOk()
  {
    registerTestingIni('services.ini',
                       '
                       [AnyService]
                       path = /news
                       service_name = NewsService
                       ');

    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $resolver = new IniBasedServiceRequestResolver();
    $uri =& $request->getUri();
    $uri->setPath('/news');

    $service = $resolver->resolve($request);

    $this->assertEqual($service->getName(), 'NewsService');
  }

  function testGetRequestedServiceOkNotExactMatch()
  {
    registerTestingIni('services.ini',
                       '
                       [AnyService]
                       path = /news*
                       service_name = NewsService
                       ');

    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $resolver = new IniBasedServiceRequestResolver();
    $uri =& $request->getUri();
    $uri->setPath('/news/10/10');

    $service = $resolver->resolve($request);

    $this->assertEqual($service->getName(), 'NewsService');
  }
}

?>
