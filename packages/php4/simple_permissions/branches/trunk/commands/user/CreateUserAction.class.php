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
require_once(LIMB_DIR . '/class/actions/FormCreateSiteObjectAction.class.php');

class CreateUserAction extends FormCreateSiteObjectAction
{
  function _defineSiteObjectClassName()
  {
    return 'user_object';
  }

  function _defineDataspaceName()
  {
    return 'create_user';
  }

  function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'name' => 'name',
          'lastname' => 'lastname',
          'password' => 'password',
          'email' => 'email',
          'second_password' => 'second_password',
        )
    );
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/unique_user_rule', 'identifier'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/unique_user_email_rule', 'email'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'name'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'email'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'second_password'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'email'));
  }
}

?>