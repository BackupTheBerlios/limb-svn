<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_article_action.class.php 786 2004-10-12 14:24:43Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/form_create_site_object_command.class.php');

class create_article_form_command extends form_create_site_object_command
{
  protected function _define_datamap()
  {
    return complex_array :: array_merge(
        parent :: _define_datamap(),
        array(
          'article_content' => 'content',
          'annotation' => 'annotation',
          'author' => 'author',
          'source' => 'source',
          'uri' => 'uri',
        )
    );
  }

  protected function _register_validation_rules($validator, $dataspace)
  {
    parent :: _register_validation_rules($validator, $dataspace);

    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'author'));
    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'article_content'));
  }
}

?>