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
require_once(LIMB_DIR . '/core/filters/RequestResolverMappingFilter.class.php');
require_once(LIMB_DIR . '/core/request_resolvers/RequestResolverMapper.interface.php');

Mock :: generate('FilterChain');
Mock :: generate('RequestResolverMapper');

class RequestResolverMappingFilterTest extends LimbTestCase
{
  function RequestResolverMappingFilterTest()
  {
    parent :: LimbTestCase('request resolver mapping filter test');
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testRunOk()
  {
    $toolkit =& Limb :: toolkit();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $request = $toolkit->getRequest();
    $response = $toolkit->getResponse();

    $mapper = new MockRequestResolverMapper($this);

    $filter = new RequestResolverMappingFilter();
    $filter->registerMapper($mapper);

    $mapper->expectOnce('map', array($request));
    $mapper->setReturnValue('map', $expected_resolver = new Object());

    $filter->run($fc, $request, $response);

    $resolver =& $toolkit->getRequestResolver();

    $this->assertEqual($resolver, $expected_resolver);

    $fc->tally();
    $mapper->tally();
  }

  function test404error()
  {
    $toolkit =& Limb :: toolkit();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $request = $toolkit->getRequest();
    $response = $toolkit->getResponse();

    $mapper = new MockRequestResolverMapper($this);

    $filter = new RequestResolverMappingFilter();
    $filter->registerMapper($mapper);

    $mapper->expectOnce('map', array($request));
    $mapper->setReturnValue('map', null);

    $filter->run($fc, $request, $response);

    $resolver =& $toolkit->getRequestResolver();

    $this->assertIsA($resolver, 'NotFoundRequestResolver');

    $fc->tally();
    $mapper->tally();
  }
}

?>
