<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: TreeBranchCriteria.class.php 1173 2005-03-17 11:36:43Z seregalimb $
*
***********************************************************************************/
class TreeRootNodesCriteria
{
  function process(&$sql)
  {
    $node_ids = $this->_getNodeIds();

    if(count($node_ids))
      $sql->addCondition('tree.id IN (' . implode(',', $node_ids). ')');
    else
      $sql->addCondition('0 = 1');
  }

  function _getNodeIds()
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$rs = $tree->getRootNodes())
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