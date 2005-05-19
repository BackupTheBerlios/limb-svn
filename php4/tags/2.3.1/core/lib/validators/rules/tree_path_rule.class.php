<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/tree/tree.class.php');
require_once(LIMB_DIR . '/core/lib/validators/rules/single_field_rule.class.php');

class tree_path_rule extends single_field_rule
{
  function validate(&$dataspace)
  {
    $value = $dataspace->get($this->field_name);

    $tree = tree :: instance();

    if(!$tree->get_node_by_path($value))
      $this->error(strings :: get('error_invalid_tree_path', 'error'));

  }
}

?>