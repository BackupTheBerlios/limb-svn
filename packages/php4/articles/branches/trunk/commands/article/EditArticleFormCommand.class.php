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
require_once(LIMB_DIR . '/class/core/commands/FormEditSiteObjectCommand.class.php');

class EditArticleFormCommand extends FormEditSiteObjectCommand
{
  function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'article_content' => 'content',
          'annotation' => 'annotation',
          'author' => 'author',
          'source' => 'source',
          'uri' => 'uri',
        )
    );
  }

  function _registerValidationRules(&$validator, &$dataspace)
  {
    parent :: _registerValidationRules($validator, $dataspace);

    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'author'));
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'article_content'));
  }
}

?>