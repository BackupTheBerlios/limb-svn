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
require_once(LIMB_DIR . '/class/core/object.class.php');

class image_variation extends object
{
  protected $_media_manager;
  protected $_image_library;
  
  public function attach_to_image_object($object)
  {
    $this->set_image_id($object->get_id());
  }
  
  protected function _get_media_manager()
  {
    if($this->_media_manager)
      return $this->_media_manager;
    
    include_once(dirname(__FILE__) . '/media_manager.class.php');
    $this->_media_manager = new MediaManager();

    return $this->_media_manager;    
  }
  
  protected function _get_image_library()
  {
    if($this->_image_library)
      return $this->_image_library;
    
    include_once(LIMB_DIR . '/class/lib/image/image_factory.class.php');
    $this->_image_library = image_factory :: create();
    
    return $this->_image_library;
  }  
  
  public function get_current_file()
  {
    return $this->_get_media_manager()->getMediaIdFilePath($this->get_media_id());
  }

  public function get_file_type()
  {
    return $this->_get_image_library()->get_image_type($this->get_mime_type());
  }
  
  public function resize($max_size)
  {
    if(!$input_file = $this->get_input_file())
      $input_file = $this->get_current_file();
    
    $output_file = tempnam(VAR_DIR, 'p');
    
    $image_library = $this->_get_image_library();
    
    $input_file_type = $image_library->get_image_type($this->get_mime_type());  
    $output_file_type = $image_library->fall_back_to_any_supported_type($input_file_type);
    
    try
    { 
      $image_library->set_input_file($input_file);
      $image_library->set_input_type($input_file_type);
      
      $image_library->set_output_file($output_file);
      $image_library->set_output_type($output_file_type);
      $image_library->resize(array('max_dimension' => $max_size));//ugly!!! 
      $image_library->commit();
      
      $this->set_input_file($output_file);

      $this->store();      
    }
    catch(Exception $e)
    {
      if(file_exists($output_file))
        unlink($output_file);
      throw $e;
    }

    unlink($output_file);    
  }
  
  public function store()
  {
    $variation_table = Limb :: toolkit()->createDBTable('image_variation');
        
    if(!$id = $this->get_id())
    {      
      if($this->get_input_file())
      {        
        $this->_create_media_record();
        $this->_update_dimensions_with_input_file();
        $this->reset_input_file();
      }
      else
        throw new LimbException('input file not found');
        
      complex_array :: map($this->_get_variation_db_map(), $this->export(), $variation_data);    
      
      $variation_table->insert($variation_data);
    }
    else
    {
      if($this->get_input_file())
      {        
        $this->_update_media_record();
        $this->_update_dimensions_with_input_file();
        $this->reset_input_file();        
      }
      
      complex_array :: map($this->_get_variation_db_map(), $this->export(), $variation_data);
       
      $variation_table->update($variation_data, array('id' => $id));
    }
  }
  
  protected function _create_media_record()
  {
    $media_record = $this->_get_media_manager()->createMediaRecord($this->get_input_file(), 
                                                                   $this->get_file_name(), 
                                                                   $this->get_mime_type());
    
    $this->set_media_id($media_record['id']);
    $this->set_etag($media_record['etag']);
    $this->set_size($media_record['size']);
  }

  protected function _update_media_record()
  {
    $media_record = $this->_get_media_manager()->updateMediaRecord($this->get_media_id(),
                                                                   $this->get_input_file(), 
                                                                   $this->get_file_name(), 
                                                                   $this->get_mime_type());
    
    $this->set_etag($media_record['etag']);
    $this->set_size($media_record['size']);
  }
  
  protected function _update_dimensions_with_input_file()
  {
    $size = getimagesize($this->get_input_file());
    $this->set_width($size[0]);
    $this->set_height($size[1]);        
  }
  
  public function get_id()
  {
    return (int)$this->get('id');
  }   
  
  public function set_id($id)
  {
    $this->set('id', (int)$id);
  }  
    
  public function get_etag()
  {
    return $this->get('etag');
  }   
  
  public function set_etag($etag)
  {
    $this->set('etag', $etag);
  }

  public function get_name()
  {
    return $this->get('name');
  }   
  
  public function set_name($name)
  {
    $this->set('name', $name);
  }

  public function get_width()
  {
    return (int)$this->get('width');
  }   
  
  public function set_width($width)
  {
    $this->set('width', (int)$width);
  }
  
  public function get_height()
  {
    return (int)$this->get('height');
  }   
  
  public function set_height($height)
  {
    $this->set('height', (int)$height);
  }
  
  public function get_mime_type()
  {
    return $this->get('mime_type');
  }   
  
  public function set_mime_type($mime_type)
  {
    $this->set('mime_type', $mime_type);
  }

  public function get_file_name()
  {
    return $this->get('file_name');
  }   
  
  public function set_file_name($file_name)
  {
    $this->set('file_name', $file_name);
  }
  
  public function get_image_id()
  {
    return (int)$this->get('image_id');
  }   
  
  public function set_image_id($image_id)
  {
    $this->set('image_id', (int)$image_id);
  }

  public function get_media_id()
  {
    return $this->get('media_id');
  }   
  
  public function set_media_id($media_id)
  {
    $this->set('media_id', $media_id);
  }
  
  public function get_size()
  {
    return (int)$this->get('size');
  }   
  
  public function set_size($size)
  {
    $this->set('size', (int)$size);
  }

  public function reset_input_file()
  {
    $this->destroy('input_file');
  }

  public function set_input_file($file)
  {
    $this->set('input_file', $file);
  }

  public function get_input_file()
  {
    return $this->get('input_file');
  }
  
	protected function _get_variation_db_map()
	{
	  return array(
	    'id' => 'id',
	    'name' => 'variation',
      'media_id' => 'media_id', 
      'image_id' => 'image_id',  
      'width' => 'width',
      'height' => 'height'
	  );
	}  
}

?> 
