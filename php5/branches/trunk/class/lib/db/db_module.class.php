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
require_once(LIMB_DIR . 'class/lib/error/debug.class.php');
require_once(LIMB_DIR . 'class/lib/date/date.class.php');
require_once(LIMB_DIR . 'class/i18n/locale.class.php');

abstract class db_module
{
  const DB_TRANSACTION_STATUS_IN = 1;
  const DB_TRANSACTION_STATUS_OUT = 0;
  
	protected $_transaction_status;
	
  protected $_db_connection;
  protected $_sql_result;
  
  protected $_locale_id = '';
  
  protected $_executed_sql = array();
  
  function __construct($db_params)
  {
		$this->_transaction_status = self :: DB_TRANSACTION_STATUS_OUT;
		
	  $this->_db_connection = -1;
	 	$this->_sql_result = null;
  	
  	$this->connect_db($db_params);
  	
  	$this->select_db($db_params['name']);
  }
  
  public function set_locale_id($locale_id)
  {
  	$this->_locale_id = $locale_id;
  }

  public function is_debug_enabled()
	{
		return (defined('DEBUG_DB_ENABLED') && constant('DEBUG_DB_ENABLED'));
	}

  public function connect_db($db_params)
  {
  	if(!$this->_db_connection = $this->_connect_db_operation($db_params))
  		throw new SQLException('couldnt connect to database at host, check db params', 
    		          $this->get_last_error(), 
    		          array(
    		            'host' => $db_params['host'],
    		            'database' => $db_params['name'],
    		            'login' => $db_params['login']
    		          )
  		          );
  }

  public function select_db($db_name)
  {  	
  	if(!$this->_select_db_operation($db_name))
  		throw new SQLException('couldnt select database, check db params', 
    		          $this->get_last_error(), 
    		          array('database' => $db_name)
    		        );  	
  }
  
  abstract protected function _connect_db_operation($db_params);

  abstract protected function _disconnect_db_operation($db_params);
  
  abstract protected function _select_db_operation($db_name);
  
  public function disconnect_db()
  {
  	$this->_disconnect_db_operation();
  	
  	$this->_db_connection = -1;
  }

  public function free_result()
  {
  	$this->_sql_result = null;
  }
  
  abstract public function get_affected_rows();

