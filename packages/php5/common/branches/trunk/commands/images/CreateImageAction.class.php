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
require_once(LIMB_DIR . '/class/core/actions/form_create_site_object_action.class.php');

class create_image_action extends form_create_site_object_action
{
  protected function _define_site_object_class_name()
  {
    return 'image_object';
  }

  protected function _define_dataspace_name()
  {
    return 'create_image';
  }

  protected function _define_datamap()
  {
    $datamap = array(
      'description' => 'description',
    );

    $ini = Limb :: toolkit()->getINI('image_variations.ini');

    $image_variations = $ini->get_all();

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

  protected function _init_validator()
  {
    parent :: _init_validator();

    $this->validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
  }

  protected function _init_dataspace($request)
  {
    parent :: _init_dataspace($request);

    $ini = Limb :: toolkit()->getINI('image_variations.ini');

    $image_variations = $ini->get_all();

    foreach($image_variations as $variation => $variation_data)
    {
      $this->dataspace->set('upload_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
      $this->dataspace->set('generate_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
    }
  }

  protected function _create_object_operation()
  {
    $this->object->set('files_data', $_FILES[$this->name]);

    try
    {
      return parent :: _create_object_operation();
    }
    catch(SQLException $e)
    {
      throw $e;
    }
    catch(LimbException $e)
    {
      message_box :: write_notice('Some variations were not resized');
    }
  }
}

?>