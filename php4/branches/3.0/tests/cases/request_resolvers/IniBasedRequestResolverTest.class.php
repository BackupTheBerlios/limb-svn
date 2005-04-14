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
require_once(LIMB_DIR . '/core/request_resolvers/IniBasedRequestResolver.class.php');

class IniBasedRequestResolverTest extends LimbTestCase
{
  function IniBasedRequestResolverTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
  }

  function tearDown()
  {
    clearTestingIni();
  }

  function testGetRequestedService404IfNoIni()
  {
    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $resolver = new IniBasedRequestResolver();

    $service = $resolver->getRequestedService($request);

    $this->assertEqual($service->getName(), '404');
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
    $resolver = new IniBasedRequestResolver();

    $service = $resolver->getRequestedService($request);

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
    $resolver = new IniBasedRequestResolver();
    $uri =& $request->getUri();
    $uri->setPath('/news');

    $service = $resolver->getRequestedService($request);

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
    $resolver = new IniBasedRequestResolver();
    $uri =& $request->getUri();
    $uri->setPath('/news/10/10');

    $service = $resolver->getRequestedService($request);

    $this->assertEqual($service->getName(), 'NewsService');
  }
}

?>
