<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/actions/form_edit_site_object_action.class.php');

class edit_variations_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'image_object';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'edit_variations';
	}
  
  protected function _define_datamap()
	{
		$datamap = array(
			'_FILES_' => 'files_data'
		);
		
		$ini = get_ini('image_variations.ini');
		
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
	  //??
	}
	
	protected function _init_dataspace($request)
	{
		parent :: _init_dataspace($request);
		
		$ini = get_ini('image_variations.ini');
		
		$image_variations = $ini->get_all();
		
		foreach($image_variations as $variation => $variation_data)
		{
			if(isset($variation_data['max_size']))
			{
				$this->dataspace->set('upload_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
				$this->dataspace->set('generate_' . $variation . '_max_size', isset($variation_data['max_size']) ? $variation_data['max_size'] : '');
			}
		}
	}
	
	protected function _update_object_operation()
	{	
		$this->object->set('files_data', $_FILES[$this->name]);
		
	  try
	  {
	    $this->object->update_variations();
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