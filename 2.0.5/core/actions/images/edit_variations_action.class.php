<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_variations_action.class.php 538 2004-02-22 16:21:08Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');

class edit_variations_action extends form_edit_site_object_action
{
	function edit_variations_action()
	{
		$definition = array(
			'site_object' => 'image_object',
			'datamap' => array(
				'_FILES_' => 'files_data'
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
		
		parent :: form_edit_site_object_action('edit_variations', $definition);
	}
	
	function _init_validator()
	{
	}
	
	function _init_dataspace()
	{
		parent :: _init_dataspace();
		
		$ini =& get_ini('image_variations.ini');
		
		$image_variations = $ini->get_named_array();
		
		foreach($image_variations as $variation => $variation_data)
		{
			if(isset($variation_data['max_size']))
			{
				$this->_set('upload_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
				$this->_set('generate_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
			}
		}
	}
	
	function _update_object_operation()
	{	
		$this->object->set_attribute('files_data', $_FILES[$this->name]);
		
		$this->object->update_variations();
							
		return true;
	}

}

?>