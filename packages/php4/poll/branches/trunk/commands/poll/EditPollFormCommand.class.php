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
require_once(LIMB_DIR . '/class/commands/FormEditSiteObjectCommand.class.php');

class EditPollFormCommand extends FormEditSiteObjectCommand
{
  function _defineDatamap()
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

  function _registerValidationRules(&$validator, &$dataspace)
  {
    parent :: _registerValidationRules($validator, $dataspace);

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'start_date'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'finish_date'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'restriction'));
  }
}

?>