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
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class media_object extends content_object
{	
	function _define_class_properties()
	{
		return array(
		  'abstract_class' => true,
		  'db_table_name' => 'empty',
		);
	}
		
	function _create_media_record($tmp_file_path, $file_name, $mime_type)
	{
		if(!file_exists($tmp_file_path))
		{
		  debug :: write_error('tmp file not found', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			  array('tmp_file' => $tmp_file_path));
		  return false;
		}

		srand(time());
		$media_id = md5(uniqid(rand()));

		fs :: mkdir(MEDIA_DIR);
	
		if (!copy($tmp_file_path, MEDIA_DIR . $media_id . '.media'))
		{
		  debug :: write_error('copy failed', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			  array(
			  	'dst' => MEDIA_DIR . $media_id . '.media',
			  	'src' => $tmp_file_path
			  	));
		  return false;
		}

		if (function_exists('md5_file'))
			$etag = md5_file($tmp_file_path);
		else
		{
			$fd = fopen($tmp_file_path, 'rb');
			$contents = fread($fd, filesize($tmp_file_path));
			fclose($fd);
			$etag = md5($contents);
		}
				
		$media_db_table = db_table_factory :: instance('media');
		
  	$media_db_table->insert(
  		array(
  			'id' => $media_id,
  			'file_name' => $file_name,
  			'mime_type' => $mime_type,
  			'size' => filesize($tmp_file_path),
  			'etag' => $etag,
  		));
		
		$this->set_attribute('etag', $etag);
		
		return $media_id;
	}

	function _update_media_record($id, $tmp_file_path, $file_name, $mime_type)
	{
		if(!file_exists($tmp_file_path))
		{
		  debug :: write_error('file doesnt exist', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			  array('tmp' => $tmp_file_path));
		  return false;
		}
			
		if (function_exists('md5_file'))
		{
			$etag = md5_file($tmp_file_path);
		}
		else
		{
			$fd = fopen($data['tmp_name'], 'rb');
			$contents = fread($fd, filesize($tmp_file_path));
			fclose($fd);
			$etag = md5($contents);
		}
		
		fs :: mkdir(MEDIA_DIR);
		
		if(!copy($tmp_file_path, MEDIA_DIR . $id .'.media'))
		{
			debug :: write_error('temporary file copy failed', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			array(
				'src' => $tmp_file_path,
				'dst' => MEDIA_DIR . $id .'.media',
			));
			
			return false;
		}

		$media_db_table = db_table_factory :: instance('media');
		
  	$media_db_table->update_by_id(
  		$id,
  		array(
  			'file_name' => $file_name,
  			'mime_type' => $mime_type,
  			'size' => filesize($tmp_file_path),
  			'etag' => $etag,
  		));
  		
  	$this->set_attribute('etag', $etag);
  		
		return true;
	}				
}

?>
