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

class image_object extends content_object
{
  protected $_image_library;
  protected $_media_manager;
  protected $_variations = array();
  
  public function get_variations()
  {
    return $this->_variations;
  }

  public function get_variation($variation)
  {
    if(isset($this->_variations[$variation]))
      $this->_variations[$variation];
  }

  protected function _get_image_library()
  {
    if($this->_image_library)
      return $this->_image_library;
    
    include_once(LIMB_DIR . '/class/lib/image/image_factory.class.php');
    $this->_image_library = image_factory :: create();
    
    return $this->_image_library;
  }

  protected function _get_media_manager()
  {
    if($this->_media_manager)
      return $this->_media_manager;
    
    include_once(dirname(__FILE__) . '/../media_manager.class.php');
    $this->_media_manager = new MediaManager();

    return $this->_media_manager;    
  }
  
  public function set_variation_file_path($variation, $path)
  {
    $this->set_by_index_string("[variations][$variation][path]", $path);
  }
  
  public function get_variation_file_path($variation)
  {
    return $this->get_by_index_string("[variations][$variation][path]");
  }

  public function set_variation_file_name($variation, $file_name)
  {
    $this->set_by_index_string("[variations][$variation][file_name]", $file_name);
  }
  
  public function get_variation_file_name($variation)
  {
    return $this->get_by_index_string("[variations][$variation][file_name]");
  }

  public function set_variation_mime_type($variation, $mime_type)
  {
    $this->set_by_index_string("[variations][$variation][mime_type]", $mime_type);
  }
    
  public function get_variation_mime_type($variation)
  {
    return $this->get_by_index_string("[variations][$variation][mime_type]");
  }
      
  public function create($is_root = false)
  {       
    $id = $this->_do_parent_create($is_root);
                  
    return $id;
  }
  
  //for mocking
  protected function _do_parent_create($is_root)
  {
    return parent :: create($is_root);
  }
  
  protected function _validate_variation_data($variation)
  {
    return (!is_null($this->get_variation_file_path($variation)) && 
            !is_null($this->get_variation_file_name($variation)) &&
            !is_null($this->get_variation_mime_type($variation)));
  }
  
  public function upload_variation($variation, $max_size = null)
  {
    if(!$this->_validate_variation_data($variation))
      throw new LimbException('variation data error', array('variation' => $variation));
    
    $this->_create_variation_operation($variation);
    
    if($max_size)
      $this->_resize_variation_operation($variation, $variation, $max_size);
  }
  
  public function generate_variation($variation_src, $variation_dest, $max_size)
  { 
    if(!$this->_validate_variation_data($variation_src))
      throw new LimbException('variation data error', array('variation' => $variation_src));
    
    $this->_resize_variation_operation($variation_src, $variation_dest, $max_size);                            
  }  
      
  protected function _create_variation_operation($variation)
  {
    $disk_file_path = $this->get_variation_file_path($variation);
    $file_name = $this->get_variation_file_name($variation); 
    $mime_type = $this->get_variation_mime_type($variation);
    
    $this->_create_variation_from_file($variation, $disk_file_path, $file_name, $mime_type);    
  }
  
  protected function _resize_variation_operation($variation_src, $variation_dest, $max_size)
  {
    $output_file = tempnam(VAR_DIR, 'p');
    
    $image_library = $this->_get_image_library();
    
    $media_data = $this->_get_variation_db_media_data($variation_src);
    
    $input_file_type = $image_library->get_image_type($media_data['mime_type']);  
    $output_file_type = $image_library->find_type_create_support_gracefully($input_file_type);
    
    $input_file = MediaManager :: getMediaFilePath($media_data['id']); 
     
    try
    { 
      $image_library->set_input_file($input_file);
      $image_library->set_input_type($input_file_type);
      
      $image_library->set_output_file($output_file);
      $image_library->set_output_type($output_file_type);
       
      $image_library->resize(array('max_dimension' => $max_size));//ugly!!! 
      $image_library->commit();      
    }
    catch(Exception $e)
    {
      if(file_exists($output_file))
        unlink($output_file);
      throw $e;
    }
    
    if(!$this->_get_variation_db_media_data($variation_dest))
    {
      $this->_create_variation_from_file(
        $variation_dest, 
        $output_file,
        $this->get_variation_file_name($variation_src),//note this
        $image_library->get_mime_type($output_file_type)
      );      
    }
    else
    {
      $this->_update_variation_from_file(
        $variation_dest, 
        $output_file,
        $this->get_variation_file_name($variation_dest),
        $image_library->get_mime_type($output_file_type)
      );
    }

    unlink($output_file);    
  }
  
