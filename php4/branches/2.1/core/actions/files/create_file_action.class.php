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

class create_file_action extends form_create_site_object_action
{
	function create_file_action()
	{
		$definition = array(
			'site_object' => 'file_object',
			'datamap' => array(
				'description' => 'description',
			)
		);
		
		parent :: form_create_site_object_action('create_file', $definition);
	}

	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
	}
	
	function _create_object_operation()
	{	
		if(isset($_FILES[$this->name]['tmp_name']['file']))
		{	
			if(($_FILES[$this->name]['size']['file']) > ini_get('upload_max_filesize')*1024*1024)
			{
				message_box :: write_warning('uploaded file size exceeds limit');
				return false;
			}
			
			$this->object->set_attribute('tmp_file_path', $_FILES[$this->name]['tmp_name']['file']);
			$this->object->set_attribute('file_name', $_FILES[$this->name]['name']['file']);
			$this->object->set_attribute('mime_type', $_FILES[$this->name]['type']['file']);
		}

		return parent :: _create_object_operation();
	}
	
}

?>