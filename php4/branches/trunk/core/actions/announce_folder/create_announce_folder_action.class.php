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
require_once(LIMB_DIR . '/core/actions/site_object/create_action.class.php');

class create_announce_folder_action extends create_action
{
  function _define_controller_name()
  {
    return 'announce_folder_controller';
  }
}

?>