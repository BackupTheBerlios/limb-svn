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
require_once(LIMB_DIR . '/class/core/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/class/core/filters/FullPageCacheFilter.class.php');
require_once(LIMB_DIR . '/class/cache/FullPageCacheManager.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/core/request/HttpResponse.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('FilterChain');
Mock :: generate('HttpResponse');
Mock :: generate('Request');
Mock :: generate('Response');
Mock :: generate('FullPageCacheManager');

Mock :: generatePartial('FullPageCacheFilter',
                        'FullPageCacheFilterTestVersion',
                        array('_isCachingEnabled',
                              '_getFullPacheCacheManager'));

class FullPageCacheFilterTest extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $toolkit;
  var $response;

  function setUp()
  {
    $this->filter = new FullPageCacheFilterTestVersion($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->request = new MockRequest($this);
    $this->filter_chain = new MockFilterChain($this);
    $this->response = new MockHttpResponse($this);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->request->tally();
    $this->response->tally();

    $this->toolkit->tally();

    Limb :: popToolkit();
  }

  function testCachingDisabled()
  {
    $this->filter->setReturnValue('_isCachingEnabled', false);

    $this->filter_chain->expectOnce('next');
    $this->filter->expectNever('_getFullPacheCacheManager');

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $this->filter->tally();
    $this->filter_chain->tally();
  }

  function testCacheHit()
  {
    $this->filter->setReturnValue('_isCachingEnabled', true);

    $cache_manager = new MockFullPageCacheManager($this);
    $this->filter_chain->expectOnce('next');
    $this->filter->setReturnReference('_getFullPacheCacheManager', $cache_manager);

    $cache_manager->expectOnce('setRequest');
    $cache_manager->expectOnce('get');
    $cache_manager->setReturnValue('get', $result = 'someCachedResult');

    $this->response->expectOnce('write', array($result));
    $this->filter_chain->expectNever('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $cache_manager->tally();
  }

  function testCacheMiss()
  {
    $this->filter->setReturnValue('_isCachingEnabled', true);

    $cache_manager = new MockFullPageCacheManager($this);
    $this->filter_chain->expectOnce('next');
    $this->filter->setReturnReference('_getFullPacheCacheManager', $cache_manager);

    $cache_manager->expectOnce('setRequest');
    $cache_manager->expectOnce('get');
    $cache_manager->setReturnValue('get', null);

    $this->filter_chain->expectOnce('next');

    $this->response->setReturnValue('getResponseString', $result = 'someRenderedResult');
    $cache_manager->expectOnce('write', array($result));

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $cache_manager->tally();
  }
}

?>