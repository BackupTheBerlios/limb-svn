<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_poll_action.class.php 786 2004-10-12 14:24:43Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/form_edit_site_object_command.class.php');

class edit_poll_form_command extends form_edit_site_object_command
{
  protected function _define_datamap()
  {
    return complex_array :: array_merge(
        parent :: _define_datamap(),
        array(
          'start_date' => 'start_date',
          'finish_date' => 'finish_date',
          'restriction' => 'restriction',
        )
    );
  }

  protected function _register_validation_rules($validator, $dataspace)
  {
    parent :: _register_validation_rules($validator, $dataspace);

    $this->validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'start_date'));
    $this->validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'finish_date'));
    $this->validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'restriction'));
  }
}

?>