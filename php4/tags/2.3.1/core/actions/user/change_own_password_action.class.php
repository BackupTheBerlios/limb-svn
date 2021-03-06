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

class change_own_password_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'change_own_password';
  }

  function _init_validator()
  {
    $this->validator->add_rule($v1 = array(LIMB_DIR . '/core/lib/validators/rules/user_old_password_rule', 'old_password'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'password'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'second_password'));
    $this->validator->add_rule($v4 = array(LIMB_DIR . '/core/lib/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
  }

  function _valid_perform(&$request, &$response)
  {
    $user_object =& site_object_factory :: create('user_object');

    $data = $this->dataspace->export();

    if($user_object->change_own_password($data['password']))
      $request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
    else
      $request->set_status(REQUEST_STATUS_FAILED);
  }

}

?>