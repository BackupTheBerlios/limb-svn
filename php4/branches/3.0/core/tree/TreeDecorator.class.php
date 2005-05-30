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

class TreeDecorator// implements Tree
{
  var $_tree = null;

  function TreeDecorator(&$tree)
  {
    $this->_tree =& $tree;
  }

  function _callImp($method, $args = null)
  {
    return call_user_func_array(array(&$this->_tree, $method),
                                isset($args) ? $args : null);
  }

  function isNode($node)
  {
    return $this->_tree->isNode($node);
  }

  function getNode($node)
  {
    return $this->_tree->getNode($node);
  }

  function getParent($node)
  {
    return $this->_tree->getParent($node);
  }

  function getParents($node)
  {
    return $this->_tree->getParents($node);
  }

  function getSiblings($node)
  {
    return $this->_tree->getSiblings($node);
  }

  function getChildren($node)
  {
    return $this->_tree->getChildren($node);
  }

  function countChildren($node)
  {
    return $this->_tree->countChildren($node);
  }

  function createRootNode($values)
  {
    return $this->_tree->createRootNode($values);
  }

  function createSubNode($node, $values)
  {
    return $this->_tree->createSubNode($node, $values);
  }

  function deleteNode($node)
  {
    return $this->_tree->deleteNode($node);
  }

  function updateNode($node, $values, $internal = false)
  {
    return $this->_tree->updateNode($node, $values, $internal);
  }

  function moveTree($node, $target_node)
  {
    return $this->_tree->moveTree($node, $target_node);
  }

  function setDumbMode($status=true)
  {
    $this->_tree->setDumbMode($status);
  }

  function getAllNodes()
  {
    return $this->_tree->getAllNodes();
  }

  function getNodesByIds($ids_array)
  {
    return $this->_tree->getNodesByIds($ids_array);
  }

  function getPathToNode($node, $delimeter = '/')
  {
    return $this->_tree->getPathToNode($node, $delimeter);
  }

  function getMaxChildIdentifier($node)
  {
    return $this->_tree->getMaxChildIdentifier($node);
  }

  function getNodeByPath($path, $delimiter='/')
  {
    return $this->_tree->getNodeByPath($path, $delimiter);
  }

  function getSubBranch($node, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    return $this->_tree->getSubBranch($node, $depth, $include_parent, $check_expanded_parents);
  }

  function getSubBranchByPath($path, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    return $this->_tree->getSubBranchByPath($path, $depth, $include_parent, $check_expanded_parents);
  }

  function getRootNodes()
  {
    return $this->_tree->getRootNodes();
  }

  function isNodeExpanded($node)
  {
    return $this->_tree->isNodeExpanded($node);
  }

  function toggleNode($node)
  {
    return $this->_tree->toggleNode($node);
  }

  function expandNode($node)
  {
    return $this->_tree->expandNode($node);
  }

  function collapseNode($node)
  {
    return $this->_tree->collapseNode($node);
  }

  function canAddNode($node)
  {
    return $this->_tree->canAddNode($node);
  }

  function canDeleteNode($node)
  {
    return $this->_tree->canDeleteNode($node);
  }

  function initializeExpandedParents()
  {
    return $this->_tree->initializeExpandedParents();
  }

  function normalizeExpandedParents()
  {
    return $this->_tree->normalizeExpandedParents();
  }

  function setExpandedParents(&$p)
  {
    return $this->_tree->setExpandedParents($p);
  }

  function syncExpandedParents()
  {
    return $this->_tree->syncExpandedParents();
  }

  function updateExpandedParents()
  {
    return $this->_tree->updateExpandedParents();
  }
}

?>