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
require_once(LIMB_DIR . '/core/request_resolvers/CompositeServiceRequestResolver.class.php');
require_once(LIMB_DIR . '/core/request_resolvers/RequestResolver.interface.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/services/Service.class.php');

Mock :: generate('RequestResolver');

class CompositeServiceRequestResolverTest extends LimbTestCase
{
  function CompositeServiceRequestResolverTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testResolveOkByFirstResolver()
  {
    $resolver = new CompositeServiceRequestResolver();

    $mock_resolver1 = new MockRequestResolver($this);
    $mock_resolver2 = new MockRequestResolver($this);
    $resolver->addResolver($mock_resolver1);
    $resolver->addResolver($mock_resolver2);

    $request = new Request();
    $mock_resolver1->expectOnce('resolve', array($request));
    $mock_resolver1->setReturnValue('resolve', $result = new Service('whatever'));
    $mock_resolver2->expectNever('resolve');
    $this->assertEqual($resolver->resolve($request), $result);
  }

  function testResolveOkBySecondResolverSinceFirstReturn404Service()
  {
    $resolver = new CompositeServiceRequestResolver();

    $mock_resolver1 = new MockRequestResolver($this);
    $mock_resolver2 = new MockRequestResolver($this);
    $resolver->addResolver($mock_resolver1);
    $resolver->addResolver($mock_resolver2);

    $request = new Request();
    $mock_resolver1->expectOnce('resolve', array($request));
    $mock_resolver1->setReturnValue('resolve', new Service('404'));
    $mock_resolver2->expectOnce('resolve', array($request));
    $mock_resolver2->setReturnValue('resolve', $result = new Service('whaever'));
    $this->assertEqual($resolver->resolve($request), $result);
  }

  function testResolve404ServiceSinceAllResolversCantResolve()
  {
    $resolver = new CompositeServiceRequestResolver();

    $mock_resolver1 = new MockRequestResolver($this);
    $mock_resolver2 = new MockRequestResolver($this);
    $resolver->addResolver($mock_resolver1);
    $resolver->addResolver($mock_resolver2);

    $request = new Request();
    $mock_resolver1->expectOnce('resolve', array($request));
    $mock_resolver1->setReturnValue('resolve', new Service('404'));
    $mock_resolver2->expectOnce('resolve', array($request));
    $mock_resolver2->setReturnValue('resolve', new Service('404'));
    $this->assertEqual($resolver->resolve($request), new Service('404'));
  }
}

?>
