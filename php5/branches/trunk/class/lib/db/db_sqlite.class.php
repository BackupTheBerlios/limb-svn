<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: db_sqlite.class.php 658 2004-09-15 14:21:14Z pachanga $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/lib/db/db_module.class.php');

class db_sqlite extends db_module
{	
  protected function _connect_db_operation($db_params)
  {
  	if (file_exists($db_params['name']))
  		return sqlite_open($db_params['name']);
  	else
  		return false;
  }
  
  protected function _select_db_operation($db_name)
  {
  	return true;
  }
  
  protected function _disconnect_db_operation($db_params)
  {
  	sqlite_close($this->_db_connection);
  }

  public function free_result()
  {
  	$this->_sql_result = null;
  }
  
	protected function _sql_exec_operation($sql, $count=0, $start=0)
	{		
		if ($count)
		{
			$sql .= "\nLIMIT $count";
		
			if ($start)
				$sql .= " OFFSET $start";
		}
		
		return sqlite_query($this->_db_connection, $sql);
	}
	  
  public function make_select_string($table, $fields='*', $where='', $order='', $count=0, $start=0)
  {  		
  	$sql = parent :: make_select_string($table, $fields, $where, $order, $count, $start);
  	
		if ($count)
		{
			$sql .= "\nLIMIT $count";
		
			if ($start)
				$sql .= " OFFSET $start";
		}
		
    return $sql;
  }
    
  public function get_affected_rows()
  {
  	return sqlite_changes($this->_db_connection);
  }

  public function get_sql_insert_id()
	{		
		return sqlite_last_insert_rowid($this->_db_connection);
	}

  public function get_last_error()
	{
		return sqlite_last_error($this->_db_connection);
	}
  
  public function parse_batch_sql(&$ret, $sql, $release)
	{
		$ret[] = $sql;
		return $ret;
	}

  protected function _fetch_assoc_result_row($col_num = '')
  {
  	return sqlite_fetch_array($this->_sql_result, SQLITE_ASSOC);
  }
  
	protected function _result_num_fields()
	{
		return sqlite_num_fields($this->_sql_result);
	}
	
  protected function _process_default_value($value)
  {
  	return "'{$value}'";
  }
  
	public function escape($sql)
  {
  	return sqlite_escape_string($sql);
  }
  
  public function concat($values)
  {
  	return ' ' . implode(' || ' , $values) . ' ';
  }
  
  public function substr($string, $offset, $limit=null)
  {
  	if ($limit === null)
  		$limit = "length($string) - $offset + 1";

		return " substr({$string}, {$offset}, {$limit}) ";
  }
  
  public function count_selected_rows()
  {
    return sqlite_num_rows($this->_sql_result);
  }
  
  protected function _begin_operation()
  {
  	sqlite_query('BEGIN TRANSACTION', $this->_db_connection);
  }
  
  protected function _commit_operation()
  {
  	sqlite_query('COMMIT', $this->_db_connection);
  }
  
  protected function _rollback_operation()
  {
  	sqlite_query('ROLLBACK', $this->_db_connection);
  }
}
?>