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
require_once(LIMB_DIR . '/class/core/tree/TreeDecorator.class.php');

class CachingTree extends TreeDecorator
{
  const CACHE_GROUP = 'tree';

  protected $cache;

  function __construct($tree)
  {
    parent :: __construct($tree);

    $this->cache = Limb :: toolkit()->getCache();
  }

  public function getNode($id)
  {
    if($node = $this->cache->get(array('node' => $id), self :: CACHE_GROUP))
      return $node;

    $node = $this->_tree->getNode($id);

    $this->cache->put(array('node' => $id), $node, self :: CACHE_GROUP);

    return $node;
  }

  public function getParents($id)
  {
    if($node = $this->cache->get(array('parents' => $id), self :: CACHE_GROUP))
      return $node;

    $parents = $this->_tree->getParents($id);

    $this->cache->put(array('parents' => $id), $parents, self :: CACHE_GROUP);

    return $parents;
  }

  public function getChildren($id)
  {
    if($node = $this->cache->get(array('children' => $id), self :: CACHE_GROUP))
      return $node;

    $children = $this->_tree->getChildren($id);

    $this->cache->put(array('children' => $id), $children, self :: CACHE_GROUP);

    return $children;
  }

  public function countChildren($id)
  {
    if($node = $this->cache->get(array('count_children' => $id), self :: CACHE_GROUP))
      return $node;

    $count = $this->_tree->countChildren($id);

    $this->cache->put(array('count_children' => $id), $count, self :: CACHE_GROUP);

    return $count;
  }

  public function createRootNode($values)
  {
    $result = parent :: createRootNode($values);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function createSubNode($id, $values)
  {
    $result = parent :: createSubNode($id, $values);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function deleteNode($id)
  {
    $result = parent :: deleteNode($id);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function updateNode($id, $values, $internal = false)
  {
    $result = parent :: updateNode($id, $values, $internal);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function moveTree($id, $target_id)
  {
    $result = parent :: moveTree($id, $target_id);

    $this->cache->flush(self :: CACHE_GROUP);

    return $result;
  }

  public function getNodeByPath($path, $delimiter='/')
  {
    if($node = $this->cache->get(array('path' => $path), self :: CACHE_GROUP))
      return $node;

    $node = $this->_tree->getNodeByPath($path, $delimiter);

    $this->cache->put(array('path' => $path), $node, self :: CACHE_GROUP);

    return $node;
  }

  public function getSubBranch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    $key = array('sub_branch',
                 'node_id' => $id,
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'check_expanded_parents' => $check_expanded_parents);

    if($node = $this->cache->get($key, self :: CACHE_GROUP))
      return $node;

    $nodes = $this->_tree->getSubBranch($id, $depth, $include_parent, $check_expanded_parents);

    $this->cache->put($key, $nodes, self :: CACHE_GROUP);

    return $nodes;
  }

  public function getRootNodes()
  {
    if($nodes = $this->cache->get(array('root_nodes'), self :: CACHE_GROUP))
      return $nodes;

    $nodes = $this->_tree->getRootNodes();

    $this->cache->put(array('root_nodes'), $nodes, self :: CACHE_GROUP);

    return $nodes;
  }
}

?>