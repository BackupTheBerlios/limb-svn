<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/actions/form_action.class.php');

class generate_password_action extends form_action
{
  protected function _define_dataspace_name()
  {
    return 'generate_password';
  }

  protected function _init_validator()
  {
    $this->validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'email'));
    $this->validator->add_rule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'email'));
  }

  protected function _valid_perform($request, $response)
  {
    $data = $this->dataspace->export();
    $object = Limb :: toolkit()->createSiteObject('user_object');

    $new_non_crypted_password = '';
    if($object->generate_password($data['email'], $new_non_crypted_password))
      $request->set_status(request :: STATUS_FORM_SUBMITTED);
    else
      $request->set_status(request :: STATUS_FAILED);

  }
}

?>