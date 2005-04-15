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
require_once(LIMB_DIR . '/core/actions/action.class.php');

class tree_display_action extends action
{
  function perform(&$request, &$response)
  {
    $tree =& tree :: instance();
    $tree->initialize_expanded_parents();
  }
}

?>