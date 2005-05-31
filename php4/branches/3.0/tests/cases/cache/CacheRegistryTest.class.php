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

class CacheKeyFooClass{}
class CacheableFooClass{}

class CacheRegistryTest extends LimbTestCase
{
  function CacheRegistryTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->cache = new CacheRegistry();
    $this->cache->flushAll();
  }

  function testAssignScalarKeyFalse()
  {
    $this->_testAssignFalse(1);
  }

  function testAssignScalarKeyTrue()
  {
    $this->_testAssignTrue(1);
  }

  function testAssignArrayKeyFalse()
  {
    $this->_testAssignFalse(array(1));
  }

  function testAssignArrayKeyTrue()
  {
    $this->_testAssignTrue(array(1));
  }

  function testAssignObjectKeyFalse()
  {
    $this->_testAssignFalse(new CacheKeyFooClass());
  }

  function testAssignObjectKeyTrue()
  {
    $this->_testAssignTrue(new CacheKeyFooClass());
  }

  function testPutToCacheUsingScalarKey()
  {
    $this->_testPutToCache(1);
  }

  function testPutToCacheUsingArrayKey()
  {
    $this->_testPutToCache(array(1));
  }

  function testPutToCacheUsingObjectKey()
  {
    $this->_testPutToCache(new CacheKeyFooClass());
  }

  function testPutToCacheWithGroupUsingScalarKey()
  {
    $this->_testPutToCacheWithGroup(1);
  }

  function testPutToCacheWithGroupUsingArrayKey()
  {
    $this->_testPutToCacheWithGroup(array(1));
  }

  function testPutToCacheWithGroupUsingObjectKey()
  {
    $this->_testPutToCacheWithGroup(new CacheKeyFooClass());
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
    $key1 = new CacheKeyFooClass();
    $key2 = new CacheKeyFooClass();
    $key2->im_different = 1;

    $this->_testFlushValue($key1, $key2);
  }

  function testFlushAll()
  {
    $this->cache->put(1, $v1 = 'value1');
    $this->cache->put(2, $v2 = 'value2');

    $this->cache->flushAll();

    $this->assertFalse($this->cache->assign($var, 1));
    $this->assertFalse($this->cache->assign($var, 2));
  }

  function testFlushGroup()
  {
    $key = 1;
    $this->cache->put($key, $v1 = 'value1');
    $this->cache->put($key, $v2 = 'value2', 'test-group');

    $this->cache->flushGroup('test-group');

    $this->assertFalse($this->cache->assign($var, $key, 'test-group'));

    $this->assertTrue($this->cache->assign($var, $key));
    $this->assertEqual($var, $v1);
  }

  function _testAssignFalse($key)
  {
    $this->assertFalse($this->cache->assign($var, $key));
    $this->assertNull($var);
  }

  function _testAssignTrue($key)
  {
    $this->cache->put($key, $v = 'value');
    $this->assertTrue($this->cache->assign($var, $key));
    $this->assertEqual($v, $var);
  }

  function _testPutToCache($key)
  {
    $rnd_key = mt_rand();
    $this->cache->put($rnd_key, $v1 = 'value1');

    foreach($this->_getCachedValues() as $v2)
    {
      $this->cache->put($key, $v2);
      $this->assertTrue($this->cache->assign($cache_value, $key));
      $this->assertEqual($cache_value, $v2);

      $this->assertTrue($this->cache->assign($cache_value, $rnd_key));
      $this->assertEqual($cache_value, $v1);
    }
  }

  function _testPutToCacheWithGroup($key)
  {
    $this->cache->put($key, $v1 = 'value1');
    $this->cache->put($key, $v2 = 'value2', 'test-group');

    $this->assertTrue($this->cache->assign($cache_value, $key));
    $this->assertEqual($cache_value, $v1);

    $this->assertTrue($this->cache->assign($cache_value, $key, 'test-group'));
    $this->assertEqual($cache_value, $v2);
  }

  function _testFlushValue($key1, $key2)
  {
    $this->cache->put($key1, $v1 = 'value1');
    $this->cache->put($key2, $v2 = 'value2');

    $this->cache->flushValue($key1);

    $this->assertFalse($this->cache->assign($cache_value, $key1));

    $this->assertTrue($this->cache->assign($cache_value, $key2));
    $this->assertEqual($cache_value, $v2);
  }

  function _getCachedValues()
  {
    return array($this->_createNullValue(),
                 $this->_createScalarValue(),
                 $this->_createArrayValue(),
                 $this->_createObjectValue());
  }

  function _createNullValue()
  {
    return null;
  }

  function _createScalarValue()
  {
    return 'some value';
  }

  function _createArrayValue()
  {
    return array('some value');
  }

  function _createObjectValue()
  {
    return new CacheableFooClass();
  }
}

?>