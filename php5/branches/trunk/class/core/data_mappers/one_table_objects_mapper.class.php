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
require_once(LIMB_DIR . '/class/core/data_mappers/site_object_mapper.class.php');

class one_table_objects_mapper extends site_object_mapper
{
  public function recover_version($version)
  {
    if(!$version_data = $this->fetch_version($version))
      throw new LimbException('version record not found', 
        array(
          'class_name' => get_class($this),
          'id' => $this->get_id(),
          'node_id' => $this->get_node_id(),
          'version' => $version,
        )
      );
     
    unset($version_data['version']); 
    $this->merge($version_data);
    
    $this->update();
  }

  public function fetch_version($version)
  {
    if(!$arr = $this->get_db_table()->get_list(array('object_id' => $this->get_id(),
                                                 'version' => $version)))
    {
      return false;
    }
      
    $result = reset($arr);
    unset($result['id']);
    
    return $result;
  }
  
  function trim_versions()
  {
    $this->get_db_table()->delete('object_id = ' . $this->get_id() . 
                                  ' AND version <> ' . $this->get_version());
  }
  
  protected function _create_version_record($site_object)
  {
    $version_db_table = Limb :: toolkit()->createDBTable('sys_object_version');
    
    $time = time();
    
    $data['id'] = null;
    $data['object_id'] = $site_object->get_id();
    $data['version'] = $site_object->get_version();
    $data['created_date'] = $time;
    $data['modified_date'] = $time;
    $data['creator_id'] = $site->get_creator_id();
    
    $version_db_table->insert($data);
  }
      
  protected function _create_versioned_content_record($site_object)
  {
    $data = $site_object->export();
    
    $data['object_id'] = $site_object->get_id();
        
    $this->get_db_table()->insert($data);
  }
  
  protected function _update_versioned_content_record()
  {
    $data['version'] = $this->get_version();
    $data['object_id'] = $this->get_id();

    $db_table = $this->get_db_table();
    
    $row = current($db_table->get_list($data));
    
    if($row === false)
      throw new LimbException('content record not found', 
              array(
                'version' => $data['version'],
                'object_id' => $data['object_id'],
                'class_name' => get_class($this)));
    
    $id = $row['id'];

    $data = $this->_attributes->export();
    unset($data['id']);

    $db_table->update_by_id($id, $data);
  }

  //for mocking
  protected function _do_parent_create($site_object)
  {
    return parent :: create($site_object);
  }
  
  //for mocking
  protected function _do_parent_update($site_object)
  {
    parent :: update($site_object);
  }

  //for mocking
  protected function _do_parent_delete($site_object)
  {
    parent :: delete($site_object);
  }
  
  public function update($site_object)
  {
    // Need to check if we need to create new version record
    
    $this->_do_parent_update($site_object);
    
    if ($force_create_new_version)
    {
      $this->_create_version_record($site_object);
    
      $this->_create_versioned_content_record($site_object);
    }
    else
      $this->_update_versioned_content_record($site_object);
  }
  
  public function insert($site_object)
  {
    $id = $this->_do_parent_create($site_object);
      
    $this->_create_version_record($site_object);
     
    $this->_create_versioned_content_record($site_object);
    
    return $id;
  }
        
  public function delete($site_object)
  {
    $this->_do_parent_delete($site_object);

    $this->_delete_versioned_content_records($site_object);
  }

  protected function _delete_versioned_content_records($site_object)
  {
    $db_table = $this->get_db_table();  
    $db_table->delete(array('object_id' => $site_object->get_id()));
  } 
}

?>