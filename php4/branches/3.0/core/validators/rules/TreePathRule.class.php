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

class TreePathRule extends SingleFieldRule
{
  function check($value)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$tree->getNodeByPath($value))
      $this->error('ERROR_INVALID_TREE_PATH');
  }
}

?>