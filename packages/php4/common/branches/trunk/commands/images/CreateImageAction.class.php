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
require_once(LIMB_DIR . '/class/core/actions/FormCreateSiteObjectAction.class.php');

class CreateImageAction extends FormCreateSiteObjectAction
{
  protected function _defineSiteObjectClassName()
  {
    return 'image_object';
  }

  protected function _defineDataspaceName()
  {
    return 'create_image';
  }

  protected function _defineDatamap()
  {
    $datamap = array(
      'description' => 'description',
    );

    $ini = Limb :: toolkit()->getINI('image_variations.ini');

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

  protected function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
  }

  protected function _initDataspace($request)
  {
    parent :: _initDataspace($request);

    $ini = Limb :: toolkit()->getINI('image_variations.ini');

    $image_variations = $ini->getAll();

    foreach($image_variations as $variation => $variation_data)
    {
      $this->dataspace->set('upload_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
      $this->dataspace->set('generate_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
    }
  }

  protected function _createObjectOperation()
  {
    $this->object->set('files_data', $_FILES[$this->name]);

    try
    {
      return parent :: _createObjectOperation();
    }
    catch(SQLException $e)
    {
      throw $e;
    }
    catch(LimbException $e)
    {
      MessageBox :: writeNotice('Some variations were not resized');
    }
  }
}

?>