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
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_image_action extends form_create_site_object_action
{
	function create_image_action()
	{
		$definition = array(
			'site_object' => 'image_object',
			'datamap' => array(
				'description' => 'description',
			)
		);
		
		$ini =& get_ini('image_variations.ini');
		
		$image_variations = $ini->get_named_array();

		foreach($image_variations as $variation => $variation_data)
		{
			$definition['datamap']['upload_' . $variation . '_max_size'] = 'upload_' . $variation . '_max_size';
			$definition['datamap']['generate_' . $variation . '_max_size'] = 'generate_' . $variation . '_max_size';
			$definition['datamap'][$variation . '_action'] = $variation . '_action';
			$definition['datamap'][$variation . '_base_variation'] = $variation . '_base_variation';
		}

		parent :: form_create_site_object_action('create_image', $definition);
	}	

	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
	}
	
	function _init_dataspace()
	{
		parent :: _init_dataspace();
		
		$ini =& get_ini('image_variations.ini');
		
		$image_variations = $ini->get_named_array();
		
		foreach($image_variations as $variation => $variation_data)
		{
			$this->dataspace->set('upload_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
			$this->dataspace->set('generate_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
		}
	}
	
	function _create_object_operation()
	{
		$this->object->set_attribute('files_data', $_FILES[$this->name]);
		
		if(($id = parent :: _create_object_operation()) === false)
			return false;
						
		return $id;
	}
}

?>