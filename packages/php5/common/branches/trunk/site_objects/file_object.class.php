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
require_once(dirname(__FILE__) . '/media_object.class.php');

class file_object extends media_object
{	
	protected function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'file_object_controller',
		);
	}
	
	public function create()
	{
		$this->_create_file();
				
		return parent :: create();
	}
	
	public function update($force_create_new_version = true)
	{		
		$this->_update_file();
			
		parent :: update($force_create_new_version);
	}
	
	protected function _create_file()
	{
		$tmp_file_path = $this->get('tmp_file_path');
		$file_name = $this->get('file_name');
		$mime_type = $this->get('mime_type');
		
		$media_id = $this->_create_media_record($tmp_file_path, $file_name, $mime_type);
		
		$this->set('media_id', $media_id);
	}
	
	protected function _update_file()
	{
		$tmp_file_path = $this->get('tmp_file_path');
		$file_name = $this->get('file_name');
		$mime_type = $this->get('mime_type');
		
		if(!$media_id = $this->get('media_id'))
   	  throw new LimbException('media id not set');

	  $this->_update_media_record($media_id, $tmp_file_path, $file_name, $mime_type);
	}
	
	public function fetch($params=array(), $sql_params=array())
	{
		$sql_params['columns'][] = ' m.file_name as file_name, m.mime_type as mime_type, m.etag as etag, m.size as size, ';
		$sql_params['tables'][] = ', media as m ';
		$sql_params['conditions'][] = ' AND tn.media_id=m.id ';
		
		$records = parent :: fetch($params, $sql_params);
		
		return $records;								
	}
	
}

?>
