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
require_once(LIMB_DIR . '/core/actions/site_param_object/update_param_common_action.class.php');

class update_param_action extends update_param_common_action
{
  function _define_dataspace_name()
  {
    return 'site_param_form';
  }
}
?>