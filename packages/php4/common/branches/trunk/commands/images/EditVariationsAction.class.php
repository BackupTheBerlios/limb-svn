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
require_once(LIMB_DIR . '/class/core/actions/FormEditSiteObjectAction.class.php');

class EditVariationsAction extends FormEditSiteObjectAction
{
  protected function _defineSiteObjectClassName()
  {
    return 'image_object';
  }

  protected function _defineDataspaceName()
  {
    return 'edit_variations';
  }

  protected function _defineDatamap()
  {
    $datamap = array(
      '_FILES_' => 'files_data'
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
    //??
  }

  protected function _initDataspace($request)
  {
    parent :: _initDataspace($request);

    $ini = Limb :: toolkit()->getINI('image_variations.ini');

    $image_variations = $ini->getAll();

    foreach($image_variations as $variation => $variation_data)
    {
      if(isset($variation_data['max_size']))
      {
        $this->dataspace->set('upload_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
        $this->dataspace->set('generate_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
      }
    }
  }

  protected function _updateObjectOperation()
  {
    $this->object->set('files_data', $_FILES[$this->name]);

    try
    {
      $this->object->updateVariations();
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