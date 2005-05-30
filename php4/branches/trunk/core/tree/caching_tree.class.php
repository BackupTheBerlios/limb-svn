<?php
/**********************************************************************************
* copyright 2004 BIT, _ltd. http://limb-project.com, mailto: support@limb-project.com
*
* released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: _cachingtree.class.php 1260 2005-04-20 15:10:07Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/tree/tree_decorator.class.php');

define('CACHE_REGISTRY_TREE_COMMON_GROUP', 'tree_common');
define('CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP', 'tree_accessible');

class caching_tree extends tree_decorator
{
  var $cache;
  var $current_key;
  var $current_group;

  function caching_tree(&$imp)
  {
    parent :: tree_decorator($imp);
    $this->cache =& $this->_create_cache();
  }

  function & _create_cache()
  {
    include_once(LIMB_DIR . '/core/cache/cache_registry.class.php');
    return new cache_registry();
  }

  function _use_cache_key($key, $group = null)
  {
    $this->current_key = $key;
    $this->current_group = is_null($group) ? CACHE_REGISTRY_TREE_COMMON_GROUP : $group;
  }

  function _cache_callback($method, $args = null, $key = null, $group = null)
  {
    $group = is_null($group) ? $this->current_group : $group;
    $key = is_null($key) ? $this->current_key : $key;

    if($this->cache->assign($variable, $key, $group))
      return $variable;

    $result = $this->_call_imp($method, $args);

    $this->cache->put($key, $result, $group);

    return $result;
  }

  function get_node($node)
  {
    $id = $this->_get_id_lazy($node);

    $this->_use_cache_key(array('node', $id));

    return $this->_cache_callback('get_node', array($node));
  }

  function get_all_nodes()
  {
    $this->_use_cache_key(array('all_nodes'));

    return $this->_cache_callback('get_all_nodes');
  }

  function get_nodes_by_ids($ids)
  {
    $sorted_ids = $ids;
    sort($sorted_ids);
    $this->_use_cache_key(array('ids', $sorted_ids));

    return $this->_cache_callback('get_nodes_by_ids', array($ids));
  }

  function get_parents($node)
  {
    $id = $this->_get_id_lazy($node);

    $this->_use_cache_key(array('parents', $id));

    return $this->_cache_callback('get_parents', array($node));
  }

  function get_children($node)
  {
    $id = $this->_get_id_lazy($node);

    $this->_use_cache_key(array('children', $id));

    return $this->_cache_callback('get_children', array($node));
  }

  function count_children($node)
  {
    $id = $this->_get_id_lazy($node);

    $this->_use_cache_key(array('count_children', $id));

    return $this->_cache_callback('count_children', array($node));
  }

  function get_node_by_path($path)
  {
    $this->_use_cache_key(array('path', rtrim($path, '/')));

    return $this->_cache_callback('get_node_by_path', array($path));
  }

  function get_path_to_node($node, $delimiter = '/')
  {
    $id = $this->_get_id_lazy($node);

    $this->_use_cache_key(array('path_to_node', $id));

    return $this->_cache_callback('get_path_to_node', array($node, $delimiter));
  }

  function get_sub_branch($node, $depth = -1, $include_parent = false, $normalize_expanded_parents = false, $only_parents = false)
  {
    if($normalize_expanded_parents)
      return parent :: get_sub_branch($node, $depth, $include_parent, $normalize_expanded_parents, $only_parents);

    $id = $this->_get_id_lazy($node);

    $key = array('sub_branch',
                 'node_id' => $id,
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'normalize_expanded_parents' => $normalize_expanded_parents,
                 'only_parents' => $only_parents);

    $this->_use_cache_key($key, CACHE_REGISTRY_TREE_COMMON_GROUP);

    return $this->_cache_callback('get_sub_branch',
                                  array($node, $depth, $include_parent, $normalize_expanded_parents, $only_parents));
  }

  function get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $normalize_expanded_parents = false, $only_parents = false)
  {
    if($normalize_expanded_parents)
      return parent :: get_sub_branch_by_path($path, $depth, $include_parent, $normalize_expanded_parents, $only_parents);

    $key = array('sub_branch_by_path',
                 'path' => rtrim($path, '/'),
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'normalize_expanded_parents' => $normalize_expanded_parents,
                 'only_parents' => $only_parents);

    $this->_use_cache_key($key, CACHE_REGISTRY_TREE_COMMON_GROUP);

    return $this->_cache_callback('get_sub_branch_by_path',
                                  array($path, $depth, $include_parent, $normalize_expanded_parents, $only_parents));
  }

  function get_accessible_sub_branch_by_path($path, $depth = -1, $include_parent = false, $normalize_expanded_parents = false, $class_id = null, $only_parents = false)
  {
    if($normalize_expanded_parents)
      return parent :: get_accessible_sub_branch_by_path($path, $depth, $include_parent, $normalize_expanded_parents, $class_id, $only_parents);

    $user =& user :: instance();

    $key = array('accessible_sub_branch',
                 'path' => rtrim($path, '/'),
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'normalize_expanded_parents' => $normalize_expanded_parents,
                 'class_id' => $class_id,
                 'only_parents' => $only_parents,
                 'user_id' => $user->get_id(),
                 'user_groups' => $user->get_groups());

    $this->_use_cache_key($key, CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP);

    return $this->_cache_callback('get_accessible_sub_branch_by_path',
                                  array($path, $depth, $include_parent, $normalize_expanded_parents, $class_id, $only_parents));
  }

  function count_accessible_children($node)
  {
    $id = $this->_get_id_lazy($node);

    $user =& user :: instance();

    $this->_use_cache_key(array('count_accessible_children',
                                $id,
                                $user->get_id(),
                                $user->get_groups()),
                          CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP);

    return $this->_cache_callback('count_accessible_children', array($node));
  }

  function get_root_nodes()
  {
    $this->_use_cache_key(array('root_nodes'));

    return $this->_cache_callback('get_root_nodes');
  }

  function create_root_node($values)
  {
    $this->flush_cache();
    return parent :: create_root_node($values);
  }

  function create_sub_node($id, $values)
  {
    $this->flush_cache();
    return parent :: create_sub_node($id, $values);
  }

  function delete_node($id)
  {
    $this->flush_cache();
    return parent :: delete_node($id);
  }

  function update_node($id, $values, $internal = false)
  {
    $this->flush_cache();
    return parent :: update_node($id, $values, $internal);
  }

  function move_tree($source_node, $target_node)
  {
    $this->flush_cache();
    return parent :: move_tree($source_node, $target_node);
  }

  function flush_cache($group = null)
  {
    if(is_null($group))
    {
      $this->cache->flush_group(CACHE_REGISTRY_TREE_COMMON_GROUP);
      $this->cache->flush_group(CACHE_REGISTRY_TREE_ACCESSIBLE_GROUP);
    }
    else
      $this->cache->flush_group($group);
  }

  function _get_id_lazy($node)
  {
    if(is_array($node))
      return $node['id'];
    else
      return $node;
  }
}

?>