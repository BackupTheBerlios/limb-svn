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
require_once(LIMB_DIR . 'class/lib/db/db_module.class.php');

class db_null extends db_module
{	  
  function __construct()
  {
	  $this->_db_connection = -1;
	 	$this->_sql_result = null;  
  }
  
  public function connect_db($db_params)
  {
  }

  public function select_db($db_name)
  {  	
  }
    
  public function disconnect_db($db_params)
  {
  }

  public function free_result()
  {
  }
  
	protected function _sql_exec_operation($sql, $count=0, $start=0)
	{
	  return false;
	}
	      
  public function get_affected_rows()
  {
  	return 0;
  }

  public function get_sql_insert_id()
	{		
		return false;
	}

  public function get_last_error()
	{
		return '';
	}
  
  public function parse_batch_sql(&$ret, $sql, $release)
	{
	  return false;
	}

  protected function _fetch_assoc_result_row()
  {
  	return false;
  }
  
	protected function _result_num_fields()
	{
		return false;
	}
	
  protected function _process_default_value($value)
  {
  	return false;
  }
  
	public function escape($sql)
  {
  	return false;
  }
  
  public function concat($values)
  {
  	return false;
  }
  
  public function substr($string, $offset, $limit=null)
  {
    return false;  
  }
  
  public function count_selected_rows()
  {
    return false;
  }
  
  protected function _begin_operation()
  {
  	return false;
  }
  
  protected function _commit_operation()
  {
  }
  
  protected function _rollback_operation()
  {
  }
}
?>