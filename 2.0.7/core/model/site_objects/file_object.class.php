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
require_once(LIMB_DIR . 'core/model/site_objects/media_object.class.php');

class file_object extends media_object
{	
	function file_object()
	{
		parent :: media_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'file_object_controller',
		);
	}
	
	function create()
	{
		if(!$this->create_file())
			return false;
				
		if(($id = parent :: create()) === false)
			return false;
							
		return $id;
	}
	
	function update($force_create_new_version = true)
	{
		if(!$this->update_file())
			return false;
			
		return parent :: update($force_create_new_version);
	}
	
	function create_file()
	{
		$tmp_file_path = $this->get_attribute('tmp_file_path');
		$file_name = $this->get_attribute('file_name');
		$mime_type = $this->get_attribute('mime_type');
		
		if(($media_id = $this->_create_media_record($tmp_file_path, $file_name, $mime_type)) === false)
		  return false;
		
		$this->set_attribute('media_id', $media_id);
		
		return true;
	}
	
	function update_file()
	{
		$tmp_file_path = $this->get_attribute('tmp_file_path');
		$file_name = $this->get_attribute('file_name');
		$mime_type = $this->get_attribute('mime_type');
		
		if(!$media_id = $this->get_attribute('media_id'))
		{
		  debug :: write_error('media id not set', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
			  );
		  return false;
		}

		if(!$this->_update_media_record($media_id, $tmp_file_path, $file_name, $mime_type))
		  return false;
		
		return true;
	}
	
	function & fetch($params=array(), $sql_params=array())
	{
		$sql_params['columns'][] = ', m.file_name, m.mime_type, m.etag, m.size ';
		$sql_params['tables'][] = ', media as m ';
		$sql_params['conditions'][] = ' AND tn.media_id=m.id ';
		
		$records = parent :: fetch($params, $sql_params);
		
		return $records;								
	}
	
}

?>
