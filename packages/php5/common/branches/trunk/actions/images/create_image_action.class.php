<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/actions/form_create_site_object_action.class.php');

class create_image_action extends form_create_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'image_object';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'create_image';
	}
  
  function _define_datamap()
	{
		$datamap = array(
			'description' => 'description',
		);
		
		$ini =& get_ini('image_variations.ini');
		
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

	function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule($v = array(LIMB_DIR . 'class/validators/rules/required_rule', 'title'));
	}
	
	function _init_dataspace(&$request)
	{
		parent :: _init_dataspace($request);
		
		$ini =& get_ini('image_variations.ini');
		
		$image_variations = $ini->get_all();
		
		foreach($image_variations as $variation => $variation_data)
		{
			$this->dataspace->set('upload_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
			$this->dataspace->set('generate_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
		}
	}
	
	function _create_object_operation()
	{
		$this->object->set('files_data', $_FILES[$this->name]);
		
		if(($id = parent :: _create_object_operation()) === false)
			return false;
						
		return $id;
	}
}

?>