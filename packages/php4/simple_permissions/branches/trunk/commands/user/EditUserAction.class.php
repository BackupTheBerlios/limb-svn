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
require_once(LIMB_DIR . '/core/actions/FormEditSiteObjectAction.class.php');

class EditUserAction extends FormEditSiteObjectAction
{
  function _defineSiteObjectClassName()
  {
    return 'user_object';
  }

  function _defineDataspaceName()
  {
    return 'edit_user';
  }

  function _defineDatamap()
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

  function _defineIncreaseVersionFlag()
  {
    return false;
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    if ($object_data = $datasource->fetch())
    {
      $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/unique_user_rule', 'identifier', $object_data['identifier']));
      $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/unique_user_email_rule', 'email', $object_data['email']));
    }

    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'name'));
    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'email'));
    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/email_rule', 'email'));
  }
}

?>