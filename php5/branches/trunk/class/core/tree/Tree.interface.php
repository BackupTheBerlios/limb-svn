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

interface Tree
{
  public function isNode($id);

  public function getNode($id);

  public function getParent($id);

  public function getParents($id);

  public function getSiblings($id);

  public function getChildren($id);

  public function countChildren($id);

  public function createRootNode($values);

  public function createSubNode($id, $values);

  public function deleteNode($id);

  public function updateNode($id, $values, $internal = false);

  public function moveTree($id, $target_id);

  public function setDumbMode($status=true);

  public function getAllNodes();

  public function getNodesByIds($ids_array);

  public function getMaxChildIdentifier($id);

  public function getNodeByPath($path, $delimiter='/');

  public function getSubBranch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false);

  public function getSubBranchByPath($path, $depth = -1, $include_parent = false, $check_expanded_parents = false);

  public function getRootNodes();

  public function isNodeExpanded($id);

  public function toggleNode($id);

  public function expandNode($id);

  public function collapseNode($id);
}

?>