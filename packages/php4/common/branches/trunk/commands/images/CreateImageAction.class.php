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
require_once(LIMB_DIR . '/core/actions/FormCreateSiteObjectAction.class.php');

class CreateImageAction extends FormCreateSiteObjectAction
{
  function _defineSiteObjectClassName()
  {
    return 'image_object';
  }

  function _defineDataspaceName()
  {
    return 'create_image';
  }

  function _defineDatamap()
  {
    $datamap = array(
      'description' => 'description',
    );

    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('image_variations.ini');

    $image_variations = $ini->getAll();

    foreach($image_variations as $variation => $variation_data)
    {
      $datamap['upload_' . $variation . '_max_size'] = 'upload_' . $variation . '_max_size';
      $datamap['generate_' . $variation . '_max_size'] = 'generate_' . $variation . '_max_size';
      $datamap[$variation . '_action'] = $variation . '_action';
      $datamap[$variation . '_base_variation'] = $variation . '_base_variation';
    }

    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        $datamap
    );
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'title'));
  }

  function _initDataspace(&$request)
  {
    parent :: _initDataspace($request);

    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('image_variations.ini');

    $image_variations = $ini->getAll();

    foreach($image_variations as $variation => $variation_data)
    {
      $this->dataspace->set('upload_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
      $this->dataspace->set('generate_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
    }
  }

  function _createObjectOperation()
  {
    $this->object->set('files_data', $_FILES[$this->name]);

    parent :: _createObjectOperation();

    if(catch_error('SQLException', $e))
      return throw_error($e);
    elseif(catch_error('LimbException', $e))
      MessageBox :: writeNotice('Some variations were not resized');
    elseif(catch_error('LimbException', $e))
      return throw_error($e);
  }
}

?>