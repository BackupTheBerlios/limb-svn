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
require_once(LIMB_DIR . '/class/commands/FormCreateSiteObjectCommand.class.php');

class CreatePollAnswerFormCommand extends FormCreateSiteObjectCommand
{
  function _registerValidationRules(&$validator, &$dataspace)
  {
    parent :: _registerValidationRules($validator, $dataspace);

    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
  }
}

?>