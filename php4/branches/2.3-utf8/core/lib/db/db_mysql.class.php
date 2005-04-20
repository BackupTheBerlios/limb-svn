<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(LIMB_DIR . '/core/lib/db/db_module.class.php');

class db_mysql extends db_module
{
  function _connect_db_operation($db_params)
  {
    return mysql_connect($db_params['host'], $db_params['login'], $db_params['password']);
  }

  function _select_db_operation($db_name)
  {
    return mysql_select_db($db_name, $this->_db_connection);
  }

  function _disconnect_db_operation($db_params)
  {
    mysql_close($this->_db_connection);
  }

  function free_result()
  {
    if($this->_sql_result)
    {
      mysql_free_result($this->_sql_result);
      $this->_sql_result = null;
    }
  }

  function _sql_exec_operation($sql, $count=0, $start=0)
  {
    if ($count)
      $sql .= "\nLIMIT $start, $count";

    return mysql_query($sql, $this->_db_connection);
  }

  function make_select_string($table, $fields='*', $where='', $order='', $count=0, $start=0)
  {
    $sql = parent :: make_select_string($table, $fields, $where, $order, $count, $start);

    if ($count)
      $sql .= "\nLIMIT $start, $count";

    return $sql;
  }

  function get_affected_rows()
  {
    return mysql_affected_rows($this->_db_connection);
  }

  function get_sql_insert_id()
  {
    return mysql_insert_id($this->_db_connection);
  }

  function get_last_error()
  {
    return mysql_error();
  }

  function _fetch_assoc_result_row()
  {
    return mysql_fetch_assoc($this->_sql_result);
  }

  function _result_num_fields()
  {
    return mysql_num_fields($this->_sql_result);
  }

  function _process_default_value($value)
  {
    return "'{$value}'";
  }

  function escape($sql)
  {
    return mysql_escape_string($sql);
  }

  function concat($values)
  {
    $str = implode(',' , $values);
    return " CONCAT({$str}) ";
  }

  function substr($string, $offset, $limit=null)
  {
    if ($limit === null)
      return " SUBSTRING({$string} FROM {$offset}) ";
    else
      return " SUBSTRING({$string} FROM {$offset} FOR {$limit}) ";
  }

  function count_selected_rows()
  {
    return mysql_num_rows($this->_sql_result);
  }

  function _begin_operation()
  {
    mysql_query('START TRANSACTION', $this->_db_connection);
  }

  function _commit_operation()
  {
    mysql_query('COMMIT', $this->_db_connection);
  }

  function _rollback_operation()
  {
    mysql_query('ROLLBACK', $this->_db_connection);
  }
}
?>