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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/etc/limb_util.inc.php');

class DbTable
{
  var $_db_table_name;

  var $_primary_key_name;

  var $_columns = array();

  var $_constraints = array();

  var $_db = null;

  function DbTable()
  {
    $this->_db_table_name = $this->_defineDbTableName();
    $this->_columns = $this->_defineColumns();
    $this->_constraints = $this->_defineConstraints();
    $this->_primary_key_name = $this->_definePrimaryKeyName();

    $toolkit =& Limb :: toolkit();
    $this->_db =& $toolkit->getDB();
  }

  function _defineDbTableName()
  {
    $class_name = get_class($this);

    if(($pos = strpos($class_name, 'DbTable')) !== false)
      $class_name = substr($class_name, 0, $pos);

    $table_name = to_under_scores($class_name);

    return $table_name;
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

  function insert($row)
  {
    $filtered_row = $this->_filterRow($row);

    return $this->_db->sqlInsert($this->_db_table_name, $filtered_row, $this->getColumnTypes());
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

  function update($row, $conditions)
  {
    $filtered_row = $this->_filterRow($row);

    return $this->_db->sqlUpdate($this->_db_table_name, $filtered_row, $conditions, $this->getColumnTypes());
  }

  function updateById($id, $data)
  {
    return $this->update($data, "{$this->_primary_key_name}='{$id}'");
  }

  function getRowById($id)
  {
    $data = $this->getList($this->_primary_key_name . "='{$id}'");

    return current($data);
  }

  function getList($conditions='', $order='', $group_by_column='', $start=0, $count=0)
  {
    $this->_db->sqlSelect($this->_db_table_name, '*', $conditions, $order, $start, $count);

    if ($group_by_column === '')
      $group_by_column = $this->_primary_key_name;

    if($group_by_column)
      return $this->_db->getArray($group_by_column);
    else
      return $this->_db->getArray();
  }

  function delete($conditions='')
  {
    $affected_rows = $this->_prepareAffectedRows($conditions);

    $this->_deleteOperation($conditions, $affected_rows);

    $this->_cascadeDelete($affected_rows);

    return true;
  }

  function _deleteOperation($conditions, $affected_rows)
  {
    $this->_db->sqlDelete($this->_db_table_name, $conditions);
  }

  function deleteById($id)
  {
    return $this->delete(array($this->_primary_key_name => $id));
  }

  function getLastInsertId()
  {
    return $this->_db->getSqlInsertId($this->_db_table_name, $this->_primary_key_name);
  }

  function getMaxId()
  {
    return $this->_db->getMaxColumnValue($this->_db_table_name, $this->_primary_key_name);
  }

  function getTableName()
  {
    return $this->_db_table_name;
  }

  function _cascadeDelete($affected_rows)
  {
    if(DbTable :: autoConstraintsEnabled())
      return;

    if (!count($affected_rows))
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
          return new SQLException('column not found while cascade deleting',
            null,
            array(
              'table' => $table_name,
              'column_name' => $column_name
            )
          );
        }

        $values = array();
        foreach($affected_rows as $data)
          $values[] = $data[$id];

        $db_table->delete(
          sqlIn($column_name, $values, $db_table->getColumnType($column_name)));
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

    if(DbTable :: autoConstraintsEnabled())
      return $affected_rows;

    return $this->getList($conditions);
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

    return implode(', ', $implode_arr);
  }
}

?>