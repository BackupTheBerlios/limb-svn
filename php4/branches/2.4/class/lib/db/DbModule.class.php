<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/error/Debug.class.php');
require_once(LIMB_DIR . '/class/lib/date/Date.class.php');
require_once(LIMB_DIR . '/class/i18n/Locale.class.php');

abstract class DbModule
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

    $this->connectDb($db_params);

    $this->selectDb($db_params['name']);
  }

  public function setLocaleId($locale_id)
  {
    $this->_locale_id = $locale_id;
  }

  public function isDebugEnabled()
  {
    return (defined('DEBUG_DB_ENABLED') &&  constant('DEBUG_DB_ENABLED'));
  }

  public function connectDb($db_params)
  {
    if(!$this->_db_connection = $this->_connectDbOperation($db_params))
      throw new SQLException('couldnt connect to database at host, check db params',
                  $this->getLastError(),
                  array(
                    'host' => $db_params['host'],
                    'database' => $db_params['name'],
                    'login' => $db_params['login']
                  )
                );
  }

  public function selectDb($db_name)
  {
    if(!$this->_selectDbOperation($db_name))
      throw new SQLException('couldnt select database, check db params',
                  $this->getLastError(),
                  array('database' => $db_name)
                );
  }

  abstract protected function _connectDbOperation($db_params);

  abstract protected function _disconnectDbOperation($db_params);

  abstract protected function _selectDbOperation($db_name);

  public function disconnectDb()
  {
    $this->_disconnectDbOperation();

    $this->_db_connection = -1;
  }

  public function freeResult()
  {
    $this->_sql_result = null;
  }

  abstract public function getAffectedRows();

  public function sqlExec($sql, $limit=0, $offset=0)
  {
    $this->_sql_result = null;

    if($this->isDebugEnabled())
    {
      $md5 = md5($sql);

      if(isset($this->_executed_sql[$md5]))
      {
        $this->_executed_sql[$md5]['times']++;

        Debug :: writeDebug('same SQL query at: ' .
          '<a href=#' . $this->_executed_sql[$md5]['pos'] . '><b>' . $this->_executed_sql[$md5]['pos'] . '</b></a>' .
          ' already executed for ' . $this->_executed_sql[$md5]['times'] . ' times'
        );
      }
      else
      {
        Debug :: writeDebug($sql);
        $this->_executed_sql[$md5] = array('pos' => Debug :: sizeof(), 'times' => 0);
      }

      Debug :: accumulatorStart('db', 'sql_exec');
    }

    $this->_sql_result = $this->_sqlExecOperation($sql, $limit, $offset);

    if($this->isDebugEnabled())
      Debug :: accumulatorStop('db', 'sql_exec');

    if (!$this->_sql_result)
    {
      throw new SQLException('query error',
                  $this->getLastError(),
                  array('sql' => $sql)
                );
    }

    return $this->_sql_result;
  }

  abstract protected function _sqlExecOperation($sql);

  public function sqlExecBatch($sql='')
  {
    $sqls = array();
    $this->parseBatchSql($sqls, $sql, 32344);
    foreach($sqls as $sql)
    {
      $res = $this->sqlExec($sql);
    }
    return true;
  }

  public function assignArray(&$result_array, $array_index='')
  {
    if(!$this->_sql_result)
      return;

    $arr = array();

    $col_num = $this->_resultNumFields();

    if($array_index)
      while($arr = $this->_fetchAssocResultRow($col_num))
        $result_array[$arr[$array_index]] = $arr;
    else
      while($arr = $this->_fetchAssocResultRow($col_num))
        $result_array[] = $arr;

    $this->freeResult();
  }

  public function getArray($array_index='')
  {
    $result_array = array();

    if(!$this->_sql_result)
      return $result_array;

    $arr = array();

    $col_num = $this->_resultNumFields();

    if($array_index)
      while($arr = $this->_fetchAssocResultRow($col_num))
        $result_array[$arr[$array_index]] = $arr;
    else
      while($arr = $this->_fetchAssocResultRow($col_num))
        $result_array[] = $arr;

    $this->freeResult();

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
  public function sqlSelect($table, $fields='*', $where='', $order='', $count=0, $start=0)
  {
    return $this->sqlExec($this->makeSelectString($table, $fields, $where, $order, $count, $start));
  }

  public function sqlInsert($table, $row, $column_types=array())
  {
    return $this->sqlExec($this->makeInsertString($table, $row, $column_types));
  }

  public function sqlUpdate($table, $set, $where='', $column_types=array())
  {
    return $this->sqlExec($this->makeUpdateString($table, $set, $where, $column_types));
  }

  public function sqlDelete($table, $where='')
  {
    return $this->sqlExec($this->makeDeleteString($table, $where));
  }

  abstract public function parseBatchSql(&$ret, $sql, $release);

  abstract protected function _resultNumFields();

  abstract protected function _fetchAssocResultRow($col_num = '');

  public function processValues($names_values, $column_types=array())
  {
    foreach($names_values as $key => $value)
    {
      $type = isset($column_types[$key]) ? $column_types[$key] : '';
      $names_values[$key] = $this->_processValue($value, $type);
    }

    return $names_values;
  }

  protected function _processValue($value, $type='')
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
        return $this->_processStringValue($value);
      break;
      case 'boolean':
        return $this->_processBoolValue($value);
      break;
      case 'null':
        return $this->null();
      break;
      case 'date':
        return $this->_processDateValue($value);
      break;
      case 'datetime':
        return $this->_processDatetimeValue($value);
      break;
      case 'default';
      default:
        return $this->_processDefaultValue($value);
    }
  }

  protected function _processStringValue($value)
  {
    return "'" . $this->escape($value) . "'";
  }

  protected function _processBoolValue($value)
  {
    return ($value) ? 1 : 0;
  }

  protected function _processDateValue($value)
  {
    $locale = Limb :: toolkit()->getLocale($this->_locale_id);
    $date = new Date($value, DATE_SHORT_FORMAT_ISO);

    if(!$date->isValid())
    {
      $date->setByLocaleString($locale, $value, $locale->getShortDateFormat());
      $value = $date->format($locale, DATE_SHORT_FORMAT_ISO);
    }

    return "'" . $value . "'";
  }

  protected function _processDatetimeValue($value)
  {
    $locale = Limb :: toolkit()->getLocale($this->_locale_id);
    $date = new Date($value, DATE_FORMAT_ISO);

    if(!$date->isValid())
    {
      $date->setByLocaleString($locale, $value, $locale->getShortDateTimeFormat());
      $value = $date->format($locale, DATE_FORMAT_ISO);
    }

    return "'" . $value . "'";
  }

  protected function _processDefaultValue($value)
  {
    return strval($value);
  }

  abstract public function getLastError();

  abstract public function getSqlInsertId();

  public function getMaxColumnValue($table_name, $column_name)
  {
    $sql = 'SELECT MAX('. $column_name .') as m FROM '. $table_name;

    $this->sqlExec($sql);
    $arr = $this->fetchRow();

    return isset($arr['m']) ? $arr['m'] : 0;
  }

  public function fetchRow()
  {
    return $this->_fetchAssocResultRow();
  }

  abstract public function countSelectedRows();

  public function makeInsertString($table, $names_values, $column_types=array())
  {
    if(is_array($names_values))
      $names_values = $this->processValues($names_values, $column_types);

    $keys = array_keys($names_values);
    $values = array_values($names_values);

    $str_names = '(' . implode(',', $keys) . ')';
    $str_values = '(' . implode(',', $values) . ')';

    return "INSERT INTO $table $str_names VALUES $str_values";
  }

  public function makeSelectString($table, $fields='*', $where='', $order='', $count=0, $start=0)
  {
    if(is_array($where))
      $where = ' WHERE (' . $this->sqlAnd($where) . ')';
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

  public function makeUpdateString($table, $names_values, $where='', $column_types=array())
  {
    if(is_array($names_values))
      $names_values = $this->processValues($names_values, $column_types);

    if(is_array($where))
      $where = ' WHERE (' . $this->sqlAnd($where) . ')';
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

  public function makeDeleteString($table, $where='')
  {
    if(is_array($where))
      $where = ' WHERE (' . $this->sqlAnd($where) . ')';
    elseif($where)
      $where = ' WHERE (' . $where . ')';

    return "DELETE FROM $table $where";
  }

  public function sqlIn($column_name, $values, $type='')
  {
    $implode_values = array();
    foreach($values as $value)
      $implode_values[] = $this->_processValue($value, $type);

    $in_ids = implode(' , ', $implode_values);

    return $column_name . ' IN (' . $in_ids . ')';
  }

  public function sqlAnd($conditions, $column_types=array())
  {
    $implode_values = array();

    foreach($conditions as $key => $value)
    {
      $value = $this->_processValue($value, isset($column_types[$key]) ? $column_types[$key] : '');

      $implode_values[] = "($key=$value)";
    }
    return implode(' AND ', $implode_values);
  }

  public function begin()
  {
    if($this->_transaction_status == self :: DB_TRANSACTION_STATUS_OUT)
    {
      $this->_beginOperation();
      $this->_transaction_status = self :: DB_TRANSACTION_STATUS_IN;
    }
  }

  abstract protected function _beginOperation();

  public function commit()
  {
    if($this->_transaction_status == self :: DB_TRANSACTION_STATUS_IN)
    {
      $this->_commitOperation();
      $this->_transaction_status = self :: DB_TRANSACTION_STATUS_OUT;
    }
  }

  abstract protected function _commitOperation();

  public function rollback()
  {
    if($this->_transaction_status == self :: DB_TRANSACTION_STATUS_IN)
    {
      $this->_rollbackOperation();
      $this->_transaction_status = self :: DB_TRANSACTION_STATUS_OUT;
    }
  }

  abstract protected function _rollbackOperation();
}

function sqlAnd($conditions, $column_types=array())
{
  return DbFactory :: instance()->sqlAnd($conditions, $column_types);
}

function sqlIn($column_name, $values, $type='')
{
  return DbFactory :: instance()->sqlIn($column_name, $values, $type);
}

?>