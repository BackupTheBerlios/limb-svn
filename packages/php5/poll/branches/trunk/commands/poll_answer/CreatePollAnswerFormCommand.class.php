<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_poll_answer_action.class.php 786 2004-10-12 14:24:43Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/form_create_site_object_command.class.php');

class create_poll_answer_form_command extends form_create_site_object_command
{
  protected function _register_validation_rules($validator, $dataspace)
  {
    parent :: _register_validation_rules($validator, $dataspace);

    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
  }
}

?>