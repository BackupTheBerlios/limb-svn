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

class CreateUserGroupAction extends FormCreateSiteObjectAction
{
  function _defineDataspaceName()
  {
    return 'create_user_group';
  }

  function _defineSiteObjectClassName()
  {
    return 'user_group';
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
  }
}

?>