<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/filters/filter_chain.class.php');
require_once(LIMB_DIR . '/class/core/filters/full_page_cache_filter.class.php');
require_once(LIMB_DIR . '/class/cache/full_page_cache_manager.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/request/http_response.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('filter_chain');
Mock :: generate('http_response');
Mock :: generate('request');
Mock :: generate('response');
Mock :: generate('full_page_cache_manager');

Mock :: generatePartial('full_page_cache_filter',
                        'full_page_cache_filter_test_version',
                        array('_is_caching_enabled',
                              '_get_full_pache_cache_manager')); 

class full_page_cache_filter_test extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $toolkit;
  var $response;
  
  function setUp()
  {
    $this->filter = new full_page_cache_filter_test_version($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->request = new Mockrequest($this);
    $this->filter_chain = new Mockfilter_chain($this);
    $this->response = new Mockhttp_response($this);
    
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  {
    $this->request->tally();
    $this->response->tally();  

    $this->toolkit->tally();

    Limb :: popToolkit();    
  }
  
  function test_caching_disabled()
  {
    $this->filter->setReturnValue('_is_caching_enabled', false);
    
    $this->filter_chain->expectOnce('next');
    $this->filter->expectNever('_get_full_pache_cache_manager');
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
    
    $this->filter->tally();
    $this->filter_chain->tally();
  }
  
  function test_cache_hit()
  {
    $this->filter->setReturnValue('_is_caching_enabled', true);
    
    $cache_manager = new Mockfull_page_cache_manager($this);
    $this->filter_chain->expectOnce('next');
    $this->filter->setReturnValue('_get_full_pache_cache_manager', $cache_manager);
    
    $cache_manager->expectOnce('set_request');
    $cache_manager->expectOnce('get');
    $cache_manager->setReturnValue('get', $result = 'some_cached_result');
    
    $this->response->expectOnce('write', array($result));
    $this->filter_chain->expectNever('next');
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
    
    $cache_manager->tally();
  }

  function test_cache_miss()
  {
    $this->filter->setReturnValue('_is_caching_enabled', true);
    
    $cache_manager = new Mockfull_page_cache_manager($this);
    $this->filter_chain->expectOnce('next');
    $this->filter->setReturnValue('_get_full_pache_cache_manager', $cache_manager);
    
    $cache_manager->expectOnce('set_request');
    $cache_manager->expectOnce('get');
    $cache_manager->setReturnValue('get', null);
    
    $this->filter_chain->expectOnce('next');

    $this->response->setReturnValue('get_response_string', $result = 'some_rendered_result');
    $cache_manager->expectOnce('write', array($result));
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
    
    $cache_manager->tally();
  }
}

?>