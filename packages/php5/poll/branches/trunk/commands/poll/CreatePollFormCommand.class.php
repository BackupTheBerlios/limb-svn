<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_poll_action.class.php 786 2004-10-12 14:24:43Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/FormCreateSiteObjectCommand.class.php');

class CreatePollFormCommand extends FormCreateSiteObjectCommand
{
  protected function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'start_date' => 'start_date',
          'finish_date' => 'finish_date',
          'restriction' => 'restriction',
        )
    );
  }

  protected function _registerValidationRules($validator, $dataspace)
  {
    parent :: _registerValidationRules($validator, $dataspace);

    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'start_date'));
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'finish_date'));
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'restriction'));
  }
}

?>