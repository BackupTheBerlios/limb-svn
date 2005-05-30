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

class CacheableFooClass{}

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
    $this->cache->flushAll();
  }

  function testAssignArrayKeyFalse()
  {
    $key = array('empty');

    $this->assertFalse($this->cache->assign($var, $key));
  }

  function testAssignArrayKeyTrue()
  {
    $key = array('empty');
    $this->cache->put($key, $v = 'value');

    $this->assertTrue($this->cache->assign($var, $key));
    $this->assertEqual($v, $var);
  }

  function testGetNull()
  {
    $key = 'empty';

    $this->assertNull($this->cache->get($key));
  }

  function testGetNullArrayKey()
  {
    $key = array('empty');

    $this->assertNull($this->cache->get($key));
  }

  function testGetNullObjectKey()
  {
    $key = new CacheableFooClass();

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

  function testPutToCacheNoGroupArrayKey()
  {
    $key = array(1);
    $this->cache->put($key, $v = 'value');

    $this->assertEqual($this->cache->get($key), 'value');
  }

  function testPutToCacheNoGroupObjectKey()
  {
    $key = new CacheableFooClass();
    $this->cache->put($key, $v = 'value');

    $this->assertEqual($this->cache->get($key), 'value');
  }

  function testPutToCacheWithGroup()
  {
    $key = 1;
    $this->cache->put($key, $v = 'value', 'test-group');

    $this->assertEqual($this->cache->get($key, 'test-group'), $v);
  }

  function testPutToCacheWithGroupArrayKey()
  {
    $key = array(1);
    $this->cache->put($key, $v = 'value', 'test-group');

    $this->assertEqual($this->cache->get($key, 'test-group'), $v);
  }

  function testPutToCacheWithGroupObjectKey()
  {
    $key = new CacheableFooClass();
    $this->cache->put($key, $v = 'value', 'test-group');

    $this->assertEqual($this->cache->get($key, 'test-group'), $v);
  }

  function testFlushValueWithScalarKey()
  {
    $this->_testFlushValue(1, 2);
  }

  function testFlushValueWithArrayKey()
  {
    $this->_testFlushValue(array(1), array(2));
  }

  function testFlushValueWithObjectKey()
  {
    $key1 = new CacheableFooClass();
    $key2 = new CacheableFooClass();
    $key2->im_different = 1;

    $this->_testFlushValue($key1, $key2);
  }

  function testFlushAll()
  {
    $this->cache->put(1, $v1 = 'value1');
    $this->cache->put(2, $v2 = 'value2');

    $this->cache->flushAll();

    $this->assertNull($this->cache->get(1));
    $this->assertNull($this->cache->get(2));
  }

  function testFlushGroup()
  {
    $key = 1;
    $this->cache->put($key, $v1 = 'value1');
    $this->cache->put($key, $v2 = 'value2', 'test-group');

    $this->cache->flushGroup('test-group');

    $this->assertNull($this->cache->get($key, 'test-group'));
    $this->assertEqual($this->cache->get($key), $v1);
  }

  function _testFlushValue($key1, $key2)
  {
    $this->cache->put($key1, $v1 = 'value1');
    $this->cache->put($key2, $v2 = 'value2');

    $this->cache->flushValue($key1);

    $this->assertNull($this->cache->get($key1));
    $this->assertEqual($v2, $this->cache->get($key2));
  }
}

?>