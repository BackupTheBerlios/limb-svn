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
require_once(LIMB_DIR . '/class/core/site_objects/content_object.class.php');

abstract class media_object extends content_object
{	

	protected function _create_media_record($tmp_file_path, $file_name, $mime_type)
	{
		if(!file_exists($tmp_file_path))
		  throw new FileNotFoundException('tmp file not found', $tmp_file_path);

		srand(time());
		$media_id = md5(uniqid(rand()));

		fs :: mkdir(MEDIA_DIR);
	
		if (!copy($tmp_file_path, MEDIA_DIR . $media_id . '.media'))
		{
		  throw new IOException('copy failed', 
			  array(
			  	'dst' => MEDIA_DIR . $media_id . '.media',
			  	'src' => $tmp_file_path
			  	)
			);
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
				
		$media_db_table = Limb :: toolkit()->createDBTable('media');
		
  	$media_db_table->insert(
  		array(
  			'id' => $media_id,
  			'file_name' => $file_name,
  			'mime_type' => $mime_type,
  			'size' => filesize($tmp_file_path),
  			'etag' => $etag,
  		));
		
		$this->set('etag', $etag);
		
		return $media_id;
	}

	protected function _update_media_record($id, $tmp_file_path, $file_name, $mime_type)
	{
		if(!file_exists($tmp_file_path))
		  throw new FileNotFoundException('tmp file not found', $tmp_file_path);
			
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
		  throw new IOException('copy failed', 
			  array(
			  	'dst' => MEDIA_DIR . $media_id . '.media',
			  	'src' => $tmp_file_path
			  	)
			);
		}

		$media_db_table = Limb :: toolkit()->createDBTable('media');
		
  	$media_db_table->update_by_id(
  		$id,
  		array(
  			'file_name' => $file_name,
  			'mime_type' => $mime_type,
  			'size' => filesize($tmp_file_path),
  			'etag' => $etag,
  		));
  		
  	$this->set('etag', $etag);
	}				
}

?>
