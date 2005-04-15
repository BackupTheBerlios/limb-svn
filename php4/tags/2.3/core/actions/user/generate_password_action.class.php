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

class generate_password_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'generate_password';
  }

  function _init_validator()
  {
    $this->validator->add_rule($v1 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'email'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . '/core/lib/validators/rules/email_rule', 'email'));
  }

  function _valid_perform(&$request, &$response)
  {
    $data = $this->dataspace->export();
    $object =& site_object_factory :: create('user_object');

    $new_non_crypted_password = '';
    if($object->generate_password($data['email'], $new_non_crypted_password))
    {
      $request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
      $response->redirect($_SERVER['PHP_SELF'] . '?action=password_generated');
    }
    else
    {
      $request->set_status(REQUEST_STATUS_FAILED);
      $response->redirect($_SERVER['PHP_SELF'] . '?action=password_not_generated');
    }

  }
}

?>