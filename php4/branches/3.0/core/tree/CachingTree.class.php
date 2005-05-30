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

define('CACHING_TREE_COMMON_GROUP', 'tree');

class CachingTree extends TreeDecorator
{
  var $cache;
  var $current_key;
  var $current_group;

  function CachingTree(&$tree)
  {
    parent :: TreeDecorator($tree);

    $toolkit =& Limb :: toolkit();
    $this->cache =& $toolkit->getCache();
  }

  function getNode($node)
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('node', $id));
    return $this->_cacheCallback('getNode', array($node));
  }

  function getParents($node)
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('parents', $id));
    return $this->_cacheCallback('getParents', array($node));
  }

  function getChildren($node)
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('children', $id));
    return $this->_cacheCallback('getChildren', array($node));
  }

  function countChildren($node)
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('count_children', $id));
    return $this->_cacheCallback('countChildren', array($node));
  }

  function getNodesByIds($ids)
  {
    $sorted_ids = $ids;
    sort($sorted_ids);
    $this->_useCacheKey(array('ids', $sorted_ids));

    return $this->_cacheCallback('getNodesByIds', array($ids));
  }

  function getPathToNode($node, $delimiter = '/')
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('path_to_node', $id));
    return $this->_cacheCallback('getPathToNode', array($node, $delimiter));
  }

  function getNodeByPath($path)
  {
    $this->_useCacheKey(array('path', rtrim($path, '/')));
    return $this->_cacheCallback('getNodeByPath', array($path));
  }

  function getAllNodes()
  {
    $this->_useCacheKey(array('all_nodes'));
    return $this->_cacheCallback('getAllNodes');
  }

  function getRootNodes()
  {
    $this->_useCacheKey(array('root_nodes'));
    return $this->_cacheCallback('getRootNodes');
  }

  function getSubBranch($node, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    if($check_expanded_parents)
      return parent :: getSubBranch($node, $depth, $include_parent, $check_expanded_parents);

    $id = $this->_getIdLazy($node);

    $key = array('sub_branch',
                 'node_id' => $id,
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'check_expanded_parents' => $check_expanded_parents);

    $this->_useCacheKey($key, CACHING_TREE_COMMON_GROUP);

    return $this->_cacheCallback('getSubBranch',
                                  array($node, $depth, $include_parent, $check_expanded_parents));
  }

  function getSubBranchByPath($path, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    if($check_expanded_parents)
      return $this->_tree->getSubBranchByPath($path, $depth, $include_parent, $check_expanded_parents);

    $key = array('sub_branch_by_path',
                 'path' => rtrim($path, '/'),
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'check_expanded_parents' => $check_expanded_parents);

    $this->_useCacheKey($key, CACHING_TREE_COMMON_GROUP);

    return $this->_cacheCallback('getSubBranchByPath',
                                  array($path, $depth, $include_parent, $check_expanded_parents));
  }

  function createRootNode($values)
  {
    $result = parent :: createRootNode($values);
    $this->flushCache();
    return $result;
  }

  function createSubNode($id, $values)
  {
    $result = parent :: createSubNode($id, $values);
    $this->flushCache();
    return $result;
  }

  function deleteNode($id)
  {
    $result = parent :: deleteNode($id);
    $this->flushCache();
    return $result;
  }

  function updateNode($id, $values, $internal = false)
  {
    $result = parent :: updateNode($id, $values, $internal);
    $this->flushCache();
    return $result;
  }

  function moveTree($id, $target_id)
  {
    $result = parent :: moveTree($id, $target_id);
    $this->flushCache();
    return $result;
  }

  function _useCacheKey($key, $group = null)
  {
    $this->current_key = $key;
    $this->current_group = is_null($group) ? CACHING_TREE_COMMON_GROUP : $group;
  }

  function flushCache($group = null)
  {
    if(is_null($group))
      $this->cache->flushGroup(CACHING_TREE_COMMON_GROUP);
    else
      $this->cache->flushGroup($group);
  }

  function _cacheCallback($method, $args = null, $key = null, $group = null)
  {
    $group = is_null($group) ? $this->current_group : $group;
    $key = is_null($key) ? $this->current_key : $key;

    $variable = null;
    if($this->cache->assign($variable, $key, $group))
      return $variable;

    $result = $this->_callImp($method, $args);

    $this->cache->put($key, $result, $group);

    return $result;
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