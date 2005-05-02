<?php
/**********************************************************************************
* copyright 2004 BIT, _ltd. http://limb-project.com, mailto: support@limb-project.com
*
* released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: caching_tree_test.class.php 1209 2005-04-08 14:29:41Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/tree/caching_tree.class.php');
require_once(LIMB_DIR . '/core/cache/cache_registry.class.php');
require_once(LIMB_DIR . '/core/tree/tree.class.php');

Mock:: generate('tree');
Mock:: generate('cache_registry');

Mock:: generatePartial('caching_tree',
                       'caching_tree_test_version',
                       array('_create_cache'));

class caching_tree_test extends LimbTestCase
{
  var $tree;
  var $cache;
  var $decorator;

  function setUp()
  {
    $this->tree = new Mocktree($this);
    $this->cache = new Mockcache_registry($this);

    $this->decorator = new caching_tree_test_version($this);
    $this->decorator->setReturnReference('_create_cache', $this->cache);
    $this->decorator->caching_tree($this->tree);
  }

  function tearDown()
  {
    $this->tree->tally();
    $this->cache->tally();
  }

  function test_get_node_cache_hit()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('node' => $node_id), CACHING_TREE_CACHE_GROUP));
    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('get_node');
    $this->assertEqual($this->decorator->get_node($node_id), $result);
  }

  function test_get_node_cache_miss()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('node' => $node_id), CACHING_TREE_CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('get_node', array($node_id));
    $this->tree->setReturnValue('get_node', $result = 'some result');

    $this->cache->expectOnce('put', array(array('node' => $node_id), $result, CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->get_node($node_id), $result);
  }

  function test_get_parents_cache_hit()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('parents' => $node_id),
                                          CACHING_TREE_CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('get_parents');
    $this->assertEqual($this->decorator->get_parents($node_id), $result);
  }

  function test_get_parents_cache_miss()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('parents' => $node_id), CACHING_TREE_CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('get_parents', array($node_id));
    $this->tree->setReturnValue('get_parents', $result = 'some result');

    $this->cache->expectOnce('put', array(array('parents' => $node_id), $result, CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->get_parents($node_id), $result);
  }

  function test_get_children_cache_hit()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('children' => $node_id),
                                          CACHING_TREE_CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('get_children');
    $this->assertEqual($this->decorator->get_children($node_id), $result);
  }

  function test_get_children_cache_miss()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('children' => $node_id),
                                          CACHING_TREE_CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('get_children', array($node_id));
    $this->tree->setReturnValue('get_children', $result = 'some result');

    $this->cache->expectOnce('put', array(array('children' => $node_id),
                                          $result,
                                          CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->get_children($node_id), $result);
  }

  function test_count_children_cache_hit()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('count_children' => $node_id),
                                          CACHING_TREE_CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('count_children');
    $this->assertEqual($this->decorator->count_children($node_id), $result);
  }

  function test_count_children_cache_miss()
  {
    $node_id = 100;
    $this->cache->expectOnce('get', array(array('count_children' => $node_id),
                                          CACHING_TREE_CACHE_GROUP));

    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('count_children', array($node_id));
    $this->tree->setReturnValue('count_children', $result = 'some result');

    $this->cache->expectOnce('put', array(array('count_children' => $node_id),
                                          $result,
                                          CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->count_children($node_id), $result);
  }

  function test_create_root_node()
  {
    $this->tree->setReturnValue('create_root_node', $result = 'some_result', array($values = 'whatever'));

    $this->cache->expectOnce('flush', array(CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->create_root_node($values), $result);
  }

  function test_create_sub_node()
  {
    $this->tree->setReturnValue('create_sub_node',
                                $result = 'some_result',
                                array($id = 'id',$values = 'whatever'));

    $this->cache->expectOnce('flush', array(CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->create_sub_node($id, $values), $result);
  }

  function test_delete_node()
  {
    $this->tree->setReturnValue('delete_node', $result = 'some_result', array($id = 'id'));

    $this->cache->expectOnce('flush', array(CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->delete_node($id), $result);
  }

  function test_update_node()
  {
    $this->tree->setReturnValue('update_node',
                                $result = 'some_result',
                                array($id = 'id', $values = 'whatever', false));

    $this->cache->expectOnce('flush', array(CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->update_node($id, $values), $result);
  }

  function test_move_tree()
  {
    $this->tree->setReturnValue('move_tree',
                                $result = 'some result',
                                array($id = 'id', $target_id = 'target'));

    $this->cache->expectOnce('flush', array(CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->move_tree($id, $target_id), $result);
  }

  function test_get_node_by_path_cache_hit()
  {
    $path = 'some_path';

    $this->cache->expectOnce('get', array(array('path' => $path),
                                          CACHING_TREE_CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('get_node_by_path');
    $this->assertEqual($this->decorator->get_node_by_path($path), $result);
  }

  function test_get_node_by_path_cache_miss()
  {
    $path = 'some_path';
    $delimeter = '/';
    $this->cache->expectOnce('get', array(array('path' => $path),
                                          CACHING_TREE_CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('get_node_by_path', array($path, $delimeter));
    $this->tree->setReturnValue('get_node_by_path', $result = 'some result');

    $this->cache->expectOnce('put', array(array('path' => $path),
                                          $result,
                                          CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->get_node_by_path($path,$delimeter), $result);
  }

  function test_get_sub_branch_cache_hit()
  {
    $key = array('sub_branch',
                 'node_id' => $node_id = 100,
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false,
                 'only_parents' => false);

    $this->cache->expectOnce('get', array($key, CACHING_TREE_CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('get_sub_branch');
    $this->assertEqual($this->decorator->get_sub_branch($node_id, $depth, false, false, false), $result);
  }

  function test_get_sub_branch_cache_miss()
  {
    $key = array('sub_branch',
                 'node_id' => $node_id = 100,
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false,
                 'only_parents' => false);

    $this->cache->expectOnce('get', array($key, CACHING_TREE_CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('get_sub_branch', array($node_id, $depth, false, false, false));
    $this->tree->setReturnValue('get_sub_branch', $result = 'some result');

    $this->cache->expectOnce('put', array($key, $result, CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->get_sub_branch($node_id, $depth, false, false, false), $result);
  }

  function test_get_root_nodes_cache_hit()
  {
    $this->cache->expectOnce('get', array(array('root_nodes'),
                                          CACHING_TREE_CACHE_GROUP));

    $this->cache->setReturnValue('get', $result = 'some result');

    $this->tree->expectNever('get_root_nodes');
    $this->assertEqual($this->decorator->get_root_nodes(), $result);
  }

  function test_get_root_nodes_cache_miss()
  {
    $this->cache->expectOnce('get', array(array('root_nodes'),
                                          CACHING_TREE_CACHE_GROUP));
    $this->cache->setReturnValue('get', null);

    $this->tree->expectOnce('get_root_nodes');
    $this->tree->setReturnValue('get_root_nodes', $result = 'some result');

    $this->cache->expectOnce('put', array(array('root_nodes'),
                                          $result,
                                          CACHING_TREE_CACHE_GROUP));

    $this->assertEqual($this->decorator->get_root_nodes(), $result);
  }

  function test_expand_node()
  {
    $this->cache->expectOnce('flush', array(CACHING_TREE_CACHE_GROUP));
    $this->tree->expectOnce('expand_node', array($node = 1));

    $this->decorator->expand_node($node);
  }

  function test_collapse_node()
  {
    $this->cache->expectOnce('flush', array(CACHING_TREE_CACHE_GROUP));
    $this->tree->expectOnce('collapse_node', array($node = 1));

    $this->decorator->collapse_node($node);
  }

  function test_toggle_node()
  {
    $this->cache->expectOnce('flush', array(CACHING_TREE_CACHE_GROUP));
    $this->tree->expectOnce('toggle_node', array($node = 1));

    $this->decorator->toggle_node($node);
  }
}

?>
