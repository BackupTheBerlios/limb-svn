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

class TreePathRule extends SingleFieldRule
{
  public function validate($dataspace)
  {
    $value = $dataspace->get($this->field_name);

    if(!Limb :: toolkit()->getTree()->getNodeByPath($value))
      $this->error(Strings :: get('error_invalid_tree_path', 'error'));
  }
}

?>