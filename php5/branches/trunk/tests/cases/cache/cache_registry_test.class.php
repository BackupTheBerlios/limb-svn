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
require_once(LIMB_DIR . '/class/cache/cache_registry.class.php');

class cache_registry_test extends LimbTestCase
{
  var $cache;
  
  function setUp()
  {
    $this->cache = new CacheRegistry();
  }

  function test_get_null()
  {
    $key = 'empty';
    
    $this->assertNull($this->cache->get($key));
  }

  function test_get_null2()
  {
    $key = 'empty';
    $this->cache->put($key, 'value', 'some-group');
    
    $this->assertNull($this->cache->get($key));
  }
  
  function test_put_to_cache_no_group()
  {
    $key = array('c1' => 'c', 'c2' => 'c');
    $this->cache->put($key, 'value');
    
    $this->assertEqual($this->cache->get($key), 'value');
  }
  
  function test_put_to_cache_with_group()
  {
    $key = array('c1' => 'c', 'c2' => 'c');
    $this->cache->put($key, 'value', 'test-group');
    
    $this->assertEqual($this->cache->get($key, 'test-group'), 'value');
  }

  function test_flush_all()
  {
    $key = array('c1' => 'c', 'c2' => 'c');
    $this->cache->put($key, 'value');
    
    $this->cache->flush();
    
    $this->assertNull($this->cache->get($key));
  }
  
  function test_flush_group()
  {
    $key = array('c1' => 'c', 'c2' => 'c');
    $this->cache->put($key, 'value1');
    $this->cache->put($key, 'value2', 'test-group');
    
    $this->cache->flush('test-group');
    
    $this->assertNull($this->cache->get($key, 'test-group'));
    $this->assertEqual($this->cache->get($key), 'value1');
  }
  
}

?> 