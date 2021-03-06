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
class TreeBranchCriteria
{
  var $path = '';
  var $check_expanded_parents = false;
  var $include_parent = false;
  var $depth = 1;

  function TreeBranchCriteria()
  {
    $this->reset();
  }

  function setPath($path)
  {
    $this->path = $path;
  }

  function setCheckExpandedParents($status = true)
  {
    $this->check_expanded_parents = $status;
  }

  function setIncludeParent($status = true)
  {
    $this->include_parent = $status;
  }

  function setDepth($depth)
  {
    $this->depth = $depth;
  }

  function reset()
  {
    $this->path = '';
    $this->check_expanded_parents = false;
    $this->include_parent = false;
    $this->depth = 1;
  }

  function process(&$sql)
  {
    $node_ids = $this->_getNodeIds();

    if(count($node_ids))
      $sql->addCondition('tree.id IN (' . implode(',', $node_ids). ')');
    else
      $sql->addCondition('0 = 1');

    $sql->addOrder('tree.path');
  }

  function _getNodeIds()
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$rs = $tree->getSubBranchByPath($this->path,
                                        $this->depth,
                                        $this->include_parent,
                                        $this->check_expanded_parents))
    {
      return array();
    }

    $rs->rewind();
    $node_ids = array();
    while($rs->valid())
    {
      $record =& $rs->current();
      $node_ids[] = $record->get('id');
      $rs->next();
    }

    return $node_ids;
  }
}

?>