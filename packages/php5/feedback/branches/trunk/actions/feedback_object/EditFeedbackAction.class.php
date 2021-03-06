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

class EditFeedbackAction extends FormEditSiteObjectAction
{
  protected function _defineSiteObjectClassName()
  {
    return 'feedback_object';
  }

  protected function _defineDataspaceName()
  {
    return 'feedback_form';
  }

  protected function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'content' => 'content',
        )
    );
  }

  protected function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'content'));
  }
}

?>