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
require_once(LIMB_DIR . 'core/lib/db/db_module.class.php');

class db_fake extends db_module
{	  
  function _connect_db_operation($db_params)
  {
  	return false;
  }
  
  function _select_db_operation($db_name)
  {
  	return false;
  }
  
  function _disconnect_db_operation($db_params)
  {
  }

  function free_result()
  {
  }
  
	function _sql_exec_operation($sql, $count=0, $start=0)
	{
	  return false;
	}
	      
  function get_affected_rows()
  {
  	return 0;
  }

  function get_sql_insert_id()
	{		
		return false;
	}

  function get_last_error()
	{
		return '';
	}
  
  function parse_batch_sql(&$ret, $sql, $release)
	{
	  return false;
	}

  function _fetch_assoc_result_row()
  {
  	return false;
  }
  
	function _result_num_fields()
	{
		return false;
	}
	
  function _process_default_value($value)
  {
  	return false;
  }
  
	function escape($sql)
  {
  	return false;
  }
  
  function concat($values)
  {
  	return false;
  }
  
  function substr($string, $offset, $limit=null)
  {
    return false;  
  }
  
  function count_selected_rows()
  {
    return false;
  }
  
  function _begin_operation()
  {
  	return false;
  }
  
  function _commit_operation()
  {
  }
  
  function _rollback_operation()
  {
  }
}
?>