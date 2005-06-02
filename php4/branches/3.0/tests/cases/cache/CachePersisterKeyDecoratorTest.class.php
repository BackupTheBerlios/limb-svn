<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CacheRegistryTest.class.php 1340 2005-05-31 15:01:35Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/cache/CachePersisterKeyDecorator.class.php');
require_once(LIMB_DIR . '/core/cache/CacheMemoryPersister.class.php');
require_once(dirname(__FILE__) . '/CacheBaseTest.class.php');

class CacheKeyFooClass{}
class CacheableFooClass{}

class CachePersisterKeyDecoratorTest extends CacheBaseTest
{
  function CachePersisterKeyDecoratorTest()
  {
    parent :: CacheBaseTest(__FILE__);
  }

  function &_createPersisterImp()
  {
    return new CachePersisterKeyDecorator(new CacheMemoryPersister());
  }

  function testGetArrayKeyFalse()
  {
    $this->_testGetFalse(array(1));
  }

  function testGetArrayKeyTrue()
  {
    $this->_testGetTrue(array(1));
  }

  function testGetObjectKeyFalse()
  {
    $this->_testGetFalse(new CacheKeyFooClass());
  }

  function testGetObjectKeyTrue()
  {
    $this->_testGetTrue(new CacheKeyFooClass());
  }

  function testPutToCacheUsingArrayKey()
  {
    $this->_testPutToCache(array(1));
  }

  function testPutToCacheUsingObjectKey()
  {
    $this->_testPutToCache(new CacheKeyFooClass());
  }

  function testPutToCacheWithGroupUsingArrayKey()
  {
    $this->_testPutToCacheWithGroup(array(1));
  }

  function testPutToCacheWithGroupUsingObjectKey()
  {
    $this->_testPutToCacheWithGroup(new CacheKeyFooClass());
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
}

?>