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
  function _defineSiteObjectClassName()
  {
    return 'user_object';
  }

  function _defineDataspaceName()
  {
    return 'change_password';
  }

  function _defineDatamap()
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

  function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'second_password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
  }

  function _validPerform($request, $response)
  {
    parent :: _validPerform($request, $response);

    $toolkit =& Limb :: toolkit();

    if ($this->_changingOwnPassword())
    {
      $user =& $toolkit->getUser();
      $user->logout();
      MessageBox :: writeWarning(Strings :: get('need_relogin', 'user'));
    }
    else
    {
      $object_data = $this->_loadObjectData();
      $session =& $toolkit->getSession();
      $session->storageDestroyUser($object_data['id']);
    }

    if ($request->getStatus() == Request :: STATUS_SUCCESS)
    {
      if($request->hasAttribute('popup'))
        $response->write(closePopupResponse($request, '/'));
    }
  }

  function _changingOwnPassword()
  {
    $object_data = $this->_loadObjectData();

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    return ($object_data['id'] == $user->getId()) ? true : false;
  }

  function _updateObjectOperation()
  {
    $this->object->changePassword();
  }
}

?>