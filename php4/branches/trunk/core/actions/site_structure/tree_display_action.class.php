<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
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