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
  function _defineSiteObjectClassName()
  {
    return 'image_object';
  }

  function _defineDataspaceName()
  {
    return 'edit_variations';
  }

  function _defineDatamap()
  {
    $datamap = array(
      '_FILES_' => 'files_data'
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
    //??
  }

  function _initDataspace(&$request)
  {
    parent :: _initDataspace($request);

    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('image_variations.ini');

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

  function _updateObjectOperation()
  {
    $this->object->set('files_data', $_FILES[$this->name]);

    if(Limb :: isError($e = $this->object->updateVariations()))
    {
      if(is_a($e, 'SQLException'))
        return $e;
      elseif(is_a($e, 'LimbException'))
        MessageBox :: writeNotice('Some variations were not resized');
      else
        return $e;
    }
  }

}

?>