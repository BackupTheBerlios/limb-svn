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

class cache_registry_special_version extends Mockcache_registry
{
  var $result;

  function assign(&$variable, $raw_key, $group)
  {
    $res = parent :: assign($variable, $raw_key, $group);
    $variable = $this->result;
    return $res;
  }
}

class caching_tree_test extends LimbTestCase
{
  var $tree;
  var $cache;
  var $decorator;

  function setUp()
  {
    $this->tree = new Mocktree($this);
    $this->cache = new cache_registry_special_version($this);

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
    $this->_test_cache_hit(array('get_node', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('node', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_node_cache_miss()
  {
    $this->_test_cache_miss(array('get_node', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('node', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_parents_cache_hit()
  {
    $this->_test_cache_hit(array('get_parents', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('parents', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_parents_cache_miss()
  {
    $this->_test_cache_miss(array('get_parents', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('parents', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_children_cache_hit()
  {
    $this->_test_cache_hit(array('get_children', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('children', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_children_cache_miss()
  {
    $this->_test_cache_miss(array('get_children', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('children', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_count_children_cache_hit()
  {
    $this->_test_cache_hit(array('count_children', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('count_children', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_count_children_cache_miss()
  {
    $this->_test_cache_miss(array('count_children', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('count_children', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_count_accessible_children_cache_hit()
  {
    $user =& user :: instance();

    $this->_test_cache_hit(array('count_accessible_children', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('count_accessible_children',
                                        $node_id,
                                        $user->get_id(),
                                        $user->get_groups()),
                           CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP);
  }

  function test_count_accessible_children_cache_miss()
  {
    $user =& user :: instance();

    $this->_test_cache_miss(array('count_accessible_children', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('count_accessible_children',
                                        $node_id,
                                        $user->get_id(),
                                        $user->get_groups()),
                           CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP);
  }

  function test_get_nodes_by_ids_cache_hit()
  {
    //sorting ids
    $this->_test_cache_hit(array('get_nodes_by_ids', array(array(1, 3, 2))),
                           $result = 'whatever',
                           $key = array('ids', array(1, 2, 3)),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_nodes_by_ids_cache_miss()
  {
    //sorting ids
    $this->_test_cache_miss(array('get_nodes_by_ids', array(array(1, 3, 2))),
                           $result = 'whatever',
                           $key = array('ids', array(1, 2, 3)),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_path_to_node_cache_hit()
  {
    $this->_test_cache_hit(array('get_path_to_node', array($node_id = 100, $delim = '|')),
                           $result = 'whatever',
                           $key = array('path_to_node', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_path_to_node_cache_miss()
  {
    $this->_test_cache_miss(array('get_path_to_node', array($node_id = 100, $delim = '|')),
                           $result = 'whatever',
                           $key = array('path_to_node', $node_id),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_node_by_path_cache_hit()
  {
    //trimming trailing slash
    $this->_test_cache_hit(array('get_node_by_path', array('path/')),
                           $result = 'whatever',
                           $key = array('path', 'path'),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_node_by_path_cache_miss()
  {
    //trimming trailing slash
    $this->_test_cache_miss(array('get_node_by_path', array('path/')),
                           $result = 'whatever',
                           $key = array('path', 'path'),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_all_nodes_cache_hit()
  {
    $this->_test_cache_hit(array('get_all_nodes'),
                           $result = 'whatever',
                           $key = array('all_nodes'),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_all_nodes_cache_miss()
  {
    $this->_test_cache_miss(array('get_all_nodes'),
                           $result = 'whatever',
                           $key = array('all_nodes'),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_sub_branch_cache_hit()
  {
    $key = array('sub_branch',
                 'node_id' => $node_id = 100,
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false,
                 'only_parents' => false);

    $this->_test_cache_hit(array('get_sub_branch', array($node_id, $depth, false, false, false)),
                           $result = 'whatever',
                           $key,
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_sub_branch_cache_miss()
  {
    $key = array('sub_branch',
                 'node_id' => $node_id = 100,
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false,
                 'only_parents' => false);

    $this->_test_cache_miss(array('get_sub_branch', array($node_id, $depth, false, false, false)),
                           $result = 'whatever',
                           $key,
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_sub_branch_dont_cache_expanded_parents()
  {
    $this->_assert_cache_not_called();

    $this->tree->expectOnce('get_sub_branch');
    $this->tree->setReturnValue('get_sub_branch',
                                $expected = 'result',
                                array($node_id = 100, $depth = -1, false, $check_expanded_parents = true, false));

    $result = $this->decorator->get_sub_branch($node_id, $depth, false, $check_expanded_parents, false);

    $this->assertEqual($result, $expected);
  }

  function test_get_sub_branch_by_path_cache_hit()
  {
    //trimming trailing slash
    $key = array('sub_branch_by_path',
                 'path' => 'path',
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false,
                 'only_parents' => false);

    $this->_test_cache_hit(array('get_sub_branch_by_path', array('path/', $depth, false, false, false)),
                           $result = 'whatever',
                           $key,
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_sub_branch_by_path_cache_miss()
  {
    //trimming trailing slash
    $key = array('sub_branch_by_path',
                 'path' => 'path',
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false,
                 'only_parents' => false);

    $this->_test_cache_miss(array('get_sub_branch_by_path', array('path/', $depth, false, false, false)),
                           $result = 'whatever',
                           $key,
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_sub_branch_by_path_dont_cache_expanded_parents()
  {
    $this->_assert_cache_not_called();

    $this->tree->expectOnce('get_sub_branch_by_path');
    $this->tree->setReturnValue('get_sub_branch_by_path',
                                $expected = 'result',
                                array($path = '/', $depth = -1, false, $check_expanded_parents = true, false));

    $result = $this->decorator->get_sub_branch_by_path($path, $depth, false, $check_expanded_parents, false);

    $this->assertEqual($result, $expected);
  }

  function test_get_accessible_sub_branch_by_path_cache_hit()
  {
    $user =& user :: instance();

    //trimming trailing slash
    $key = array('accessible_sub_branch',
                 'path' => 'path',
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false,
                 'class_id' => $class_id = 10,
                 'only_parents' => false,
                 'user_id' => $user->get_id(),
                 'user_groups' => $user->get_groups());

    $this->_test_cache_hit(array('get_accessible_sub_branch_by_path', array('path/', $depth, false, false, $class_id, false)),
                           $result = 'whatever',
                           $key,
                           CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP);
  }

  function test_get_accessible_sub_branch_by_path_cache_miss()
  {
    $user =& user :: instance();

    //trimming trailing slash
    $key = array('accessible_sub_branch',
                 'path' => 'path',
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false,
                 'class_id' => $class_id = 10,
                 'only_parents' => false,
                 'user_id' => $user->get_id(),
                 'user_groups' => $user->get_groups());

    $this->_test_cache_miss(array('get_accessible_sub_branch_by_path', array('path/', $depth, false, false, $class_id, false)),
                           $result = 'whatever',
                           $key,
                           CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP);
  }

  function test_get_accessible_sub_branch_by_path_dont_cache_expanded_parents()
  {
    $this->_assert_cache_not_called();

    $this->tree->expectOnce('get_accessible_sub_branch_by_path');
    $this->tree->setReturnValue('get_accessible_sub_branch_by_path',
                                $expected = 'result',
                                array($path = '/', $depth = -1, false, $check_expanded_parents = true, $class_id = 1, false));

    $result = $this->decorator->get_accessible_sub_branch_by_path($path, $depth, false, $check_expanded_parents, $class_id, false);

    $this->assertEqual($result, $expected);
  }

  function test_get_root_nodes_cache_hit()
  {
    $this->_test_cache_hit(array('get_root_nodes'),
                           $result = 'whatever',
                           $key = array('root_nodes'),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_get_root_nodes_cache_miss()
  {
    $this->_test_cache_miss(array('get_root_nodes'),
                           $result = 'whatever',
                           $key = array('root_nodes'),
                           CACHE_REGISTRY_TREE_COMMON_GROUP);
  }

  function test_create_root_node()
  {
    $this->tree->setReturnValue('create_root_node', $result = 'some_result', array($values = 'whatever'));

    $this->cache->expectArgumentsAt(0, 'flush_group', array(CACHE_REGISTRY_TREE_COMMON_GROUP));
    $this->cache->expectArgumentsAt(1, 'flush_group', array(CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP));

    $this->assertEqual($this->decorator->create_root_node($values), $result);
  }

  function test_create_sub_node()
  {
    $this->tree->setReturnValue('create_sub_node',
                                $result = 'some_result',
                                array($id = 'id',$values = 'whatever'));

    $this->cache->expectArgumentsAt(0, 'flush_group', array(CACHE_REGISTRY_TREE_COMMON_GROUP));
    $this->cache->expectArgumentsAt(1, 'flush_group', array(CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP));

    $this->assertEqual($this->decorator->create_sub_node($id, $values), $result);
  }

  function test_delete_node()
  {
    $this->tree->setReturnValue('delete_node', $result = 'some_result', array($id = 'id'));

    $this->cache->expectArgumentsAt(0, 'flush_group', array(CACHE_REGISTRY_TREE_COMMON_GROUP));
    $this->cache->expectArgumentsAt(1, 'flush_group', array(CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP));

    $this->assertEqual($this->decorator->delete_node($id), $result);
  }

  function test_update_node()
  {
    $this->tree->setReturnValue('update_node',
                                $result = 'some_result',
                                array($id = 'id', $values = 'whatever', false));

    $this->cache->expectArgumentsAt(0, 'flush_group', array(CACHE_REGISTRY_TREE_COMMON_GROUP));
    $this->cache->expectArgumentsAt(1, 'flush_group', array(CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP));

    $this->assertEqual($this->decorator->update_node($id, $values), $result);
  }

  function test_move_tree()
  {
    $this->tree->setReturnValue('move_tree',
                                $result = 'some result',
                                array($id = 'id', $target_id = 'target'));

    $this->cache->expectArgumentsAt(0, 'flush_group', array(CACHE_REGISTRY_TREE_COMMON_GROUP));
    $this->cache->expectArgumentsAt(1, 'flush_group', array(CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP));

    $this->assertEqual($this->decorator->move_tree($id, $target_id), $result);
  }

  function test_expand_node()
  {
    $this->tree->expectOnce('expand_node', array($node = 1));

    $this->decorator->expand_node($node);
  }

  function test_collapse_node()
  {
    $this->tree->expectOnce('collapse_node', array($node = 1));

    $this->decorator->collapse_node($node);
  }

  function test_toggle_node()
  {
    $this->tree->expectOnce('toggle_node', array($node = 1));

    $this->decorator->toggle_node($node);
  }

  function _test_cache_hit($callback, $expected, $key, $group)
  {
    $this->cache->result = $expected;
    $this->cache->expectOnce('assign', array(null, $key, $group));
    $this->cache->setReturnValue('assign', true);

    $this->cache->setReturnValue('get', $expected);

    $this->tree->expectNever($callback[0]);
    $result = $this->_call_decorator($callback);
    $this->assertEqual($result, $expected);
  }

  function _assert_cache_not_called()
  {
    $this->cache->expectNever('assign');
    $this->cache->expectNever('put');
    $this->cache->expectNever('get');
  }

  function _test_cache_miss($callback, $expected, $key, $group)
  {
    $this->cache->expectOnce('assign', array(null, $key, $group));
    $this->cache->setReturnValue('assign', false);

    $this->tree->expectOnce($callback[0]);
    $this->tree->setReturnValue($callback[0], $expected, isset($callback[1]) ? $callback[1] : false);

    $this->cache->expectOnce('put', array($key, $expected, $group));

    $result = $this->_call_decorator($callback);
    $this->assertEqual($result, $expected);
  }

  function _call_decorator($callback)
  {
    return call_user_func_array(array(&$this->decorator, $callback[0]),
                                isset($callback[1]) ? $callback[1] : null);
  }
}

?>
