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
require_once(LIMB_DIR . '/class/core/actions/FormEditSiteObjectAction.class.php');

class ChangePasswordAction extends FormEditSiteObjectAction
{
  protected function _defineSiteObjectClassName()
  {
    return 'user_object';
  }

  protected function _defineDataspaceName()
  {
    return 'change_password';
  }

  protected function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'identifier' => 'identifier',
          'password' => 'password',
          'second_password' => 'second_password',
        )
    );
  }

  protected function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'second_password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
  }

  public function _validPerform($request, $response)
  {
    parent :: _validPerform($request, $response);

    if ($this->_changingOwnPassword())
    {
      Limb :: toolkit()->getUser()->logout();
      MessageBox :: writeWarning(Strings :: get('need_relogin', 'user'));
    }
    else
    {
      $object_data = $this->_loadObjectData();
      Limb :: toolkit()->getSession()->storageDestroyUser($object_data['id']);
    }

    if ($request->getStatus() == Request :: STATUS_SUCCESS)
    {
      if($request->hasAttribute('popup'))
        $response->write(closePopupResponse($request, '/'));
    }
  }

  protected function _changingOwnPassword()
  {
    $object_data = $this->_loadObjectData();

    return ($object_data['id'] == Limb :: toolkit()->getUser()->getId()) ? true : false;
  }

  protected function _updateObjectOperation()
  {
    $this->object->changePassword();
  }
}

?>