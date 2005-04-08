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
require_once(LIMB_DIR . '/core/cache/CacheRegistry.class.php');

class CacheRegistryTest extends LimbTestCase
{
  var $cache;

  function CacheRegistryTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->cache = new CacheRegistry();
  }

  function testGetNull()
  {
    $key = 'empty';

    $this->assertNull($this->cache->get($key));
  }

  function testGetNull2()
  {
    $key = 'empty';
    $this->cache->put($key, $v = 'value', 'some-group');

    $this->assertNull($this->cache->get($key));
  }

  function testPutToCacheNoGroup()
  {
    $key = 1;
    $this->cache->put($key, $v = 'value');

    $this->assertEqual($this->cache->get($key), 'value');
  }

  function testPutToCacheWithGroup()
  {
    $key = 1;
    $this->cache->put($key, $v = 'value', 'test-group');

    $this->assertEqual($this->cache->get($key, 'test-group'), $v);
  }

  function testFlushAll()
  {
    $key = 1;
    $this->cache->put($key, $v = 'value');

    $this->cache->flush();

    $this->assertNull($this->cache->get($key));
  }

  function testFlushGroup()
  {
    $key = 1;
    $this->cache->put($key, $v1 = 'value1');
    $this->cache->put($key, $v2 = 'value2', 'test-group');

    $this->cache->flush('test-group');

    $this->assertNull($this->cache->get($key, 'test-group'));
    $this->assertEqual($this->cache->get($key), $v1);
  }

}

?>