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
require_once(LIMB_DIR . '/core/filters/ImageCacheFilter.class.php');
require_once(LIMB_DIR . '/core/cache/ImageCacheManager.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('FilterChain');
Mock :: generate('HttpResponse');
Mock :: generate('Request');
Mock :: generate('Response');
Mock :: generate('ImageCacheManager');

Mock :: generatePartial('ImageCacheFilter',
                        'ImageCacheFilterTestVersion',
                        array('_isCachingEnabled',
                              '_getImageCacheManager'));

class ImageCacheFilterTest extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $toolkit;
  var $response;

  function setUp()
  {
    $this->filter = new ImageCacheFilterTestVersion($this);

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
    $this->filter->expectNever('_getImageCacheManager');

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $this->filter->tally();
    $this->filter_chain->tally();
  }

  function testNoContent()
  {
    $this->filter->setReturnValue('_isCachingEnabled', true);

    $cache_manager = new MockImageCacheManager($this);
    $this->filter_chain->expectOnce('next');
    $this->filter->setReturnReference('_getImageCacheManager', $cache_manager);

    $this->filter_chain->expectOnce('next');

    $cache_manager->expectOnce('setUri');

    $this->response->expectOnce('fileSent');
    $this->response->setReturnValue('fileSent', true);

    $cache_manager->expectNever('processContent');
    $this->response->expectNever('write');

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $cache_manager->tally();
  }

  function testProcessContent()
  {
    $this->filter->setReturnValue('_isCachingEnabled', true);

    $cache_manager = new MockImageCacheManager($this);
    $this->filter_chain->expectOnce('next');
    $this->filter->setReturnReference('_getImageCacheManager', $cache_manager);

    $this->filter_chain->expectOnce('next');

    $cache_manager->expectOnce('setUri');

    $this->response->expectOnce('fileSent');
    $this->response->setReturnValue('fileSent', false);

    $this->response->setReturnValue('getResponseString', $result = 'someResponse');

    $cache_manager->expectOnce('processContent', array($result));
    $cache_manager->setReturnValue('processContent', $result);
    $this->response->expectOnce('write', array($result));

    $this->filter->run($this->filter_chain, $this->request, $this->response);

    $cache_manager->tally();
  }
}

?>