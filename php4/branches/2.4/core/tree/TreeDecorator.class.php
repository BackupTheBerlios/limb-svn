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

  function isNode($id)
  {
    return $this->_tree->isNode($id);
  }

  function getNode($id)
  {
    return $this->_tree->getNode($id);
  }

  function getParent($id)
  {
    return $this->_tree->getParent($id);
  }

  function getParents($id)
  {
    return $this->_tree->getParents($id);
  }

  function getSiblings($id)
  {
    return $this->_tree->getSiblings($id);
  }

  function getChildren($id)
  {
    return $this->_tree->getChildren($id);
  }

  function countChildren($id)
  {
    return $this->_tree->countChildren($id);
  }

  function createRootNode($values)
  {
    return $this->_tree->createRootNode($values);
  }

  function createSubNode($id, $values)
  {
    return $this->_tree->createSubNode($id, $values);
  }

  function deleteNode($id)
  {
    return $this->_tree->deleteNode($id);
  }

  function updateNode($id, $values, $internal = false)
  {
    return $this->_tree->updateNode($id, $values, $internal);
  }

  function moveTree($id, $target_id)
  {
    return $this->_tree->moveTree($id, $target_id);
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
    if(($parents = $this->_tree->getParents($node['id'])) === false)
      return false;

    $path = '';
    foreach($parents as $parent_data)
      $path .= $delimeter . $parent_data['identifier'];

    return $path .= $delimeter . $node['identifier'];
  }

  function getMaxChildIdentifier($id)
  {
    return $this->_tree->getMaxChildIdentifier($id);
  }

  function getNodeByPath($path, $delimiter='/')
  {
    return $this->_tree->getNodeByPath($path, $delimiter);
  }

  function getSubBranch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    return $this->_tree->getSubBranch($id, $depth, $include_parent, $check_expanded_parents);
  }

  function getSubBranchByPath($path, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    return $this->_tree->getSubBranchByPath($path, $depth, $include_parent, $check_expanded_parents);
  }

  function getRootNodes()
  {
    return $this->_tree->getRootNodes();
  }

  function isNodeExpanded($id)
  {
    return $this->_tree->isNodeExpanded($id);
  }

  function toggleNode($id)
  {
    return $this->_tree->toggleNode($id);
  }

  function expandNode($id)
  {
    return $this->_tree->expandNode($id);
  }

  function collapseNode($id)
  {
    return $this->_tree->collapseNode($id);
  }

  function canAddNode($id)
  {
    if (!$this->isNode($id))
      return false;
    else
      return true;
  }

  function canDeleteNode($id)
  {
    $amount = $this->countChildren($id);

    if ($amount === false ||  $amount == 0)
      return true;
    else
      return false;
  }
}

?>