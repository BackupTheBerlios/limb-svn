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
require_once(LIMB_DIR . '/class/core/dataspace.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');

class content_object extends site_object
{
  protected  $_db_table = null;
    
  public function get_db_table()
  {
    if(!$this->_db_table)
    {
      $db_table_name = $this->_define_db_table_name();
        
      $this->_db_table = Limb :: toolkit()->createDBTable($db_table_name);
    } 
      
    return $this->_db_table;
  }
  
  protected function _define_db_table_name()
  {
    return get_class($this);
  }
  
  public function fetch($params=array(), $sql_params=array())
  {
    $db_table = $this->get_db_table();
    
    $sql_params['columns'][] = ' ' . $db_table->get_columns_for_select('tn', array('id')) . ',';
    
    $table_name = $db_table->get_table_name();
    $sql_params['tables'][] = ",{$table_name} as tn";
    
    $sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
    
    return $this->_do_parent_fetch($params, $sql_params);
  }
  
  //for mocking
  protected function _do_parent_fetch($params, $sql_params)
  {
    return parent :: fetch($params, $sql_params);
  }
  
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
  
  protected function _do_parent_fetch_count($sql_params)
  {
    return parent :: fetch_count($sql_params);
  }
  
  public function fetch_count($sql_params=array())
  {
    $db_table = $this->get_db_table();
    $table_name = $db_table->get_table_name();
    $sql_params['tables'][] = ",{$table_name} as tn";
    
    $sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
    
    return $this->_do_parent_fetch_count($sql_params);
  }
    
  protected function _create_version_record()
  {
    $version_db_table = Limb :: toolkit()->createDBTable('sys_object_version');
    
    $time = time();
    
    $data['id'] = null;
    $data['object_id'] = $this->get_id();
    $data['version'] = $this->get_version();
    $data['created_date'] = $time;
    $data['modified_date'] = $time;
    $data['creator_id'] = Limb :: toolkit()->getUser()->get_id();
    
    $version_db_table->insert($data);
  }
      
  protected function _create_versioned_content_record()
  {
    $data = $this->_attributes->export();
    
    $data['object_id'] = $this->get_id();
        
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
  protected function _do_parent_create($is_root)
  {
    return parent :: create($is_root);
  }
  
  //for mocking
  protected function _do_parent_update($force_create_new_version)
  {
    parent :: update($force_create_new_version);
  }

  //for mocking
  protected function _do_parent_delete()
  {
    parent :: delete();
  }
  
  public function update($force_create_new_version = true)
  {
    $this->_do_parent_update($force_create_new_version);
    
    if ($force_create_new_version)
    {
      $this->_create_version_record();
    
      $this->_create_versioned_content_record();
    }
    else
      $this->_update_versioned_content_record();
  }
  
  public function create($is_root = false)
  {
    $id = $this->_do_parent_create($is_root);
      
    $this->_create_version_record();
     
    $this->_create_versioned_content_record();
    
    return $id;
  }
        
  public function delete()
  {
    $this->_do_parent_delete();

    $this->_delete_versioned_content_records();
  }

  protected function _delete_versioned_content_records()
  {
    $db_table = $this->get_db_table();  
    $db_table->delete(array('object_id' => $this->get_id()));
  } 
}

?>