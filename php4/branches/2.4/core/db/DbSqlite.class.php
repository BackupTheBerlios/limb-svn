<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: db_sqlite.class.php 658 2004-09-15 14:21:14Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/db/DbModule.class.php');

class DbSqlite extends DbModule
{
  function _connectDbOperation($db_params)
  {
    if (file_exists($db_params['name']))
      return sqlite_open($db_params['name']);
    else
      return false;
  }

  function _selectDbOperation($db_name)
  {
    return true;
  }

  function _disconnectDbOperation($db_params)
  {
    sqlite_close($this->_db_connection);
  }

  function freeResult()
  {
    $this->_sql_result = null;
  }

  function _sqlExecOperation($sql, $count=0, $start=0)
  {
    if ($count)
    {
      $sql .= "\nLIMIT $count";

      if ($start)
        $sql .= " OFFSET $start";
    }

    return sqlite_query($this->_db_connection, $sql);
  }

  function makeSelectString($table, $fields='*', $where='', $order='', $count=0, $start=0)
  {
    $sql = parent :: makeSelectString($table, $fields, $where, $order, $count, $start);

    if ($count)
    {
      $sql .= "\nLIMIT $count";

      if ($start)
        $sql .= " OFFSET $start";
    }

    return $sql;
  }

  function getAffectedRows()
  {
    return sqlite_changes($this->_db_connection);
  }

  function getSqlInsertId()
  {
    return sqlite_last_insert_rowid($this->_db_connection);
  }

  function getLastError()
  {
    return sqlite_last_error($this->_db_connection);
  }

  function parseBatchSql(&$ret, $sql, $release)
  {
    $ret[] = $sql;
    return $ret;
  }

  function _fetchAssocResultRow($col_num = '')
  {
    return sqlite_fetch_array($this->_sql_result, SQLITE_ASSOC);
  }

  function _resultNumFields()
  {
    return sqlite_num_fields($this->_sql_result);
  }

  function _processDefaultValue($value)
  {
    return "'{$value}'";
  }

  function escape($sql)
  {
    return sqlite_escape_string($sql);
  }

  function concat($values)
  {
    return ' ' . implode(' || ' , $values) . ' ';
  }

  function substr($string, $offset, $limit=null)
  {
    if ($limit === null)
      $limit = "length($string) - $offset + 1";

    return " substr({$string}, {$offset}, {$limit}) ";
  }

  function countSelectedRows()
  {
    return sqlite_num_rows($this->_sql_result);
  }

  function _beginOperation()
  {
    sqlite_query('BEGIN TRANSACTION', $this->_db_connection);
  }

  function _commitOperation()
  {
    sqlite_query('COMMIT', $this->_db_connection);
  }

  function _rollbackOperation()
  {
    sqlite_query('ROLLBACK', $this->_db_connection);
  }
}
?>