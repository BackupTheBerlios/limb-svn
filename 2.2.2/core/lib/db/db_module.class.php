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
require_once(LIMB_DIR . 'core/lib/error/error.inc.php');
require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');
require_once(LIMB_DIR . 'core/lib/date/date.class.php');
require_once(LIMB_DIR . 'core/lib/i18n/locale.class.php');

define('DB_TRANSACTION_STATUS_IN', 1);
define('DB_TRANSACTION_STATUS_OUT', 0);

class db_module
{
	var $_transaction_status;
	
  var $_db_connection;
  var $_sql_result;
  
  var $_locale_id = '';
  
  var $_executed_sql = array();
  
  function db_module($db_params)
  {
		$this->_transaction_status = DB_TRANSACTION_STATUS_OUT;
		
	  $this->_db_connection = -1;
	 	$this->_sql_result = null;
  	
  	$this->connect_db($db_params);
  	
  	$this->select_db($db_params['name']);
  }
  
  function set_locale_id($locale_id)
  {
  	$this->_locale_id = $locale_id;
  }

  function is_debug_enabled()
	{
		return (defined('DEBUG_DB_ENABLED') && constant('DEBUG_DB_ENABLED'));
	}

  function connect_db($db_params)
  {
  	if(!$this->_db_connection = $this->_connect_db_operation($db_params))
  		error("couldnt connect to db at host {$db_params['host']}, check db params", 
  			__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);  	
  }

