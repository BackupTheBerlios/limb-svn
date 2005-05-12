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
require_once(LIMB_DIR . '/core/tree/materialized_path_tree.class.php');
require_once(LIMB_DIR . '/core/tree/session_tree.class.php');
require_once(LIMB_DIR . '/core/tree/caching_tree.class.php');
require_once(dirname(__FILE__) . '/tree_decorator.class.php');

class tree extends tree_decorator
{
  function tree()
  {
    $imp = new caching_tree(new session_tree(new materialized_path_tree()));
    parent :: tree_decorator($imp);
  }

  function &instance()
  {
    return instantiate_object('tree');
  }
}

?>