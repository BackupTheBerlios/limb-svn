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
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/lib/error/error.inc.php');

class db_table
{
	var $_db_table_name = '';
	
	var $_primary_key_name = 'id';
	
	var $_columns = array();
	
	var $_constraints = array();

  var $_db = null;

  function db_table()
  {
  	$this->_db_table_name = $this->_define_db_table_name();
    $this->_columns = $this->_define_columns();
    $this->_constraints = $this->_define_constraints();
   
    $this->_db =& db_factory :: instance();
  }
      
  function _define_db_table_name()
  {
  	$class_name = get_class($this);
  	
  	if(($pos = strpos($class_name, '_db_table')) !== false)
  		$class_name = substr($class_name, 0, $pos);
  	
  	return $class_name;
  }
  
  function _define_columns()
  {
  	return array();
  }
  
  function _define_constraints()
  {
  	return array();
  }
    
  function has_column($name)
  {
  	return isset($this->_columns[$name]);
  }
  
  function get_columns()
  {
  	return $this->_columns;
  }
  
  function get_constraints()
  {
  	return $this->_constraints;
  }
  
  function get_column_types()
  {
  	$types = array();
  	foreach(array_keys($this->_columns) as $column_name)
  		$types[$column_name] = $this->get_column_type($column_name);
  		
  	return $types;
  }
  
  function get_column_type($column_name)
  {
  	if(!$this->has_column($column_name))
  		return false;
  		
  	return (is_array($this->_columns[$column_name]) && isset($this->_columns[$column_name]['type'])) ? 
  		$this->_columns[$column_name]['type'] : 
  		'';
  }
  
  function get_primary_key_name()
  {
  	return $this->_primary_key_name;
  }
  
  function insert($row)
  {
  	if (!is_array($row))
  	{
  	  error('not array',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('row' => $row)
    	);
  	}
		
		$filtered_row =& $this->_filter_row($row);
		
    return $this->_db->sql_insert($this->_db_table_name, $filtered_row, $this->get_column_types());
  }
  
  function & _filter_row($row)
  {
  	$filtered = array();
  	foreach($row as $key => $value)
  	{
  		if($this->has_column($key))
  			$filtered[$key] = $value;		
  	}
  	return $filtered;
  }
    
  function update($row, $conditions)
  { 
  	if (!is_array($row))
  	{
  	  error('not array',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('row' => $row)
    	);
  	}

  	$filtered_row =& $this->_filter_row($row);
  	  
    return $this->_db->sql_update($this->_db_table_name, $filtered_row, $conditions, $this->get_column_types());
  } 

  function update_by_id($id, $data)
  {
		if (!$this->_primary_key_name)
		{
    	error('primary id column not set',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		}

  	return $this->update($data, "{$this->_primary_key_name}='{$id}'");
  } 
  
  function get_row_by_id($id)
  {
		if (!$this->_primary_key_name)
		{
    	error('primary id column not set',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		}

		$data = $this->get_list($this->_primary_key_name . "='{$id}'");
		
		return current($data);
  }
  
  function get_num_rows()
  {
  }
      
  function & get_list($conditions='', $order='', $group_by_column='', $start=0, $count=0)
	{			
    $this->_db->sql_select($this->_db_table_name, '*', $conditions, $order, $start, $count);
   
		if ($group_by_column === '')
			$group_by_column = $this->_primary_key_name;
 		 				
		if($group_by_column)
    	$result =& $this->_db->get_array($group_by_column);
    else
    	$result =& $this->_db->get_array();
    	
    return $result;
	}
	
  function delete($conditions='')
  {  	
		$affected_rows = $this->_prepare_affected_rows($conditions);
		
    $this->_delete_operation($conditions, $affected_rows);
    
    $this->_cascade_delete($affected_rows);
    
    return true;
	}
	
	function _delete_operation($conditions, $affected_rows)
	{
		$this->_db->sql_delete($this->_db_table_name, $conditions);
	}
	
	function delete_by_id($id)
	{
		if (!$this->_primary_key_name)
		{
    	error('primary id column not set',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		}

		return $this->delete(array($this->_primary_key_name => $id));
	}
  	  	
  function get_last_insert_id()
  {		
		return $this->_db->get_sql_insert_id($this->_db_table_name, $this->_primary_key_name);
	}

  function get_max_id()
  {			
		return $this->_db->get_max_column_value($this->_db_table_name, $this->_primary_key_name);
	}

  function get_table_name()
  {
		return $this->_db_table_name;
	}
  	
	function _cascade_delete($affected_rows)
	{
		if($this->auto_constraints_enabled())
			return;
			
		if (!count($affected_rows))
			return;

		foreach($this->_constraints as $id => $constraints_array)
		{
			foreach($constraints_array as $key => $constraint_params)
			{
				$table_name = $constraint_params['table_name'];
				$column_name = $constraint_params['field'];
				
				$db_table =& db_table_factory :: instance($table_name);
				
				if(!$db_table->has_column($column_name))
				{
		    	error('no such a column',
		    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
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

	function & _prepare_affected_rows($conditions)
	{
		$affected_rows = array();
		
		if($this->auto_constraints_enabled())
			return $affected_rows;
			
		$affected_rows =& $this->get_list($conditions);
		
		return $affected_rows;
	}
		
	function auto_constraints_enabled()
	{
		return (defined('DB_AUTO_CONSTRAINTS') && DB_AUTO_CONSTRAINTS == true);
	}
}

?>