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
require_once(LIMB_DIR . '/core/actions/navigation_item/create_navigation_item_action.class.php');

class create_admin_navigation_item_action extends create_navigation_item_action
{
  function _define_controller_name()
  {
    return 'admin_navigation_item_controller';
  }
}
?>