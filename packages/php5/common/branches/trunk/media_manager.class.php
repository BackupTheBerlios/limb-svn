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
class MediaManager
{ 
  public function getMediaIdFilePath($media_id)
  {
    return MEDIA_DIR . $media_id . '.media';
  }
  
	public function createMediaRecord($disk_file_path, $file_name, $mime_type)
	{
		if(!file_exists($disk_file_path))
		  throw new FileNotFoundException('file not found', $disk_file_path);

		srand(time());
		$media_id = md5(uniqid(rand()));

		fs :: mkdir(MEDIA_DIR);
	
		if (!copy($disk_file_path, $this->getMediaIdFilePath($media_id)))
		{
		  throw new IOException('copy failed', 
			  array(
			  	'dst' => $this->getMediaIdFilePath($media_id),
			  	'src' => $disk_file_path
			  	)
			);
		}
    
		$etag = md5_file($disk_file_path);
				
		$media_db_table = Limb :: toolkit()->createDBTable('media');
		
    $record = array(
  			'id' => $media_id,
  			'file_name' => $file_name,
  			'mime_type' => $mime_type,
  			'size' => filesize($disk_file_path),
  			'etag' => $etag,
  		); 
    
  	$media_db_table->insert($record);
		
		return $record;
	}
  
  protected function _overwriteMediaFile($media_id, $disk_file_path)
  {
		if(!file_exists($disk_file_path))
		  throw new FileNotFoundException('file not found', $disk_file_path);

		fs :: mkdir(MEDIA_DIR);

		if(!copy($disk_file_path, $this->getMediaIdFilePath($media_id)))
		{
		  throw new IOException('copy failed', 
			  array(
			  	'dst' => $media_path,
			  	'src' => $disk_file_path
			  	)
			);
		}    
  }

	protected function _updateDBMediaRecord($media_id, $file_name, $mime_type, $size, $etag)
	{			
		$media_db_table = Limb :: toolkit()->createDBTable('media');
		
  	$media_db_table->update_by_id(
  		$media_id,
  		array(
  			'file_name' => $file_name,
  			'mime_type' => $mime_type,
  			'size' => $size,
  			'etag' => $etag,
  		));
	}				  

	public function updateMediaRecord($media_id, $file_path, $file_name, $mime_type)
	{			
    $this->_overwriteMediaFile($media_id, $file_path);

    $this->_updateDBMediaRecord($media_id, 
                             $file_name, 
                             $mime_type, 
                             $size = filesize($file_path), 
                             $etag = md5_file($file_path));
    
    return array('file_name' => $file_name, 
                 'mime_type' => $mime_type, 
                 'size' => $size, 
                 'etag' => $etag);
	}				  
  
}


?> 
