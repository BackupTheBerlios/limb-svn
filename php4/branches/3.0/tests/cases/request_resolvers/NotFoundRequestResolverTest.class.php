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
require_once(LIMB_DIR . '/core/request_resolvers/NotFoundRequestResolver.class.php');

class NotFoundRequestResolverTest extends LimbTestCase
{
  function NotFoundRequestResolverTest()
  {
    parent :: LimbTestCase('not found request resolver test');
  }

  function setUp()
  {
  }

  function tearDown()
  {
  }

  function testGetters()
  {
    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();

    $resolver = new NotFoundRequestResolver();

    $service =& $resolver->getRequestedService($request);
    $this->assertEqual($service->getName(), '404');

    $this->assertEqual($resolver->getAction($request), 'display');

    $this->assertEqual($resolver->getRequestedEntity($request), new Object());
  }
}

?>
