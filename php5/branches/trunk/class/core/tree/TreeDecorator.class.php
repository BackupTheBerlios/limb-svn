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
require_once(LIMB_DIR . '/class/core/tree/Tree.interface.php');

class TreeDecorator implements Tree
{
  protected $_tree = null;

  function __construct($tree)
  {
    $this->_tree = $tree;
  }

  public function isNode($id)
  {
    return $this->_tree->isNode($id);
  }

  public function getNode($id)
  {
    return $this->_tree->getNode($id);
  }

  public function getParent($id)
  {
    return $this->_tree->getParent($id);
  }

  public function getParents($id)
  {
    return $this->_tree->getParents($id);
  }

  public function getSiblings($id)
  {
    return $this->_tree->getSiblings($id);
  }

  public function getChildren($id)
  {
    return $this->_tree->getChildren($id);
  }

  public function countChildren($id)
  {
    return $this->_tree->countChildren($id);
  }

  public function createRootNode($values)
  {
    return $this->_tree->createRootNode($values);
  }

  public function createSubNode($id, $values)
  {
    return $this->_tree->createSubNode($id, $values);
  }

  public function deleteNode($id)
  {
    return $this->_tree->deleteNode($id);
  }

  public function updateNode($id, $values, $internal = false)
  {
    return $this->_tree->updateNode($id, $values, $internal);
  }

  public function moveTree($id, $target_id)
  {
    return $this->_tree->moveTree($id, $target_id);
  }

  public function setDumbMode($status=true)
  {
    $this->_tree->setDumbMode($status);
  }

  public function getAllNodes()
  {
    return $this->_tree->getAllNodes();
  }

  public function getNodesByIds($ids_array)
  {
    return $this->_tree->getNodesByIds($ids_array);
  }

  public function getPathToNode($node, $delimeter = '/')
  {
    if(($parents = $this->_tree->getParents($node['id'])) === false)
      return false;

    $path = '';
    foreach($parents as $parent_data)
      $path .= $delimeter . $parent_data['identifier'];

    return $path .= $delimeter . $node['identifier'];
  }

  public function getMaxChildIdentifier($id)
  {
    return $this->_tree->getMaxChildIdentifier($id);
  }

  public function getNodeByPath($path, $delimiter='/')
  {
    return $this->_tree->getNodeByPath($path, $delimiter);
  }

  public function getSubBranch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    return $this->_tree->getSubBranch($id, $depth, $include_parent, $check_expanded_parents);
  }

  public function getSubBranchByPath($path, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    return $this->_tree->getSubBranchByPath($path, $depth, $include_parent, $check_expanded_parents);
  }

  public function getRootNodes()
  {
    return $this->_tree->getRootNodes();
  }

  public function isNodeExpanded($id)
  {
    return $this->_tree->isNodeExpanded($id);
  }

  public function toggleNode($id)
  {
    return $this->_tree->toggleNode($id);
  }

  public function expandNode($id)
  {
    return $this->_tree->expandNode($id);
  }

  public function collapseNode($id)
  {
    return $this->_tree->collapseNode($id);
  }

  public function canAddNode($id)
  {
    if (!$this->isNode($id))
      return false;
    else
      return true;
  }

  public function canDeleteNode($id)
  {
    $amount = $this->countChildren($id);

    if ($amount === false ||  $amount == 0)
      return true;
    else
      return false;
  }
}

?>