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
require_once(LIMB_DIR . '/class/actions/FormAction.class.php');

class ChangeOwnPasswordAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'change_own_password';
  }

  function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/user_old_password_rule', 'old_password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'second_password'));
    $this->validator->addRule( array(LIMB_DIR . '/class/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
  }

  function _validPerform(&$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    $user_object =& $toolkit->createSiteObject('UserObject');

    $data = $this->dataspace->export();

    $user_object->changeOwnPassword($data['password']);

    if(catch('SQLException', $e))
      return throw($e);
    elseif(catch('LimbException', $e))
      $request->setStatus(Request :: STATUS_FAILED);
    elseif(catch('Exception', $e))
      return throw($e);

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);

    $user =& toolkit()->getUser();
    $user->logout();
    MessageBox :: writeWarning(Strings :: get('need_relogin', 'user'));
  }
}

?>