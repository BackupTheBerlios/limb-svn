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
  function isNode($id);

  function getNode($id);

  function getParent($id);

  function getParents($id);

  function getSiblings($id);

  function getChildren($id);

  function countChildren($id);

  function createRootNode($values);

  function createSubNode($id, $values);

  function deleteNode($id);

  function updateNode($id, $values, $internal = false);

  function moveTree($id, $target_id);

  function setDumbMode($status=true);

  function getAllNodes();

  function getNodesByIds($ids_array);

  function getMaxChildIdentifier($id);

  function getNodeByPath($path, $delimiter='/');

  function getSubBranch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false);

  function getSubBranchByPath($path, $depth = -1, $include_parent = false, $check_expanded_parents = false);

  function getRootNodes();

  function isNodeExpanded($id);

  function toggleNode($id);

  function expandNode($id);

  function collapseNode($id);
}

?>