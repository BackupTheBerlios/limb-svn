<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/form_create_site_object_action.class.php');

class create_faq_object_action extends form_create_site_object_action
{
  function _define_site_object_class_name()
  {
    return 'faq_object';
  }

  function _define_dataspace_name()
  {
    return 'create_faq_object';
  }

  function _define_datamap()
  {
    return complex_array :: array_merge(
        parent :: _define_datamap(),
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

  function _init_validator()
  {
    parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'question'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'answer'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . '/core/lib/validators/rules/email_rule', 'question_author_email'));
    $this->validator->add_rule($v4 = array(LIMB_DIR . '/core/lib/validators/rules/email_rule', 'answer_author_email'));
  }
}

?>