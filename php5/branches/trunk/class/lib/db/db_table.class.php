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
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

class db_table
{
	protected $_db_table_name;
	
	protected $_primary_key_name;
	
	protected $_columns = array();
	
	protected $_constraints = array();

  protected $_db = null;

  function __construct()
  {
  	$this->_db_table_name = $this->_define_db_table_name();
    $this->_columns = $this->_define_columns();
    $this->_constraints = $this->_define_constraints();
    $this->_primary_key_name = $this->_define_primary_key_name();
   
    $this->_db = db_factory :: instance();
  }
      
  protected function _define_db_table_name()
  {
  	$class_name = get_class($this);
  	
  	if(($pos = strpos($class_name, '_db_table')) !== false)
  		$class_name = substr($class_name, 0, $pos);
  	
  	return $class_name;
  }
  
  protected function _define_primary_key_name()
  {
    return 'id';
  }
  
  protected function _define_columns()
  {
  	return array();
  }
  
  protected function _define_constraints()
  {
  	return array();
  }
    
  public function has_column($name)
  {
  	return isset($this->_columns[$name]);
  }
  
  public function get_columns()
  {
  	return $this->_columns;
  }
  
  public function get_constraints()
  {
  	return $this->_constraints;
  }
  
  public function get_column_types()
  {
  	$types = array();
  	foreach(array_keys($this->_columns) as $column_name)
  		$types[$column_name] = $this->get_column_type($column_name);
  		
  	return $types;
  }
  
  public function get_column_type($column_name)
  {
  	if(!$this->has_column($column_name))
  		return false;
  		
  	return (is_array($this->_columns[$column_name]) && isset($this->_columns[$column_name]['type'])) ? 
  		$this->_columns[$column_name]['type'] : 
  		'';
  }
  
  public function get_primary_key_name()
  {
  	return $this->_primary_key_name;
  }
  
  public function insert($row)
  {		
		$filtered_row = $this->_filter_row($row);
		
    return $this->_db->sql_insert($this->_db_table_name, $filtered_row, $this->get_column_types());
  }
  
  protected function _filter_row($row)
  {
    if (!is_array($row))
      return array();
      
  	$filtered = array();
  	foreach($row as $key => $value)
  	{  	    	  
  		if($this->has_column($key))
  			$filtered[$key] = $value;		
  	}
  	return $filtered;
  }
    
  public function update($row, $conditions)
  { 
  	$filtered_row = $this->_filter_row($row);
  	  
    return $this->_db->sql_update($this->_db_table_name, $filtered_row, $conditions, $this->get_column_types());
  } 

  public function update_by_id($id, $data)
  {
  	return $this->update($data, "{$this->_primary_key_name}='{$id}'");
  } 
  
  public function get_row_by_id($id)
  {
		$data = $this->get_list($this->_primary_key_name . "='{$id}'");
		
		return current($data);
  }
        
  public function get_list($conditions='', $order='', $group_by_column='', $start=0, $count=0)
	{			
    $this->_db->sql_select($this->_db_table_name, '*', $conditions, $order, $start, $count);
   
		if ($group_by_column === '')
			$group_by_column = $this->_primary_key_name;
 		 				
		if($group_by_column)
    	return $this->_db->get_array($group_by_column);
    else
    	return $this->_db->get_array();
	}
	
  public function delete($conditions='')
  {  	
		$affected_rows = $this->_prepare_affected_rows($conditions);
		
    $this->_delete_operation($conditions, $affected_rows);
    
    $this->_cascade_delete($affected_rows);
    
    return true;
	}
	
	protected function _delete_operation($conditions, $affected_rows)
	{
		$this->_db->sql_delete($this->_db_table_name, $conditions);
	}
	
	public function delete_by_id($id)
	{
		return $this->delete(array($this->_primary_key_name => $id));
	}
  	  	
  public function get_last_insert_id()
  {		
		return $this->_db->get_sql_insert_id($this->_db_table_name, $this->_primary_key_name);
	}

  public function get_max_id()
  {			
		return $this->_db->get_max_column_value($this->_db_table_name, $this->_primary_key_name);
	}

  public function get_table_name()
  {
		return $this->_db_table_name;
	}
  	
	protected function _cascade_delete($affected_rows)
	{
		if(self :: auto_constraints_enabled())
			return;
			
		if (!count($affected_rows))
			return;

		foreach($this->_constraints as $id => $constraints_array)
		{
			foreach($constraints_array as $key => $constraint_params)
			{
				$table_name = $constraint_params['table_name'];
				$column_name = $constraint_params['field'];
				
				$db_table = Limb :: toolkit()->createDBTable($table_name);
				
				if(!$db_table->has_column($column_name))
				{
		    	throw new SQLException('column not found while cascade deleting',
		    	  null,
		    		array(
		    			'table' => $table_name,
		    			'column_name' => $column_name
		    		)
		    	);
				}
				
				$values = array();
				foreach($affected_rows as $data)
					$values[] = $data[$id];
				
				$db_table->delete(
					sql_in($column_name, $values, $db_table->get_column_type($column_name)));
			}
		}
	}

	protected function _prepare_affected_rows($conditions)
	{
		$affected_rows = array();
		
		if(self :: auto_constraints_enabled())
			return $affected_rows;
			
		return $this->get_list($conditions);
	}
		
	static public function auto_constraints_enabled()
	{
		return (defined('DB_AUTO_CONSTRAINTS') && DB_AUTO_CONSTRAINTS == true);
	}
	
	public function get_columns_for_select($table_name = '', $exclude_columns = array())
	{
	  if(!$table_name)
	    $table_name = $this->get_table_name();
	    
	  $columns = $this->get_columns();
	  $implode_arr = array();
	  foreach($columns as $key => $descr)
	  {
	    if(!in_array($key, $exclude_columns))
	      $implode_arr[] = $table_name . '.' . $key . ' as ' . $key;
	  }
	  
	  return implode(', ', $implode_arr);
	}
}

?>