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
require_once(LIMB_DIR . '/class/core/data_mappers/one_table_objects_mapper.class.php');

class image_object_mapper extends one_table_objects_mapper
{
  protected function _get_finder()
  {
    include_once(dirname(__FILE__) . '/../finders/image_objects_raw_finder.class.php');
    return new image_objects_raw_finder();    
  }
  
  protected function _create_domain_object()
  {
    include_once(dirname(__FILE__) . '/../site_objects/image_object.class.php');
    return new image_object();
  }

  protected function _define_db_table_name()
  {
    return 'image_object';
  }
  
  protected function _do_load($result_set, $domain_object)
  {
    $variations_data = $result_set['variations'];
    unset($result_set['variations']);
    
    $domain_object->import($result_set);
    
    $this->_attach_variations($variations_data, $domain_object);
  }
  
  protected function _attach_variations($variations_data, $domain_object)
  {
    include_once(dirname(__FILE__) . '/../image_variation.class.php');
    
    foreach($variations_data as $key => $data)
    {
      $variation = new image_variation();
      $variation->import($data);
      $domain_object->attach_variation($variation);
    }
  }
  
  public function insert($domain_object)
  {
    $this->_do_parent_insert($domain_object);
    
    $this->_insert_variations($domain_object);
  }

  public function update($domain_object)
  {
    $this->_do_parent_update($domain_object);
    
    $this->_update_variations($domain_object);
  }

  protected function _update_variations($domain_object)
  {
    $variations = $domain_object->get_variations();
    
    $media_db_table = Limb :: toolkit()->createDBTable('media'); 
    $variation_db_table = Limb :: toolkit()->createDBTable('image_variation'); 
    
    foreach($variations as $variation)
    {
      if($variation->get_id())
      {
        $this->_update_variation($domain_object, $variation);
      }
      else
      {
        $this->_insert_variation($domain_object, $variation);
      }
    }
  }
  
  protected function _update_variation($domain_object, $variation)
  {
    $media_db_table = Limb :: toolkit()->createDBTable('media'); 
    $variation_db_table = Limb :: toolkit()->createDBTable('image_variation'); 
    
    $old_variation = $variation_db_table->get_row_by_id($variation->get_id());
    
    if($old_variation['media_id'] != $variation->get_media_id())
    {
      $this->_get_media_manager()->unlink_media($old_variation['media_id']);
      $media_db_table->delete_by_id($old_variation['media_id']);
      
      $media_record = array('id' => $variation->get_media_id(),
                            'file_name' => $variation->get_file_name(), 
                            'mime_type' => $variation->get_mime_type(),
                            'size' => $variation->get_size(),
                            'etag' => $variation->get_etag());
    
      $media_db_table->insert($media_record);
      
    }  
    else
    {
      $media_record = array('file_name' => $variation->get_file_name(), 
                            'mime_type' => $variation->get_mime_type(),
                            'size' => $variation->get_size(),
                            'etag' => $variation->get_etag());
      
      $media_db_table->update_by_id($variation->get_media_id(), $media_record);
    }
    
    $image_variation_record = array('image_id' => $domain_object->get_id(), 
                                    'media_id' => $variation->get_media_id(), 
                                    'width' => $variation->get_width(),
                                    'height' => $variation->get_height(),
                                    'variation' => $variation->get_name());

    $variation_db_table->update_by_id($variation->get_id(), $image_variation_record);    
  }  
  
  protected function _insert_variations($domain_object)
  {
    $variations = $domain_object->get_variations();
        
    foreach($variations as $variation)
    {
      $this->_insert_variation($domain_object, $variation);
    }
  }
  
  protected function _insert_variation($domain_object, $variation)
  {
    $media_db_table = Limb :: toolkit()->createDBTable('media'); 
    $variation_db_table = Limb :: toolkit()->createDBTable('image_variation'); 

    $media_record = array('id' => $variation->get_media_id(), 
                          'file_name' => $variation->get_file_name(), 
                          'mime_type' => $variation->get_mime_type(),
                          'size' => $variation->get_size(),
                          'etag' => $variation->get_etag());
    
    $media_db_table->insert($media_record);
    
    $image_variation_record = array('image_id' => $domain_object->get_id(), 
                          'media_id' => $variation->get_media_id(), 
                          'width' => $variation->get_width(),
                          'height' => $variation->get_height(),
                          'variation' => $variation->get_name());

    $variation_db_table->insert($image_variation_record);    
  }
  
  protected function _get_media_manager()
  {
    include_once(dirname(__FILE__) . '/../media_manager.class.php');
    return new media_manager();
  }
  
}

?> 
