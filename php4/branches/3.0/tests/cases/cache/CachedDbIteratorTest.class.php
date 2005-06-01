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
require_once(LIMB_DIR . '/core/cache/CacheMemoryPersister.class.php');
require_once(LIMB_DIR . '/core/cache/CachePersisterKeyDecorator.class.php');
require_once(LIMB_DIR . '/core/cache/CachedDbIterator.class.php');
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');

class RsCacheDataPager {
    function setPagedDataSet(&$dataset) {}
    function getStartingItem() {}
    function getItemsPerPage() {}
}

Mock :: generate('PagedArrayDataSet');
Mock::generate('RsCacheDataPager', 'MockPager');

class ArrayDataSetCacheStub extends PagedArrayDataSet
{
  var $calls = array('rewind' => 0,
                     'next' => 0,
                     'valid' => 0,
                     'total_row_count' => 0);

  function rewind()
  {
    $this->calls['rewind']++;
    return parent :: rewind();
  }

  function next()
  {
    $this->calls['next']++;
    return parent :: next();
  }

  function valid()
  {
    $this->calls['valid']++;
    return parent :: valid();
  }

  function getTotalRowCount()
  {
    $this->calls['total_row_count']++;
    return parent :: getTotalRowCount();
  }
}

class CachedDbIteratorTest extends LimbTestCase
{
  var $cache;

  function CachedDbIteratorTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->cache =& new CachePersisterKeyDecorator(new CacheMemoryPersister());
    $this->cache->flushAll();
  }

  function tearDown()
  {
    $this->cache->flushAll();
  }

  function testCacheWithMock()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $mock =& $this->_coachMockUsingArray($arr);
    $rs =& new CachedDbIterator($mock, $this->cache);

    $this->_traverseAndCompareArrayWithRs($arr, $rs);
    $this->_traverseAndCompareArrayWithRs($arr, $rs);

    $mock->tally();
  }

  function testUseExistingCache()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $stub =& new ArrayDataSetCacheStub($arr);

    $this->cache->put($stub, new PagedArrayDataSet($arr), RS_CACHE_COMMON_GROUP);

    $rs =& new CachedDbIterator($stub, $this->cache);

    $this->_traverseAndCompareArrayWithRs($arr, $rs);
    $this->_traverseAndCompareArrayWithRs($arr, $rs);

    $this->assertEqual($stub->calls['rewind'], 0);
    $this->assertEqual($stub->calls['valid'], 0);
    $this->assertEqual($stub->calls['next'], 0);
  }

  function testCacheMiss()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $stub =& new ArrayDataSetCacheStub($arr);
    $rs =& new CachedDbIterator($stub, $this->cache);

    $clean_stub = $stub;

    $this->_traverseAndCompareArrayWithRs($arr, $rs);
    $this->_traverseAndCompareArrayWithRs($arr, $rs);

    $this->assertEqual($stub->calls['rewind'], 1);
    $this->assertEqual($stub->calls['valid'], 4);
    $this->assertEqual($stub->calls['next'], 3);

    $this->assertTrue($this->cache->assign($var, $clean_stub, RS_CACHE_COMMON_GROUP));
    $this->assertEqual($var, new PagedArrayDataSet($arr));
  }

  function testCacheMissWithPager()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $arr_for_pager1 = array(array('bar'));
    $arr_for_pager2 = array(array('bar'),
                            array('wow'));

    $stub =& new ArrayDataSetCacheStub($arr);
    $rs =& new CachedDbIterator($stub, $this->cache);

    $this->_traverseAndCompareArrayWithRs($arr, $rs);
    $this->_traverseAndCompareArrayWithRs($arr, $rs);

    $pager1 =& new MockPager($this);
    $pager1->setReturnValue('getStartingItem', 1);
    $pager1->setReturnValue('getItemsPerPage', 1);

    $rs->paginate($pager1);

    $this->_traverseAndCompareArrayWithRs($arr_for_pager1, $rs);
    $this->_traverseAndCompareArrayWithRs($arr_for_pager1, $rs);

    $pager2 =& new MockPager($this);
    $pager2->setReturnValue('getStartingItem', 1);
    $pager2->setReturnValue('getItemsPerPage', 2);

    $rs->paginate($pager2);

    $this->_traverseAndCompareArrayWithRs($arr_for_pager2, $rs);
    $this->_traverseAndCompareArrayWithRs($arr_for_pager2, $rs);

    $pager1->tally();
    $pager2->tally();
  }

  function testCachedTotalRowCount()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $stub =& new ArrayDataSetCacheStub($arr);
    $rs =& new CachedDbIterator($stub, $this->cache);

    $this->assertEqual($rs->getTotalRowCount(), 3);
    $this->assertEqual($rs->getTotalRowCount(), 3);

    $this->assertEqual($stub->calls['total_row_count'], 1);
  }

  function testCachedTotalRowCountWithPager()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $arr_for_pager = array(array('bar'));

    $stub =& new ArrayDataSetCacheStub($arr);
    $rs =& new CachedDbIterator($stub, $this->cache);

    $this->assertEqual($rs->getTotalRowCount(), 3);

    $pager1 =& new MockPager($this);

    $rs->paginate($pager1);
    $this->assertEqual($rs->getTotalRowCount(), 3);

    $pager2 =& new MockPager($this);

    $rs->paginate($pager2);
    $this->assertEqual($rs->getTotalRowCount(), 3);

    $this->assertEqual($stub->calls['total_row_count'], 1);
  }

  function _traverseAndCompareArrayWithRs($array, &$rs)
  {
    $counter = 0;
    for($rs->rewind();$rs->valid();$rs->next())
    {
      $record = $rs->current();
      $this->assertEqual($record->export(), $array[$counter]);

      $counter++;
    }
    $this->assertEqual($counter, sizeof($array));
    $this->assertEqual($counter, $rs->getRowCount());
  }

  function _coachMockUsingArray($array)
  {
    $mock =& new MockPagedArrayDataSet($this);

    $mock->expectCallCount('rewind', 1);
    $mock->expectCallCount('next', sizeof($array));
    $mock->expectCallCount('valid', sizeof($array) + 1);

    for($i=0; $i<sizeof($array); $i++)
    {
      $mock->setReturnValueAt($i, 'valid', true);

      $dataspace = new DataSpace();
      $dataspace->import($array[$i]);
      $mock->setReturnValueAt($i, 'current', $dataspace);
    }

    $mock->setReturnValueAt($i, 'valid', false);
    return $mock;
  }
}

?>