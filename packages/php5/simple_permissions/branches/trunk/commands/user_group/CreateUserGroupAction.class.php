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
require_once(LIMB_DIR . '/class/core/actions/FormCreateSiteObjectAction.class.php');

class CreateUserGroupAction extends FormCreateSiteObjectAction
{
  protected function _defineDataspaceName()
  {
    return 'create_user_group';
  }

  protected function _defineSiteObjectClassName()
  {
    return 'user_group';
  }

  protected function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
  }
}

?>