  function select_db($db_name)
  {  	
  	if(!$this->_select_db_operation($db_name))
  		error("couldnt select db '{$db_name}', check db params", 
  			__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
  }
  
  function _connect_db_operation($db_params)
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);
  }
  
  function _select_db_operation($db_name)
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
  }

  function disconnect_db()
  {
  	$this->_disconnect_db_operation();
  	
  	$this->_db_connection = -1;
  }

  function free_result()
  {
  	$this->_sql_result = null;
  }
  
  function get_affected_rows()
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
  }

  function sql_exec($sql, $limit=0, $offset=0)
  { 
  	$this->_sql_result = null;
  	  	 	
  	if($this->is_debug_enabled())
  	{
  	  $md5 = md5($sql);
  	  
  	  if(isset($this->_executed_sql[$md5]))
  	  {
  	    $this->_executed_sql[$md5]['times']++;
  	    
  	    debug :: write_debug('same SQL query at: ' .   	    
  	      '<a href=#' . $this->_executed_sql[$md5]['pos'] . '><b>' . $this->_executed_sql[$md5]['pos'] . '</b></a>' .    	     
  	      ' already executed for ' . $this->_executed_sql[$md5]['times'] . ' times'
  	    );
  	  }
  	  else
  	  {
  	    debug :: write_debug($sql);
  	    $this->_executed_sql[$md5] = array('pos' => debug :: sizeof(), 'times' => 0);
  	  }
  		
  		debug :: accumulator_start('db', 'sql_exec');
  	}
  	
  	$this->_sql_result = $this->_sql_exec_operation($sql, $limit, $offset);
  	
  	if($this->is_debug_enabled())
  		debug :: accumulator_stop('db', 'sql_exec');
  	
  	if (!$this->_sql_result)
  	{
  		error(
  			$this->_sql_exec_error($sql), 
  			__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
  	}

    return $this->_sql_result;
  }
  
  function _sql_exec_operation($sql)
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);
  }
  
  function _sql_exec_error($sql)
  {
  	 return $this->get_last_error() . "\n" . $sql;
  }

  function sql_exec_batch($sql='')
  {
  	$sqls = array();
		$this->parse_batch_sql($sqls, $sql, 32344);
		foreach($sqls as $sql)
		{
			$res = $this->sql_exec($sql);
		}
		return true;
  }
  
  function assign_array(&$result_array, $array_index='')
  {
		if(!$this->_sql_result)
			return;

    $arr = array();
		
		$col_num = $this->_result_num_fields();
		
		if($array_index)
    	while($arr = $this->_fetch_assoc_result_row($col_num))
      	$result_array[$arr[$array_index]] = $arr;
    else
    	while($arr = $this->_fetch_assoc_result_row($col_num))
      	$result_array[] = $arr;
      	
    $this->free_result();
  }
    
  function & get_array($array_index='')
  {
    $result_array = array();
    
		if(!$this->_sql_result)
			return $result_array;

    $arr = array();
		
		$col_num = $this->_result_num_fields();
		
		if($array_index)
    	while($arr = $this->_fetch_assoc_result_row($col_num))
      	$result_array[$arr[$array_index]] = $arr;
    else
    	while($arr = $this->_fetch_assoc_result_row($col_num))
      	$result_array[] = $arr;
      	
    $this->free_result();
    
    return $result_array;
  }
  
  function escape($sql)
  {
  	return $sql;
  }
  
  function concat($value1, $value2)
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);
  }
  
  function substr($string, $offset, $limit=null)
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);
  }
  
  function null()
  {
  	return "''";
  }
  
	//$count $start not supported by default!
  function sql_select($table, $fields='*', $where='', $order='', $count=0, $start=0)
  {
    return $this->sql_exec($this->make_select_string($table, $fields, $where, $order, $count, $start));
  }

  function sql_insert($table, $row, $column_types=array())
  {  	
    return $this->sql_exec($this->make_insert_string($table, $row, $column_types));
  }

  function sql_update($table, $set, $where='', $column_types=array())
  {  	
  	return $this->sql_exec($this->make_update_string($table, $set, $where, $column_types));
  }

  function sql_delete($table, $where='')
  {  	
    return $this->sql_exec($this->make_delete_string($table, $where));
  }
   
  function parse_batch_sql(&$ret, $sql, $release)
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
	}
	
	function _result_num_fields()
	{
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
	}
	
	function _fetch_assoc_result_row($col_num)
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
  }
	
	function _process_values($names_values, $column_types=array())
	{
		foreach($names_values as $key => $value)
		{
			$type = isset($column_types[$key]) ? $column_types[$key] : '';
			$names_values[$key] = $this->_process_value($value, $type);
		}
  	
  	return $names_values;
	}
	
	function _process_value($value, $type='')
  { 
  	$type = ($type) ? $type : gettype($value);
  	
    switch(strtolower($type)) 
    {
    	case 'numeric':
    		return $value*1;
    	break;
    	case 'float':
    		return str_replace(',', '.', "'" . floatval($value) . "'"); // FIXX!!
    	break;
	    case 'string':
	    	return $this->_process_string_value($value);
	    break;
	    case 'boolean':
	    	return $this->_process_bool_value($value);
	    break;
	    case 'null':
	    	return $this->null();
	    break;
	    case 'date':
	    	return $this->_process_date_value($value);
	    break;
	    case 'datetime':
	    	return $this->_process_datetime_value($value);
	    break;
	    case 'default';
	    default:
	    	return $this->_process_default_value($value);
    }
  }
  
  function _process_string_value($value)
  {
  	return "'" . $this->escape($value) . "'";
  }

  function _process_bool_value($value)
  {
  	return ($value) ? 1 : 0;
  }

  function _process_date_value($value)
  {
  	$locale =& locale :: instance($this->_locale_id);
  	$date =& new date($value, DATE_SHORT_FORMAT_ISO);
  	
  	if(!$date->is_valid())
  	{
  		$date->set_by_string($value, $locale->get_short_date_format());
  		$value = $date->format(DATE_SHORT_FORMAT_ISO);
  	}
  	
  	return "'" . $value . "'";
  }

  function _process_datetime_value($value)
  {
  	$locale =& locale :: instance($this->_locale_id);
  	$date =& new date($value, DATE_FORMAT_ISO);
  	
  	if(!$date->is_valid())
  	{
  		$date->set_by_string($value, $locale->get_short_date_time_format());
  		$value = $date->format(DATE_FORMAT_ISO);
  	}
  	
  	return "'" . $value . "'";
  }  
  
  function _process_default_value($value)
  {
  	return strval($value);
  }
  	
	function get_last_error()
	{
		return 'undefined error';
	}
	
	function get_sql_insert_id()
	{
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
	}
	
  function get_max_column_value($table_name, $column_name)
  {	
  	$sql = 'SELECT MAX('. $column_name .') as m FROM '. $table_name;
		
		$this->sql_exec($sql);
		$arr = $this->fetch_row();
		
		return isset($arr['m']) ? $arr['m'] : 0;
	}
	
	function fetch_row()
	{
	  return $this->_fetch_assoc_result_row();
	}
	
	function count_selected_rows()
	{
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
	}
	
  function make_insert_string($table, $names_values, $column_types=array())
  {
  	if(is_array($names_values))
  		$names_values = $this->_process_values($names_values, $column_types);
    	
		$keys = array_keys($names_values);
		$values = array_values($names_values);
		
		$str_names = '(' . implode(',', $keys) . ')';
    $str_values = '(' . implode(',', $values) . ')';
		
    return "INSERT INTO $table $str_names VALUES $str_values";
  }

  function make_select_string($table, $fields='*', $where='', $order='', $count=0, $start=0)
  {
    if(is_array($where))
    	$where = ' WHERE (' . $this->sql_and($where) . ')';
    elseif ($where != '')
      $where=' WHERE (' . $where . ')';

    if($order != '') 
    	$order = ' ORDER BY ' . $order;

    $fields_str = '';
    if(is_array($fields))
    	$fields_str = implode(',', $fields);    	
    else
      $fields_str = $fields;

    return "SELECT $fields_str FROM $table $where $order";
  }

  function make_update_string($table, $names_values, $where='', $column_types=array())
  { 
  	if(is_array($names_values))
  		$names_values = $this->_process_values($names_values, $column_types);
   	    	   
		if(is_array($where))
			$where = ' WHERE (' . $this->sql_and($where) . ')';
    elseif($where)
      $where = ' WHERE (' . $where . ')';

    $fields_str = '';
    if(is_array($names_values))
    {
    	$implode_values = array();
    	
    	foreach($names_values as $key => $val)
    		$implode_values[] = $key . '=' . $val;
    		
      	$fields_str = implode(',', $implode_values);
    }
    else
      $fields_str = $names_values;

    return "UPDATE $table SET $fields_str $where";
  }

  function make_delete_string($table, $where='')
  {
    if(is_array($where))
    	$where = ' WHERE (' . $this->sql_and($where) . ')';
    elseif($where)
      $where = ' WHERE (' . $where . ')';

    return "DELETE FROM $table $where";
  }
  
  function sql_in($column_name, $values, $type='')
  {
		$implode_values = array();
		foreach($values as $value)
			$implode_values[] = $this->_process_value($value, $type);
		
		$in_ids = implode(' , ', $implode_values);
		
		return $column_name . ' IN (' . $in_ids . ')';
  }
  
  function sql_and($conditions, $column_types=array())
  {
		$implode_values = array();
		
		foreach($conditions as $key => $value)
		{
			$value = $this->_process_value($value, isset($column_types[$key]) ? $column_types[$key] : '');
			
			$implode_values[] = "($key=$value)";			
		}
		return implode(' AND ', $implode_values);
  }
  
  function begin()
  {
  	if($this->_transaction_status == DB_TRANSACTION_STATUS_OUT)
  	{
  		$this->_begin_operation();
  		$this->_transaction_status = DB_TRANSACTION_STATUS_IN;
  	}
  }
  
  function _begin_operation()
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
  }
  
  function commit()
  {
  	if($this->_transaction_status == DB_TRANSACTION_STATUS_IN)
  	{
  		$this->_commit_operation();
  		$this->_transaction_status = DB_TRANSACTION_STATUS_OUT;
  	}
  }

  function _commit_operation()
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
  }
  
  function rollback()
  {
  	if($this->_transaction_status == DB_TRANSACTION_STATUS_IN)
  	{
  		$this->_rollback_operation();
  		$this->_transaction_status = DB_TRANSACTION_STATUS_OUT;
  	}
  }
  
  function _rollback_operation()
  {
  	error('abstract method',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
  	);  
  }

}

function sql_and($conditions, $column_types=array())
{	
	$db =& db_factory :: instance();
	
	return $db->sql_and($conditions, $column_types);
}

function sql_in($column_name, $values, $type='')
{	
	$db =& db_factory :: instance();
	
	return $db->sql_in($column_name, $values, $type);
}

?>