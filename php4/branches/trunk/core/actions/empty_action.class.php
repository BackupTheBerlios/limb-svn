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
class empty_action
{
  function set_view(&$view)
  {
  }

  function perform(&$request, &$response)
  {
    $request->set_status(REQUEST_STATUS_SUCCESS);
  }
}


?>