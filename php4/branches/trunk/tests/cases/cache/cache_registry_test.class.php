<?php
/**********************************************************************************
* copyright 2004 BIT, _ltd. http://limb-project.com, mailto: support@limb-project.com
*
* released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: cache_registry_test.class.php 1260 2005-04-20 15:10:07Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/cache/cache_registry.class.php');

class cacheable_foo_class{}

class cache_registry_test extends LimbTestCase
{
  var $cache;

  function setUp()
  {
    $this->cache = new cache_registry();
  }

  function test_get_null()
  {
    $key = 'empty';

    $this->assertNull($this->cache->get($key));
  }

  function test_get_null_array_key()
  {
    $key = array('empty');

    $this->assertNull($this->cache->get($key));
  }

  function test_get_null_object_key()
  {
    $key = new cacheable_foo_class();

    $this->assertNull($this->cache->get($key));
  }

  function test_get_null2()
  {
    $key = 'empty';
    $this->cache->put($key, $v = 'value', 'some-group');

    $this->assertNull($this->cache->get($key));
  }

  function test_put_to_cache_no_group()
  {
    $key = 1;
    $this->cache->put($key, $v = 'value');

    $this->assertEqual($this->cache->get($key), 'value');
  }

  function test_put_to_cache_no_group_array_key()
  {
    $key = array(1);
    $this->cache->put($key, $v = 'value');

    $this->assertEqual($this->cache->get($key), 'value');
  }

  function test_put_to_cache_no_group_object_key()
  {
    $key = new cacheable_foo_class();
    $this->cache->put($key, $v = 'value');

    $this->assertEqual($this->cache->get($key), 'value');
  }

  function test_put_to_cache_with_group()
  {
    $key = 1;
    $this->cache->put($key, $v = 'value', 'test-group');

    $this->assertEqual($this->cache->get($key, 'test-group'), $v);
  }

  function test_put_to_cache_with_group_array_key()
  {
    $key = array(1);
    $this->cache->put($key, $v = 'value', 'test-group');

    $this->assertEqual($this->cache->get($key, 'test-group'), $v);
  }

  function test_put_to_cache_with_group_object_key()
  {
    $key = new cacheable_foo_class();
    $this->cache->put($key, $v = 'value', 'test-group');

    $this->assertEqual($this->cache->get($key, 'test-group'), $v);
  }

  function test_purge()
  {
    $key = 1;
    $this->cache->put($key, $v = 'value');

    $this->cache->purge($key);

    $this->assertNull($this->cache->get($key));
  }

  function test_purge_array_key()
  {
    $key = array(1);
    $this->cache->put($key, $v = 'value');

    $this->cache->purge($key);

    $this->assertNull($this->cache->get($key));
  }

  function test_purge_object_key()
  {
    $key = new cacheable_foo_class();
    $this->cache->put($key, $v = 'value');

    $this->cache->purge($key);

    $this->assertNull($this->cache->get($key));
  }

  function test_flush_all()
  {
    $key = 1;
    $this->cache->put($key, $v = 'value');

    $this->cache->flush();

    $this->assertNull($this->cache->get($key));
  }

  function test_flush_group()
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