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

class ChangeOwnPasswordAction extends FormAction
{
  protected function _defineDataspaceName()
  {
    return 'change_own_password';
  }

  protected function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/user_old_password_rule', 'old_password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'second_password'));
    $this->validator->addRule( array(LIMB_DIR . '/class/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
  }

  protected function _validPerform($request, $response)
  {
    $user_object = Limb :: toolkit()->createSiteObject('UserObject');

    $data = $this->dataspace->export();

    try
    {
      $user_object->changeOwnPassword($data['password']);
    }
    catch(SQLException $e)
    {
      throw $e;
    }
    catch(LimbException $e)
    {
      $request->setStatus(Request :: STATUS_FAILED);
    }

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);

    Limb :: toolkit()->getUser()->logout();
    MessageBox :: writeWarning(Strings :: get('need_relogin', 'user'));
  }
}

?>