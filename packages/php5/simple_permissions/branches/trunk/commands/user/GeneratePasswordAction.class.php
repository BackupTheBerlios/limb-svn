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
require_once(LIMB_DIR . '/class/core/actions/FormAction.class.php');

class GeneratePasswordAction extends FormAction
{
  protected function _defineDataspaceName()
  {
    return 'generate_password';
  }

  protected function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'email'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'email'));
  }

  protected function _validPerform($request, $response)
  {
    $data = $this->dataspace->export();
    $object = Limb :: toolkit()->createSiteObject('UserObject');

    $new_non_crypted_password = '';
    if($object->generatePassword($data['email'], $new_non_crypted_password))
      $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
    else
      $request->setStatus(Request :: STATUS_FAILED);

  }
}

?>