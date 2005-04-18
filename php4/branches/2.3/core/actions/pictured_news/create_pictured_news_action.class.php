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

class create_pictured_news_action extends form_create_site_object_action
{
  function _define_site_object_class_name()
  {
    return 'pictured_news_object';
  }

  function _define_dataspace_name()
  {
    return 'pictured_news_form';
  }

  function _define_datamap()
  {
    return complex_array :: array_merge(
        parent :: _define_datamap(),
        array(
          'image_id' => 'image_id',
          'annotation' => 'annotation',
          'news_content' => 'content',
          'news_date' => 'news_date',
        )
    );
  }

  function _init_validator()
  {
    parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'title'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'annotation'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'news_date'));
    $this->validator->add_rule($v4 = array(LIMB_DIR . '/core/lib/validators/rules/locale_date_rule', 'news_date'));
  }
}

?>