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

class EditUserAction extends FormEditSiteObjectAction
{
  protected function _defineSiteObjectClassName()
  {
    return 'user_object';
  }

  protected function _defineDataspaceName()
  {
    return 'edit_user';
  }

  protected function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'name' => 'name',
          'lastname' => 'lastname',
          'email' => 'email',
        )
    );
  }

  protected function _defineIncreaseVersionFlag()
  {
    return false;
  }

  protected function _initValidator()
  {
    parent :: _initValidator();

    $request = Limb :: toolkit()->getRequest();

    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    if ($object_data = $datasource->fetch())
    {
      $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/unique_user_rule', 'identifier', $object_data['identifier']));
      $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/unique_user_email_rule', 'email', $object_data['email']));
    }

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'name'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'email'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'email'));
  }
}

?>