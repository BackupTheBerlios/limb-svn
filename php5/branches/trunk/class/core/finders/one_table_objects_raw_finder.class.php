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
require_once(LIMB_DIR . '/class/core/finders/site_objects_raw_finder.class.php');

abstract class one_table_objects_raw_finder extends site_objects_raw_finder
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
  
  abstract protected function _define_db_table_name();
  
  public function find($params=array(), $sql_params=array())
  {
    $db_table = $this->get_db_table();
    
    $sql_params['columns'][] = ' ' . $db_table->get_columns_for_select('tn', array('id')) . ',';
    
    $table_name = $db_table->get_table_name();
    $sql_params['tables'][] = ",{$table_name} as tn";
    
    $sql_params['conditions'][] = 'AND sso.id=tn.object_id';
    
    return $this->_do_parent_find($params, $sql_params);
  }
  
  //for mocking
  protected function _do_parent_find($params, $sql_params)
  {
    return parent :: find($params, $sql_params);
  }
  
  protected function _do_parent_count($sql_params)
  {
    return parent :: count($sql_params);
  }
  
  public function count($sql_params=array())
  {
    $db_table = $this->get_db_table();
    $table_name = $db_table->get_table_name();
    $sql_params['tables'][] = ",{$table_name} as tn";
    
    $sql_params['conditions'][] = 'AND sso.id=tn.object_id';
    
    return $this->_do_parent_count($sql_params);
  }
}

?>