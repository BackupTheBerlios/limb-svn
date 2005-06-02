<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CacheFilePersisterTest.class.php 1343 2005-06-01 08:16:13Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/cache/CacheCompositePersister.class.php');
require_once(LIMB_DIR . '/core/cache/CachePersister.class.php');
require_once(LIMB_DIR . '/core/cache/CacheMemoryPersister.class.php');

Mock :: generate('CachePersister');

class CacheCompositePersisterTest extends LimbTestCase
{
  var $cache;

  function CacheCompositePersisterTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->cache = new CacheCompositePersister();
  }

  function testGetFailure()
  {
    $this->assertEqual($this->cache->get(1, 'group'), CACHE_NULL_RESULT);
  }

  function testGetSuccess()
  {
    $p1 = new MockCachePersister($this);
    $p2 = new MockCachePersister($this);

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('get', array($key = 1, $group = 'some_group'));
    $p1->setReturnValue('get', $value = 'value');

    $p2->expectNever('get');

    $this->assertEqual($value, $this->cache->get($key, $group));

    $p1->tally();
    $p2->tally();
  }

  function testGetSuccessCacheValueForUpperPersister()
  {
    $p1 = new MockCachePersister($this);
    $p2 = new MockCachePersister($this);

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('get');
    $p1->setReturnValue('get', CACHE_NULL_RESULT, array($key = 1, $group = 'some_group'));

    $p2->expectOnce('get');
    $p2->setReturnValue('get', $value = 'value', array($key, $group));

    $p1->expectOnce('put', array($key, $value, $group));

    $this->assertEqual($value, $this->cache->get($key, $group));

    $p1->tally();
    $p2->tally();
  }

  function testPutValue()
  {
    $p1 = new MockCachePersister($this);
    $p2 = new MockCachePersister($this);

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('put', array($key = 1, $value = 'whatever', $group = 'some_group'));
    $p2->expectOnce('put', array($key, $value, $group));

    $this->cache->put($key, $value, $group);

    $p1->tally();
    $p2->tally();
  }

  function testFlushValue()
  {
    $p1 = new MockCachePersister($this);
    $p2 = new MockCachePersister($this);

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('flushValue', array($key = 1, $group = 'some_group'));
    $p2->expectOnce('flushValue', array($key, $group));

    $this->cache->flushValue($key, $group);

    $p1->tally();
    $p2->tally();
  }

  function testFlushGroup()
  {
    $p1 = new MockCachePersister($this);
    $p2 = new MockCachePersister($this);

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('flushGroup', array($group = 'some_group'));
    $p2->expectOnce('flushGroup', array($group));

    $this->cache->flushGroup($group);

    $p1->tally();
    $p2->tally();
  }

  function testFlushAll()
  {
    $p1 = new MockCachePersister($this);
    $p2 = new MockCachePersister($this);

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('flushAll', array());
    $p2->expectOnce('flushAll', array());

    $this->cache->flushAll();

    $p1->tally();
    $p2->tally();
  }

  function testRealGet()
  {
    $p1 = new CacheMemoryPersister();
    $p2 = new CacheMemoryPersister();
    $p3 = new CacheMemoryPersister();

    $p3->put($key = 1, $value='yahoo', $group = 'group');

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);
    $this->cache->registerPersister($p3);

    $cache_value = $this->cache->get($key, $group);

    $this->assertEqual($value, $cache_value);
  }
}

?>