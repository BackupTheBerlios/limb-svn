<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_message_action.class.php 707 2004-09-18 14:43:42Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/FormCreateSiteObjectCommand.class.php');

class CreateMessageFormCommand extends FormCreateSiteObjectCommand
{
  function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'content' => 'content',
        )
    );
  }

  function _registerValidationRules(&$validator, &$dataspace)
  {
    parent :: _registerValidationRules($validator, $dataspace);

    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'content'));
  }
}

?>