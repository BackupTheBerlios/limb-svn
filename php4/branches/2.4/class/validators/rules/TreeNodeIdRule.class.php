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
require_once(LIMB_DIR . '/class/validators/rules/SingleFieldRule.class.php');

class TreeNodeIdRule extends SingleFieldRule
{
  function check($value)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(empty($value))
      $this->error(Strings :: get('error_invalid_tree_node_id', 'error'));
    elseif(!$tree->getNode((int)$value))
      $this->error(Strings :: get('error_invalid_tree_node_id', 'error'));
  }
}
?>