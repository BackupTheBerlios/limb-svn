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
require_once(LIMB_DIR . '/core/actions/FormAction.class.php');

class GeneratePasswordAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'generate_password';
  }

  function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'email'));
    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/email_rule', 'email'));
  }

  function _validPerform(&$request, &$response)
  {
    $data = $this->dataspace->export();
    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->createSiteObject('UserObject');

    $new_non_crypted_password = '';
    if($object->generatePassword($data['email'], $new_non_crypted_password))
      $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
    else
      $request->setStatus(Request :: STATUS_FAILED);

  }
}

?>