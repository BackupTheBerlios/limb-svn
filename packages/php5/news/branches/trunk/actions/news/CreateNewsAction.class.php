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

class CreateNewsAction extends FormCreateSiteObjectAction
{
  protected function _defineSiteObjectClassName()
  {
    return 'news_object';
  }

  protected function _defineDataspaceName()
  {
    return 'news_form';
  }

  protected function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'annotation' => 'annotation',
          'news_content' => 'content',
          'news_date' => 'news_date',
        )
    );
  }

  protected function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'annotation'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'news_date'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/locale_date_rule', 'news_date'));
  }
}

?>