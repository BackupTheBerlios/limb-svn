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
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/db/SimpleQueryBuilder.class.php');
require_once(LIMB_DIR . '/core/etc/limb_util.inc.php');

class LimbDbTable
{
  var $_db_table_name;

  var $_primary_key_name;

  var $_columns = array();

  var $_constraints = array();

  var $_db = null;
  var $_stmt = null;

  function LimbDbTable()
  {
    $this->_db_table_name = $this->_defineDbTableName();
    $this->_columns = $this->_defineColumns();
    $this->_constraints = $this->_defineConstraints();
    $this->_primary_key_name = $this->_definePrimaryKeyName();

    $toolkit =& Limb :: toolkit();
    $this->_db =& $toolkit->getDbConnection();
  }

  function _defineDbTableName()
  {
    die('abstract function! ' . __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
  }

  function _definePrimaryKeyName()
  {
    return 'id';
  }

  function _defineColumns()
  {
    return array();
  }

  function _defineConstraints()
  {
    return array();
  }

  function hasColumn($name)
  {
    return isset($this->_columns[$name]);
  }

  function getColumns()
  {
    return $this->_columns;
  }

  function getConstraints()
  {
    return $this->_constraints;
  }

  function getColumnTypes()
  {
    $types = array();
    foreach(array_keys($this->_columns) as $column_name)
      $types[$column_name] = $this->getColumnType($column_name);

    return $types;
  }

  function getColumnType($column_name)
  {
    if(!$this->hasColumn($column_name))
      return false;

    return (is_array($this->_columns[$column_name]) &&  isset($this->_columns[$column_name]['type'])) ?
      $this->_columns[$column_name]['type'] :
      '';
  }

  function getPrimaryKeyName()
  {
    return $this->_primary_key_name;
  }

  function & getStatement()
  {
    return $this->_stmt;
  }

  function getAffectedRowCount()
  {
    if($this->_stmt)
      return $this->_stmt->getAffectedRowCount();
    else
      return 0;
  }

  function insert($row)
  {
    $filtered_row = $this->_filterRow($row);

    $sql = SimpleQueryBuilder :: buildInsertSQL($this->_db_table_name, array_keys($filtered_row));

    $this->_stmt =& $this->_db->newStatement($sql);

    $this->_fillStatementVariables($filtered_row);

    return $this->_stmt->insertId($this->_primary_key_name);
  }

  function update($row, $conditions = array())
  {
    $row = $this->_filterRow($row);
    $conditions = $this->_filterRow($conditions);

    $sql = SimpleQueryBuilder :: buildUpdateSQL($this->_db_table_name,
                                                array_keys($row),
                                                array_keys($conditions));

    $this->_stmt =& $this->_db->newStatement($sql);

    $prefix = SimpleQueryBuilder :: getUpdatePrefix();
    foreach($row as $key => $value)
      $prefixed_row[$prefix . $key] = $value;

    $this->_fillStatementVariables($prefixed_row);
    $this->_fillStatementVariables($conditions);

    return $this->_stmt->execute();
  }

  function updateById($id, $data)
  {
    return $this->update($data, array($this->_primary_key_name => $id));
  }

  function & selectRecordById($id)
  {
    $record_set =& $this->select(array($this->_primary_key_name => $id));
    $record_set->rewind();

    if(!$record_set->valid())
      return null;
    else
      return $record_set->current();
  }

  function & select($conditions = array(), $order = '')
  {
    $conditions = $this->_filterRow($conditions);

    $sql = SimpleQueryBuilder :: buildSelectSQL($this->_db_table_name,
                                                $this->getColumnsForSelect(),
                                                array_keys($conditions),
                                                $order);

    $this->_stmt =& $this->_db->newStatement($sql);

    $this->_fillStatementVariables($conditions);

    return new SimpleDbDataset($this->_stmt->getRecordSet());
  }

  function delete($conditions = array())
  {
    $conditions = $this->_filterRow($conditions);

    $affected_rows =& $this->_prepareAffectedRows($conditions);

    $this->_cascadeDelete($affected_rows);

    $this->_deleteOperation($conditions, $affected_rows);
  }

  function _deleteOperation($conditions, &$affected_rows)
  {
    $sql = SimpleQueryBuilder :: buildDeleteSQL($this->_db_table_name,
                                                array_keys($conditions));

    $this->_stmt =& $this->_db->newStatement($sql);
    $this->_fillStatementVariables($conditions);

    $this->_stmt->execute();
  }

  function deleteById($id)
  {
    return $this->delete(array($this->_primary_key_name => $id));
  }

  function getTableName()
  {
    return $this->_db_table_name;
  }

  function _cascadeDelete($affected_rows)
  {
    if(LimbDbTable :: autoConstraintsEnabled())
      return;

    if(!$affected_rows->getTotalRowCount())
      return;

    foreach($this->_constraints as $id => $constraints_array)
    {
      foreach($constraints_array as $key => $constraint_params)
      {
        $table_name = $constraint_params['table_name'];
        $column_name = $constraint_params['field'];

        $class_name = $this->_mapTableNameToClass($table_name);

        $toolkit =& Limb :: toolkit();
        $db_table =& $toolkit->createDBTable($class_name);

        if(!$db_table->hasColumn($column_name))
        {
          return throw(new SQLException('column not found while cascade deleting',
            null,
            array(
              'table' => $table_name,
              'column_name' => $column_name
            )
          ));
        }

        for($affected_rows->rewind(); $affected_rows->valid(); $affected_rows->next())
        {
          $record =& $affected_rows->current();

          //NOTE!!! This should be improved later by using IN condition
          $db_table->delete(array($column_name => $record->get($id)));
        }
      }
    }
  }

  function _mapTableNameToClass($table_name)
  {
    //this probably should be moved to toolkit...
    return toStudlyCaps($table_name);

  }

  function _prepareAffectedRows($conditions)
  {
    $affected_rows = array();

    if(LimbDbTable :: autoConstraintsEnabled())
      return $affected_rows;

    return $this->select($conditions);
  }

  function autoConstraintsEnabled()
  {
    return (defined('DB_AUTO_CONSTRAINTS') &&  DB_AUTO_CONSTRAINTS == true);
  }

  function getColumnsForSelect($table_name = '', $exclude_columns = array())
  {
    if(!$table_name)
      $table_name = $this->getTableName();

    $columns = $this->getColumns();
    $implode_arr = array();
    foreach($columns as $key => $descr)
    {
      if(!in_array($key, $exclude_columns))
        $implode_arr[] = $table_name . '.' . $key . ' as ' . $key;
    }

    return $implode_arr;
  }

  function getColumnsForSelectAsString($table_name = '', $exclude_columns = array())
  {
    return implode(', ', $this->getColumnsForSelect($table_name, $exclude_columns));
  }

  function _fillStatementVariables($values)
  {
    $column_types = $this->getColumnTypes();

    foreach($values as $key => $value)
    {
      $type = isset($column_types[$key]) ? $column_types[$key] : '';
      $this->_fillStatementValue($key, $value, $type);
    }
  }

  function _fillStatementValue($key, $value, $type='default')
  {
    switch(strtolower($type))
    {
      case 'numeric':
      case 'int':
      case 'integer':
        $this->_stmt->setInteger($key, $value);
      break;
      case 'float':
        $this->_stmt->setFloat($key, $value);
      break;
      case 'decimal':
        $this->_stmt->setDecimal($key, $value);
      break;
      case 'double':
        $this->_stmt->setDouble($key, $value);
      break;
      case 'clob':
      case 'text':
        $this->_stmt->setText($key, $value);
      break;
      case 'blob':
        $this->_stmt->setBlob($key, $value);
      break;
      case 'string':
        $this->_stmt->setVarChar($key, $value);
      break;
      case 'boolean':
        $this->_stmt->setBoolean($key, $value);
      break;
      case 'null':
        $this->_stmt->setNull($key);
      break;
      case 'date':
        $this->_stmt->setDate($key, $value);
      break;
      case 'time':
        $this->_stmt->setTime($key, $value);
      break;
      case 'datetime':
        $this->_stmt->setTimeStamp($key, $value);
      break;
      case 'default';
      default:
        $this->_stmt->set($key, $value);
    }
  }

  function _filterRow($row)
  {
    if (!is_array($row))
      return array();

    $filtered = array();
    foreach($row as $key => $value)
    {
      if($this->hasColumn($key))
        $filtered[$key] = $value;
    }
    return $filtered;
  }
}

?>