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

require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_types.class.php');
require_once(LIMB_DIR . 'core/lib/db/builder/sql_builder.class.php');
require_once(LIMB_DIR . 'core/lib/db/builder/criteria.class.php');

class db_table
{
	var $_db_table_name = '';
	
	var $_primary_key_name = 'id';
	
	var $_columns = array();
	
	var $_constraints = array();

  var $_connection = null;
  
  var $_sql_builder = null;
  
  function db_table()
  {
  	$this->_db_table_name = $this->_define_db_table_name();
    $this->_columns = $this->_define_columns();
    $this->_constraints = $this->_define_constraints();
   
    $this->_connection =& $this->_get_connection();
    $this->_sql_builder =& $this->_get_sql_builder();
  }
  
  function get_table_name()
  {
		return $this->_db_table_name;
	}
  
  function & _get_connection()
  {
  	return db_factory :: get_connection();
  }

  function & _get_sql_builder()
  {
  	return sql_builder :: instance();
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
  
  function use_id_generator()
  {
  	return true;
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
  		
  	return $this->_columns[$column_name]['type'];
  }
  
  function get_primary_key_name()
  {
  	return $this->_primary_key_name;
  }
  
  function insert($row)
  {
  	if (!is_array($row))
  	{
  	  error('not array', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
  	}
		
		$filtered_row =& $this->_filter_row($row);
		
		$criteria = new criteria();
		foreach($filtered_row as $field => $value)
		{
			$criteria->add($this->_db_table_name . '.' . $field, $value);
		}
		
    return $this->_sql_builder->do_insert($criteria, $this->_connection);
  }
      
  function update($row, $select_criteria)
  {
  	if (!is_array($row))
  	{
  	  error('not array', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
  	}

  	$filtered_row =& $this->_filter_row($row);
  	
  	$update_values = new criteria();
		foreach($filtered_row as $field => $value)
		{
			$update_values->add($this->_db_table_name . '.' . $field, $value);
		}
  	
  	return $this->_sql_builder->do_update($select_criteria, $update_values, $this->_connection);
  } 
        
  function & select($criteria=null)
	{
  	if($criteria == null)
  	{
  		$criteria = new criteria();
  	}

		for(($it = &$criteria->get_iterator()); $it->valid(); $it->next())
		{
			$c = $it->current();
		}
		
		$criteria->clear_select_columns();
		
		foreach(array_keys($this->_columns) as $column_name)
			$criteria->add_select_column($column_name);
		
		return $this->_sql_builder->do_select($criteria, $this->_connection);
	}
	
  function delete($criteria=null)
  { 
  	if($criteria == null)
  	{
  		$criteria = new criteria();
  		$criteria->add_select_column($this->_db_table_name . '.*');
  	}
  	 	
		$affected_rs =& $this->_sql_builder->do_select($criteria, $this->_connection);
		
    $this->_delete_operation($criteria, $affected_rs);
    
    $this->_cascade_delete($affected_rs);
    
    return true;
	}
	
	function _delete_operation($criteria, &$affected_rs)
	{
		return $this->_sql_builder->do_delete($criteria, $this->_connection);
	}
	  	
	function _cascade_delete(&$affected_rs)
	{			
		if ($affected_rs->get_record_count() == 0)
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
				$affected_rs->first();
				while($affected_rs->next())
					$values[] = $affected_rs->get_int($id);
				
				$criteria = new criteria();
				$criteria->add($this->_db_table_name . '.' . $column_name, $values, criteria::IN());
				$db_table->delete($criteria);
			}
		}
	}	
}

?>