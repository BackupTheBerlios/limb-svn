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
require_once(LIMB_DIR . '/class/core/tree/CachingTree.class.php');
require_once(LIMB_DIR . '/class/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/cache/CacheRegistry.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Tree');
Mock :: generate('CacheRegistry');

class CachingTreeTest extends LimbTestCase
{
  var $tree;
  var $driver;
  var $toolkit;
  var $cache;

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);
    $this->tree = new MockTree($this);
    $this->cache = new MockCacheRegistry($this);

    $this->toolkit->setReturnValue('getCache', $this->cache);

    Limb :: registerToolkit($this->toolkit);

    $this->decorator = new CachingTree($this->tree);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->tree->tally();
    $this->cache->tally();

    Limb :: popToolkit();
  }

  function testGetNodeCacheHit()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('node' => $node_id), CachingTree :: CACHE_GROUP));
    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('getNode');
    $this->assertEqual($this->decorator->getNode($node_id), $result);
  }

  function testGetNodeCacheMiss()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('node' => $node_id), CachingTree :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('getNode', array($node_id));
    $this->tree->setReturnValue('getNode', $result = 'some result');

    $this->cache->expectOnce('put', array(array('node' => $node_id), $result, CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->getNode($node_id), $result);
  }

  function testGetParentsCacheHit()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('parents' => $node_id),
                                          CachingTree :: CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('getParents');
    $this->assertEqual($this->decorator->getParents($node_id), $result);
  }

  function testGetParentsCacheMiss()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('parents' => $node_id), CachingTree :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('getParents', array($node_id));
    $this->tree->setReturnValue('getParents', $result = 'some result');

    $this->cache->expectOnce('put', array(array('parents' => $node_id), $result, CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->getParents($node_id), $result);
  }

  function testGetChildrenCacheHit()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('children' => $node_id),
                                          CachingTree :: CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('getChildren');
    $this->assertEqual($this->decorator->getChildren($node_id), $result);
  }

  function testGetChildrenCacheMiss()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('children' => $node_id),
                                          CachingTree :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('getChildren', array($node_id));
    $this->tree->setReturnValue('getChildren', $result = 'some result');

    $this->cache->expectOnce('put', array(array('children' => $node_id),
                                          $result,
                                          CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->getChildren($node_id), $result);
  }

  function testCountChildrenCacheHit()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('count_children' => $node_id),
                                          CachingTree :: CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('countChildren');
    $this->assertEqual($this->decorator->countChildren($node_id), $result);
  }

  function testCountChildrenCacheMiss()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('count_children' => $node_id),
                                          CachingTree :: CACHE_GROUP));

    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('countChildren', array($node_id));
    $this->tree->setReturnValue('countChildren', $result = 'some result');

    $this->cache->expectOnce('put', array(array('count_children' => $node_id),
                                          $result,
                                          CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->countChildren($node_id), $result);
  }

  function testCreateRootNode()
  {
    $this->tree->setReturnValue('createRootNode', $result = 'someResult', array($values = 'whatever'));

    $this->cache->expectOnce('flush', array(CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->createRootNode($values), $result);
  }

  function testCreateSubNode()
  {
    $this->tree->setReturnValue('createSubNode',
                                $result = 'someResult',
                                array($id = 'id',$values = 'whatever'));

    $this->cache->expectOnce('flush', array(CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->createSubNode($id, $values), $result);
  }

  function testDeleteNode()
  {
    $this->tree->setReturnValue('deleteNode', $result = 'someResult', array($id = 'id'));

    $this->cache->expectOnce('flush', array(CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->deleteNode($id), $result);
  }

  function testUpdateNode()
  {
    $this->tree->setReturnValue('updateNode',
                                $result = 'someResult',
                                array($id = 'id', $values = 'whatever', false));

    $this->cache->expectOnce('flush', array(CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->updateNode($id, $values), $result);
  }

  function testMoveTree()
  {
    $this->tree->setReturnValue('moveTree',
                                $result = 'some result',
                                array($id = 'id', $target_id = 'target'));

    $this->cache->expectOnce('flush', array(CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->moveTree($id, $target_id), $result);
  }

  function testGetNodeByPathCacheHit()
  {
    $path = 'some_path';

    $this->cache->expectOnce('get', array(array('path' => $path),
                                          CachingTree :: CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('getNodeByPath');
    $this->assertEqual($this->decorator->getNodeByPath($path), $result);
  }

  function testGetNodeByPathCacheMiss()
  {
    $path = 'some_path';
    $delimeter = '/';
    $this->cache->expectOnce('get', array(array('path' => $path),
                                          CachingTree :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('getNodeByPath', array($path, $delimeter));
    $this->tree->setReturnValue('getNodeByPath', $result = 'some result');

    $this->cache->expectOnce('put', array(array('path' => $path),
                                          $result,
                                          CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->getNodeByPath($path,$delimeter), $result);
  }

  function testGetSubBranchCacheHit()
  {
    $key = array('sub_branch',
                 'node_id' => $node_id = 100,
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false );

    $this->cache->expectOnce('get', array($key, CachingTree :: CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('getSubBranch');
    $this->assertEqual($this->decorator->getSubBranch($node_id, $depth, false, false), $result);
  }

  function testGetSubBranchCacheMiss()
  {
    $key = array('sub_branch',
                 'node_id' => $node_id = 100,
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false );

    $this->cache->expectOnce('get', array($key, CachingTree :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('getSubBranch', array($node_id, $depth, false, false));
    $this->tree->setReturnValue('getSubBranch', $result = 'some result');

    $this->cache->expectOnce('put', array($key, $result, CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->getSubBranch($node_id, $depth, false, false), $result);
  }

  function testGetRootNodesCacheHit()
  {
    $this->cache->expectOnce('get', array(array('root_nodes'),
                                          CachingTree :: CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('getRootNodes');
    $this->assertEqual($this->decorator->getRootNodes(), $result);
  }

  function testGetRootNodesCacheMiss()
  {
    $this->cache->expectOnce('get', array(array('root_nodes'),
                                          CachingTree :: CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('getRootNodes');
    $this->tree->setReturnValue('getRootNodes', $result = 'some result');

    $this->cache->expectOnce('put', array(array('root_nodes'),
                                          $result,
                                          CachingTree :: CACHE_GROUP));

    $this->assertEqual($this->decorator->getRootNodes(), $result);
  }

}

?>
