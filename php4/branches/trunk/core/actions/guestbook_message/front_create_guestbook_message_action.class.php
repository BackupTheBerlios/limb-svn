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
require_once(LIMB_DIR . '/core/actions/guestbook_message/create_guestbook_message_action.class.php');

class front_create_guestbook_message_action extends create_guestbook_message_action
{
  function _define_dataspace_name()
  {
    return 'display';
  }

  function _valid_perform(&$request, &$response)
  {
    parent :: _valid_perform($request, $response);

    if ($request->is_success())
      $response->reload();
  }
}

?>