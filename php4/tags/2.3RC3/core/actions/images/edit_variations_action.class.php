<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/form_edit_site_object_action.class.php');

class edit_variations_action extends form_edit_site_object_action
{
  function _define_site_object_class_name()
  {
    return 'image_object';
  }

  function _define_dataspace_name()
  {
    return 'edit_variations';
  }

  function _define_datamap()
  {
    $datamap = array();

    $image_variations = $this->_get_variations_ini_list();

    foreach($image_variations as $variation => $variation_data)
    {
      $datamap['upload_' . $variation . '_max_size'] = 'upload_' . $variation . '_max_size';
      $datamap['generate_' . $variation . '_max_size'] = 'generate_' . $variation . '_max_size';
      $datamap[$variation . '_action'] = $variation . '_action';
      $datamap[$variation . '_base_variation'] = $variation . '_base_variation';
    }

    return complex_array :: array_merge(
        parent :: _define_datamap(),
        $datamap
    );
  }

  function _init_validator()
  {
    //??
  }

  function _init_dataspace(&$request)
  {
    parent :: _init_dataspace($request);

    $image_variations = $this->_get_variations_ini_list();

    foreach($image_variations as $variation => $variation_data)
    {
      if(isset($variation_data['max_size']))
      {
        $this->dataspace->set('upload_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
        $this->dataspace->set('generate_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
      }
    }
  }

  function _update_object_operation()
  {
    $this->object->set_attribute('files_data', $this->_get_uploaded_files_data());

    $this->object->update_variations();

    return true;
  }

  function _get_uploaded_files_data()
  {
    $files_data = array();

    $image_variations = $this->_get_variations_ini_list();

    foreach($image_variations as $variation => $variation_data)
    {
      if($file = $this->dataspace->get($variation))
        $files_data[$variation] = $file;
    }

    return $files_data;
  }

  function _get_variations_ini_list()
  {
    $ini =& get_ini('image_variations.ini');

    return $ini->get_all();
  }
}

?>