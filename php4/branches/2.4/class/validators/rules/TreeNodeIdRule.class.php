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
require_once(WACT_ROOT . '/validation/rule.inc.php');

class TreeNodeIdRule extends SingleFieldRule
{
  function check($value)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(empty($value))
      $this->error('ERROR_INVALID_TREE_NODE_ID');
    elseif(!$tree->getNode((int)$value))
      $this->error('ERROR_INVALID_TREE_NODE_ID');
  }
}
?>