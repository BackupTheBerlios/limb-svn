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
require_once(LIMB_DIR . '/core/actions/form_edit_site_object_action.class.php');

class edit_poll_answer_action extends form_edit_site_object_action
{
  function _define_site_object_class_name()
  {
    return 'poll_answer';
  }

  function _define_dataspace_name()
  {
    return 'edit_poll_answer';
  }
}

?>