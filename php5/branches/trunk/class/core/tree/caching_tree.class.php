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
require_once(LIMB_DIR . '/class/core/tree/tree_decorator.class.php');

class caching_tree extends tree_decorator
{
  const CACHE_GROUP = 'tree';

  protected $cache;

  function __construct($tree)
  {
    parent :: __construct($tree);

    $this->cache = Limb :: toolkit()->getCache();
  }

  public function get_node($id)
  {
    if($node = $this->cache->get(array('node' => $id), self :: CACHE_GROUP))
      return $node;

    $node = $this->_tree->get_node($id);

    $this->cache->put(array('node' => $id), $node, self :: CACHE_GROUP);

    return $node;
  }

  public function get_parents($id)
  {
    if($node = $this->cache->get(array('parents' => $id), self :: CACHE_GROUP))
      return $node;

    $parents = $this->_tree->get_parents($id);

    $this->cache->put(array('parents' => $id), $parents, self :: CACHE_GROUP);

    return $parents;
  }

  public function get_children($id)
  {
    if($node = $this->cache->get(array('children' => $id), self :: CACHE_GROUP))
      return $node;

    $children = $this->_tree->get_children($id);

    $this->cache->put(array('children' => $id), $children, self :: CACHE_GROUP);

    return $children;
  }

  public function count_children($id)
  {
    if($node = $this->cache->get(array('count_children' => $id), self :: CACHE_GROUP))
      return $node;

    $count = $this->_tree->count_children($id);

    $this->cache->put(array('count_children' => $id), $count, self :: CACHE_GROUP);

    return $count;
  }

  public function create_root_node($values)
  {
    $result = parent :: create_root_node($values);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function create_sub_node($id, $values)
  {
    $result = parent :: create_sub_node($id, $values);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function delete_node($id)
  {
    $result = parent :: delete_node($id);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function update_node($id, $values, $internal = false)
  {
    $result = parent :: update_node($id, $values, $internal);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function move_tree($id, $target_id)
  {
    $result = parent :: move_tree($id, $target_id);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function get_node_by_path($path, $delimiter='/')
  {
    if($node = $this->cache->get(array('path' => $path), self :: CACHE_GROUP))
      return $node;

    $node = $this->_tree->get_node_by_path($path, $delimiter);

    $this->cache->put(array('path' => $path), $node, self :: CACHE_GROUP);

    return $node;
  }

  public function get_sub_branch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    $key = array('sub_branch',
                 'node_id' => $id,
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'check_expanded_parents' => $check_expanded_parents);

    if($node = $this->cache->get($key, self :: CACHE_GROUP))
      return $node;

    $nodes = $this->_tree->get_sub_branch($id, $depth, $include_parent, $check_expanded_parents);

    $this->cache->put($key, $nodes, self :: CACHE_GROUP);

    return $nodes;
  }

  public function get_root_nodes()
  {
    if($nodes = $this->cache->get(array('root_nodes'), self :: CACHE_GROUP))
      return $nodes;

    $nodes = $this->_tree->get_root_nodes();

    $this->cache->put(array('root_nodes'), $nodes, self :: CACHE_GROUP);

    return $nodes;
  }
}

?>