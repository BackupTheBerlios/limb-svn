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

class EditAction extends FormEditSiteObjectAction
{
  function _defineDataspaceName()
  {
    return 'edit_site_object';
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'title'));
  }
}

?>