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

class vote_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'vote_action';
  }

  function _valid_perform(&$request, &$response)
  {
    $object =& site_object_factory :: create('poll_container');
    $data = $this->dataspace->export();

    $request->set_status(REQUEST_STATUS_FAILURE);

    if (!isset($data['answer']))
    {
      message_box :: write_notice(strings :: get('no_answer', 'poll'));
      return;
    }

    $object->register_answer($data['answer']);
    $request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
  }

}

?>