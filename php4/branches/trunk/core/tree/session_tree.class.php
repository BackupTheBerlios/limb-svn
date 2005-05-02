<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: tree.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/tree_decorator.class.php');

class session_tree extends tree_decorator
{
  function initialize_expanded_parents()
  {
    $parents =& session :: get('tree_expanded_parents');
    $this->tree_imp->set_expanded_parents($parents);
  }
}

?>