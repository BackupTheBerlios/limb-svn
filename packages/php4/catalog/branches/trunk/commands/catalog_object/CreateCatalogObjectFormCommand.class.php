<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_catalog_object_action.class.php 786 2004-10-12 14:24:43Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/FormCreateSiteObjectCommand.class.php');

class CreateCatalogObjectFormCommand extends FormCreateSiteObjectCommand
{
  function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'annotation' => 'annotation',
          'object_content' => 'content',
          'image_id' => 'image_id'
        )
    );
  }

  function _registerValidationRules($validator, $dataspace)
  {
    parent :: _registerValidationRules($validator, $dataspace);

    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'annotation'));
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'object_content'));
  }
}

?>