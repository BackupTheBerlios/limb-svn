<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/FormEditSiteObjectAction.class.php');

class EditImageAction extends FormEditSiteObjectAction
{
  function _defineSiteObjectClassName()
  {
    return 'image_object';
  }

  function _defineDataspaceName()
  {
    return 'edit_image';
  }

  function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'description' => 'description',
        )
    );
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'title'));
  }

}

?>