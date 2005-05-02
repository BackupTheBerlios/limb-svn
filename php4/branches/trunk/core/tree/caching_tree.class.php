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

define('CACHING_TREE_CACHE_GROUP', 'tree');

class caching_tree extends tree_decorator
{
  var $cache;

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

  function get_node($lazy_node)
  {
    $id = $this->_get_id_lazy($lazy_node);

    if($node = $this->cache->get(array('node' => $id), CACHING_TREE_CACHE_GROUP))
      return $node;

    $node = $this->tree_imp->get_node($lazy_node);

    $this->cache->put(array('node' => $id), $node, CACHING_TREE_CACHE_GROUP);

    return $node;
  }

  function get_parents($lazy_node)
  {
    $id = $this->_get_id_lazy($lazy_node);

    if($parents = $this->cache->get(array('parents' => $id), CACHING_TREE_CACHE_GROUP))
      return $parents;

    $parents = $this->tree_imp->get_parents($lazy_node);

    $this->cache->put(array('parents' => $id), $parents, CACHING_TREE_CACHE_GROUP);

    return $parents;
  }

  function get_children($lazy_node)
  {
    $id = $this->_get_id_lazy($lazy_node);

    if($children = $this->cache->get(array('children' => $id), CACHING_TREE_CACHE_GROUP))
      return $children;

    $children = $this->tree_imp->get_children($lazy_node);

    $this->cache->put(array('children' => $id), $children, CACHING_TREE_CACHE_GROUP);

    return $children;
  }

  function count_children($lazy_node)
  {
    $id = $this->_get_id_lazy($lazy_node);

    if($count = $this->cache->get(array('count_children' => $id), CACHING_TREE_CACHE_GROUP))
      return $count;

    $count = $this->tree_imp->count_children($lazy_node);

    $this->cache->put(array('count_children' => $id), $count, CACHING_TREE_CACHE_GROUP);

    return $count;
  }

  function create_root_node($values)
  {
    $result = $this->tree_imp->create_root_node($values);

    $this->flush_cache();

    return $result;
  }

  function create_sub_node($id, $values)
  {
    $result = $this->tree_imp->create_sub_node($id, $values);

    $this->flush_cache();

    return $result;
  }

  function delete_node($id)
  {
    $result = $this->tree_imp->delete_node($id);

    $this->flush_cache();

    return $result;
  }

  function update_node($id, $values, $internal = false)
  {
    $result = $this->tree_imp->update_node($id, $values, $internal);

    $this->flush_cache();

    return $result;
  }

  function move_tree($source_node, $target_node)
  {
    $result = $this->tree_imp->move_tree($source_node, $target_node);

    $this->flush_cache();

    return $result;
  }

  function get_node_by_path($path, $delimiter='/')
  {
    if($node = $this->cache->get(array('path' => $path), CACHING_TREE_CACHE_GROUP))
      return $node;

    $node = $this->tree_imp->get_node_by_path($path, $delimiter);

    $this->cache->put(array('path' => $path), $node, CACHING_TREE_CACHE_GROUP);

    return $node;
  }

  function get_sub_branch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false)
  {
    $key = array('sub_branch',
                 'node_id' => $id,
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'check_expanded_parents' => $check_expanded_parents,
                 'only_parents' => $only_parents);

    if($node = $this->cache->get($key, CACHING_TREE_CACHE_GROUP))
      return $node;

    $nodes = $this->tree_imp->get_sub_branch($id, $depth, $include_parent, $check_expanded_parents, $only_parents);

    $this->cache->put($key, $nodes, CACHING_TREE_CACHE_GROUP);

    return $nodes;
  }

  function get_root_nodes()
  {
    if($nodes = $this->cache->get(array('root_nodes'), CACHING_TREE_CACHE_GROUP))
      return $nodes;

    $nodes = $this->tree_imp->get_root_nodes();

    $this->cache->put(array('root_nodes'), $nodes, CACHING_TREE_CACHE_GROUP);

    return $nodes;
  }

  function collapse_node($node)
  {
    $this->flush_cache();

    return $this->tree_imp->collapse_node($node);
  }

  function expand_node($node)
  {
    $this->flush_cache();

    return $this->tree_imp->expand_node($node);
  }

  function toggle_node($node)
  {
    $this->flush_cache();

    return $this->tree_imp->toggle_node($node);
  }

  function flush_cache()
  {
    $this->cache->flush(CACHING_TREE_CACHE_GROUP);
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