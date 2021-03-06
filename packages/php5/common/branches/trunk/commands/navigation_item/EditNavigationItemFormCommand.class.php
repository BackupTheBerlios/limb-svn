<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_navigation_item_action.class.php 786 2004-10-12 14:24:43Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/FormEditSiteObjectCommand.class.php');

class EditNavigationItemFormCommand extends FormEditSiteObjectCommand
{
  protected function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'url' => 'url',
          'new_window' => 'new_window',
        )
    );
  }

  protected function _registerValidationRules($validator, $dataspace)
  {
    parent :: _registerValidationRules($validator, $dataspace);

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'url'));
  }
}

?>