  protected function _update_variation_from_file($variation, $disk_file_path, $file_name, $mime_type)
  {
    $media_data = $this->_get_variation_db_media_data($variation);

    $this->_media_manager->updateMediaRecord($media_data['id'], 
                                                      $disk_file_path, 
                                                      $file_name, 
                                                      $mime_type);
    
    $size = getimagesize($disk_file_path);
    
    $db_table = Limb :: toolkit()->createDBTable('image_variation');
    
    $db_table->update(array('width' => $size[0], 
                            'height' => $size[1]),
                      array('image_id' => $this->get_id(), 
                            'variation' => $variation)
    );
  }
  
  protected function _create_variation_from_file($variation, $disk_file_path, $file_name, $mime_type)
  {
    $media_id = $this->_get_media_manager()->createMediaRecord($disk_file_path, $file_name, $mime_type);
    
    $size = getimagesize($disk_file_path);
    
    $image_variation_data = array();
    $image_variation_data['id'] = null;
    $image_variation_data['image_id'] = $this->get_id();
    $image_variation_data['media_id'] = $media_id;
    $image_variation_data['width'] = $size[0];
    $image_variation_data['height'] = $size[1];
    $image_variation_data['variation'] = $variation;
    
    $image_variation_db_table = Limb :: toolkit()->createDBTable('image_variation');
    
    $image_variation_db_table->insert($image_variation_data);    
  }    
        
  protected function _get_output_image_variation_file_type($variation)
  {
    $media_data = $this->_get_variation_db_media_data($variation);
    
    $current_type = $this->_image_library->get_image_type($media_data['mime_type']);
    
    return $this->_image_library->find_type_create_support_gracefully($current_type);    
  }
      
  protected function _get_variation_db_media_data($variation)
  {
    $image_id = $this->get_id();
    
    $sql = "SELECT        
            iv.image_id as image_id,
            iv.media_id as media_id, 
            iv.variation as variation, 
            iv.width as width, 
            iv.height as height, 
            m.size as size, 
            m.mime_type as mime_type, 
            m.file_name as file_name, 
            m.etag as etag,
            m.id as id
            FROM image_variation iv, media m
            WHERE iv.image_id='{$image_id}' 
            AND iv.variation='{$variation}' 
            AND iv.media_id=m.id";
    
    $db = Limb :: toolkit()->getDB();
    $db->sql_exec($sql);
    
    return $db->fetch_row();
  }
  
  public function merge($attributes)
  {
    parent :: merge($attributes);
    
    $this->_reload_variations();
  }

  public function import($attributes)
  {
    parent :: import($attributes);
    
    $this->_reload_variations();
  }

  protected function _reload_variations()
  {
    $this->_variations = array();
    
    $raw_data = $this->get('variations', array());
    
    foreach($raw_data as $id => $data)
    {
      $variation = $this->_create_variation();
      $variation->import($data);
      $this->_variations[$id] = $variation;
    }
  }
  
  protected function _create_variation()
  {
    include_once(dirname(__FILE__) . '/../image_variation.class.php');
    return new image_variation();
  }
  
  public function fetch($params=array(), $sql_params=array())
  {
    if(!$records = $this->_do_parent_fetch($params, $sql_params))
      return array();
    
    $images_ids = array();
    
    foreach($records as $record)
      $images_ids[] = "{$record['object_id']}";
      
    $ids = '('. implode(',', $images_ids) . ')';
      
    $sql = "SELECT 
            iv.image_id as image_id,
            iv.media_id as media_id, 
            iv.variation as variation, 
            iv.width as width,  
            iv.height as height, 
            m.size as size, 
            m.mime_type as mime_type, 
            m.file_name as file_name, 
            m.etag as etag,
            m.id as id
            FROM image_variation iv, media m
            WHERE iv.media_id = m.id AND 
            iv.image_id IN {$ids}";
    
    $db = Limb :: toolkit()->getDB();
    
    $db->sql_exec($sql);
    
    if(!$images_variations = $db->get_array())
      return $records;
      
    foreach($images_variations as $variation_data)
    {
      foreach($records as $id => $record)
      {
        if($record['object_id'] == $variation_data['image_id'])
        {
          $records[$id]['variations'][$variation_data['variation']] = $variation_data;
          break;
        }
      }
    }
    
    return $records;
  } 
  
  //for mocking
  protected function _do_parent_fetch($params, $sql_params)
  {
    return parent :: fetch($params, $sql_params);
  }
}

?>
