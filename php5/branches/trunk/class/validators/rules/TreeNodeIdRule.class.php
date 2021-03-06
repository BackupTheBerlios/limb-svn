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
  protected function check($value)
  {
    if(empty($value))
      $this->error(Strings :: get('error_invalid_tree_node_id', 'error'));
    elseif(!Limb :: toolkit()->getTree()->getNode((int)$value))
      $this->error(Strings :: get('error_invalid_tree_node_id', 'error'));
  }
}
?>