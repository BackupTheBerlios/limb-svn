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

class EditFaqObjectAction extends FormEditSiteObjectAction
{
  protected function _defineSiteObjectClassName()
  {
    return 'faq_object';
  }

  protected function _defineDataspaceName()
  {
    return 'edit_faq_object';
  }

  protected function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'question' => 'question',
          'question_author' => 'question_author',
          'question_author_email' => 'question_author_email',
          'answer' => 'answer',
          'answer_author' => 'answer_author',
          'answer_author_email' => 'answer_author_email',
        )
    );
  }

  protected function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'question'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'answer'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'question_author_email'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'answer_author_email'));
  }
}

?>