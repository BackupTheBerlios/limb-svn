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
require_once(LIMB_DIR . 'class/core/actions/form_edit_site_object_action.class.php');

class edit_file_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'file_object';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'edit_file';
	}
  
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'description' => 'description',
	      )
	  );     
	}
	
	protected function _define_increase_version_flag()
	{
	  return false;
	}  

	protected function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'title'));
	}
	
	protected function _update_object_operation()
	{	
		if(isset($_FILES[$this->name]['tmp_name']['file']))
		{	
			if(($_FILES[$this->name]['size']['file']) > ini_get('upload_max_filesize')*1024*1024)
			{
				message_box :: write_warning('uploaded file size exceeds limit');
				return false;
			}
			
			$object_data = fetch_requested_object();
			
			$this->object->set('media_id', $object_data['media_id']);
			$this->object->set('tmp_file_path', $_FILES[$this->name]['tmp_name']['file']);
			$this->object->set('file_name', $_FILES[$this->name]['name']['file']);
			$this->object->set('mime_type', $_FILES[$this->name]['type']['file']);
		}

		return parent :: _update_object_operation();
	}
}

?>