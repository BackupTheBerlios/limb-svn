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

class recover_version_action extends action
{
  function perform(&$request, &$response)
  {
    $request->set_status(REQUEST_STATUS_FAILURE);

    if(!$version = $request->get_attribute('version'))
      return;

    if(!$node_id = $request->get_attribute('version_node_id'))
      return;

    if(!$site_object = wrap_with_site_object(fetch_one_by_node_id((int)$node_id)))
      return;

    if(!is_subclass_of($site_object, 'content_object'))
      return;

    if(!$site_object->recover_version((int)$version))
      return;

    if($request->has_attribute('popup'))
      $response->write(close_popup_response($request));

    $request->set_status(REQUEST_STATUS_SUCCESS);
  }
}

?>