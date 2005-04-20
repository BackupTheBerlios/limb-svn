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
require_once(LIMB_DIR . '/core/tree/TreeDecorator.class.php');

define('CACHING_TREE_CACHE_GROUP', 'tree');

class CachingTree extends TreeDecorator
{
  var $cache;

  function CachingTree(&$tree)
  {
    parent :: TreeDecorator($tree);

    $toolkit =& Limb :: toolkit();
    $this->cache =& $toolkit->getCache();
  }

  function getNode($lazy_node)
  {
    $id = $this->_getIdLazy($lazy_node);

    if($node = $this->cache->get(array('node' => $id), CACHING_TREE_CACHE_GROUP))
      return $node;

    $node = $this->_tree->getNode($lazy_node);

    $this->cache->put(array('node' => $id), $node, CACHING_TREE_CACHE_GROUP);

    return $node;
  }

  function getParents($lazy_node)
  {
    $id = $this->_getIdLazy($lazy_node);

    if($parents = $this->cache->get(array('parents' => $id), CACHING_TREE_CACHE_GROUP))
      return $parents;

    $parents = $this->_tree->getParents($lazy_node);

    $this->cache->put(array('parents' => $id), $parents, CACHING_TREE_CACHE_GROUP);

    return $parents;
  }

  function getChildren($lazy_node)
  {
    $id = $this->_getIdLazy($lazy_node);

    if($children = $this->cache->get(array('children' => $id), CACHING_TREE_CACHE_GROUP))
      return $children;

    $children = $this->_tree->getChildren($lazy_node);

    $this->cache->put(array('children' => $id), $children, CACHING_TREE_CACHE_GROUP);

    return $children;
  }

  function countChildren($lazy_node)
  {
    $id = $this->_getIdLazy($lazy_node);

    if($count = $this->cache->get(array('count_children' => $id), CACHING_TREE_CACHE_GROUP))
      return $count;

    $count = $this->_tree->countChildren($lazy_node);

    $this->cache->put(array('count_children' => $id), $count, CACHING_TREE_CACHE_GROUP);

    return $count;
  }

  function createRootNode($values)
  {
    $result = parent :: createRootNode($values);

    $this->cache->flush(CACHING_TREE_CACHE_GROUP);

    return $result;
  }

  function createSubNode($id, $values)
  {
    $result = parent :: createSubNode($id, $values);

    $this->cache->flush(CACHING_TREE_CACHE_GROUP);

    return $result;
  }

  function deleteNode($id)
  {
    $result = parent :: deleteNode($id);

    $this->cache->flush(CACHING_TREE_CACHE_GROUP);

    return $result;
  }

  function updateNode($id, $values, $internal = false)
  {
    $result = parent :: updateNode($id, $values, $internal);

    $this->cache->flush(CACHING_TREE_CACHE_GROUP);

    return $result;
  }

  function moveTree($id, $target_id)
  {
    $result = parent :: moveTree($id, $target_id);

    $this->cache->flush(CACHING_TREE_CACHE_GROUP);

    return $result;
  }

  function getNodeByPath($path, $delimiter='/')
  {
    if($node = $this->cache->get(array('path' => $path), CACHING_TREE_CACHE_GROUP))
      return $node;

    $node = $this->_tree->getNodeByPath($path, $delimiter);

    $this->cache->put(array('path' => $path), $node, CACHING_TREE_CACHE_GROUP);

    return $node;
  }

  function getSubBranch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    $key = array('sub_branch',
                 'node_id' => $id,
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'check_expanded_parents' => $check_expanded_parents);

    if($node = $this->cache->get($key, CACHING_TREE_CACHE_GROUP))
      return $node;

    $nodes = $this->_tree->getSubBranch($id, $depth, $include_parent, $check_expanded_parents);

    $this->cache->put($key, $nodes, CACHING_TREE_CACHE_GROUP);

    return $nodes;
  }

  function getRootNodes()
  {
    if($nodes = $this->cache->get(array('root_nodes'), CACHING_TREE_CACHE_GROUP))
      return $nodes;

    $nodes = $this->_tree->getRootNodes();

    $this->cache->put(array('root_nodes'), $nodes, CACHING_TREE_CACHE_GROUP);

    return $nodes;
  }

  function _getIdLazy($node)
  {
    if(is_array($node))
      return $node['id'];
    else
      return $node;
  }
}

?>