  public function sql_exec($sql, $limit=0, $offset=0)
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
  		throw new SQLException('query error', 
    		          $this->get_last_error(), 
    		          array('sql' => $sql)
    		        );  	  	
  	}

    return $this->_sql_result;
  }
  
  abstract protected function _sql_exec_operation($sql);
  
  public function sql_exec_batch($sql='')
  {
  	$sqls = array();
		$this->parse_batch_sql($sqls, $sql, 32344);
		foreach($sqls as $sql)
		{
			$res = $this->sql_exec($sql);
		}
		return true;
  }
  
  public function assign_array(&$result_array, $array_index='')
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
    
  public function get_array($array_index='')
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
  
  public function escape($sql)
  {
  	return $sql;
  }
  
  abstract public function concat($values);
  
  abstract public function substr($string, $offset, $limit=null);
  
  public function null()
  {
  	return 'NULL';
  }
  
	//$count $start not supported by default!
  public function sql_select($table, $fields='*', $where='', $order='', $count=0, $start=0)
  {
    return $this->sql_exec($this->make_select_string($table, $fields, $where, $order, $count, $start));
  }

  public function sql_insert($table, $row, $column_types=array())
  {  	
    return $this->sql_exec($this->make_insert_string($table, $row, $column_types));
  }

  public function sql_update($table, $set, $where='', $column_types=array())
  {  	
  	return $this->sql_exec($this->make_update_string($table, $set, $where, $column_types));
  }

  public function sql_delete($table, $where='')
  {  	
    return $this->sql_exec($this->make_delete_string($table, $where));
  }
   
  abstract public function parse_batch_sql(&$ret, $sql, $release);
	
	abstract protected function _result_num_fields();
	
	abstract protected function _fetch_assoc_result_row($col_num = '');
	
	public function process_values($names_values, $column_types=array())
	{
		foreach($names_values as $key => $value)
		{
			$type = isset($column_types[$key]) ? $column_types[$key] : '';
			$names_values[$key] = $this->_process_value($value, $type);
		}
  	
  	return $names_values;
	}
	
	protected function _process_value($value, $type='')
  {   	
	  //quick'n'dirty fix for autoincrements
  	if(is_null($value))
  		return $this->null();
  
  	$type = ($type) ? $type : gettype($value);
  	
    switch(strtolower($type)) 
    {
    	case 'numeric':
    		return $value*1;
    	case 'int':
    		return intval($value);
    	break;
    	case 'float':
    		return str_replace(',', '.', "'" . floatval($value) . "'"); // FIXX!!
    	break;
    	case 'clob':
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
  
  protected function _process_string_value($value)
  {
  	return "'" . $this->escape($value) . "'";
  }

  protected function _process_bool_value($value)
  {
  	return ($value) ? 1 : 0;
  }

  protected function _process_date_value($value)
  {
  	$locale = locale :: instance($this->_locale_id);
  	$date = new date($value, DATE_SHORT_FORMAT_ISO);
  	
  	if(!$date->is_valid())
  	{
  		$date->set_by_string($value, $locale->get_short_date_format());
  		$value = $date->format(DATE_SHORT_FORMAT_ISO);
  	}
  	
  	return "'" . $value . "'";
  }

  protected function _process_datetime_value($value)
  {
  	$locale = locale :: instance($this->_locale_id);
  	$date = new date($value, DATE_FORMAT_ISO);
  	
  	if(!$date->is_valid())
  	{
  		$date->set_by_string($value, $locale->get_short_date_time_format());
  		$value = $date->format(DATE_FORMAT_ISO);
  	}
  	
  	return "'" . $value . "'";
  }  
  
  protected function _process_default_value($value)
  {
  	return strval($value);
  }
  	
	abstract public function get_last_error();
	
	abstract public function get_sql_insert_id();
	
  public function get_max_column_value($table_name, $column_name)
  {	
  	$sql = 'SELECT MAX('. $column_name .') as m FROM '. $table_name;
		
		$this->sql_exec($sql);
		$arr = $this->fetch_row();
		
		return isset($arr['m']) ? $arr['m'] : 0;
	}
	
	public function fetch_row()
	{
	  return $this->_fetch_assoc_result_row();
	}
	
	abstract public function count_selected_rows();
	
  public function make_insert_string($table, $names_values, $column_types=array())
  {
  	if(is_array($names_values))
  		$names_values = $this->process_values($names_values, $column_types);
    	
		$keys = array_keys($names_values);
		$values = array_values($names_values);
		
		$str_names = '(' . implode(',', $keys) . ')';
    $str_values = '(' . implode(',', $values) . ')';
		
    return "INSERT INTO $table $str_names VALUES $str_values";
  }

  public function make_select_string($table, $fields='*', $where='', $order='', $count=0, $start=0)
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

  public function make_update_string($table, $names_values, $where='', $column_types=array())
  { 
  	if(is_array($names_values))
  		$names_values = $this->process_values($names_values, $column_types);
   	    	   
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

  public function make_delete_string($table, $where='')
  {
    if(is_array($where))
    	$where = ' WHERE (' . $this->sql_and($where) . ')';
    elseif($where)
      $where = ' WHERE (' . $where . ')';

    return "DELETE FROM $table $where";
  }
  
  public function sql_in($column_name, $values, $type='')
  {
		$implode_values = array();
		foreach($values as $value)
			$implode_values[] = $this->_process_value($value, $type);
		
		$in_ids = implode(' , ', $implode_values);
		
		return $column_name . ' IN (' . $in_ids . ')';
  }
  
  public function sql_and($conditions, $column_types=array())
  {
		$implode_values = array();
		
		foreach($conditions as $key => $value)
		{
			$value = $this->_process_value($value, isset($column_types[$key]) ? $column_types[$key] : '');
			
			$implode_values[] = "($key=$value)";			
		}
		return implode(' AND ', $implode_values);
  }
  
  public function begin()
  {
  	if($this->_transaction_status == self :: DB_TRANSACTION_STATUS_OUT)
  	{
  		$this->_begin_operation();
  		$this->_transaction_status = self :: DB_TRANSACTION_STATUS_IN;
  	}
  }
  
  abstract protected function _begin_operation();
  
  public function commit()
  {
  	if($this->_transaction_status == self :: DB_TRANSACTION_STATUS_IN)
  	{
  		$this->_commit_operation();
  		$this->_transaction_status = self :: DB_TRANSACTION_STATUS_OUT;
  	}
  }

  abstract protected function _commit_operation();
  
  public function rollback()
  {
  	if($this->_transaction_status == self :: DB_TRANSACTION_STATUS_IN)
  	{
  		$this->_rollback_operation();
  		$this->_transaction_status = self :: DB_TRANSACTION_STATUS_OUT;
  	}
  }
  
  abstract protected function _rollback_operation();
}

function sql_and($conditions, $column_types=array())
{	
	return db_factory :: instance()->sql_and($conditions, $column_types);
}

function sql_in($column_name, $values, $type='')
{	
	return db_factory :: instance()->sql_in($column_name, $values, $type);
}

?>