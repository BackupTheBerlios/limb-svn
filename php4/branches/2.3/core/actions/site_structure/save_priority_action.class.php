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
require_once(LIMB_DIR . '/core/actions/form_action.class.php');

class save_priority_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'grid_form';
  }

  function perform(&$request, &$response)
  {
    parent :: perform($request, $response);
  }

  function _valid_perform(&$request, &$response)
  {
    $data = $this->dataspace->export();
    $object =& site_object_factory :: create('site_object');

    if(isset($data['priority']))
      $object->save_priority($data['priority']);

    $request->set_status(REQUEST_STATUS_SUCCESS);

    if($request->has_attribute('popup'))
      $response->write(close_popup_response($request));
  }
}

?>