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
  protected $_db_table_name;

  protected $_primary_key_name;

  protected $_columns = array();

  protected $_constraints = array();

  protected $_db = null;

  function __construct()
  {
    $this->_db_table_name = $this->_defineDbTableName();
    $this->_columns = $this->_defineColumns();
    $this->_constraints = $this->_defineConstraints();
    $this->_primary_key_name = $this->_definePrimaryKeyName();

    $this->_db = Limb :: toolkit()->getDB();
  }

  protected function _defineDbTableName()
  {
    $class_name = get_class($this);

    if(($pos = strpos($class_name, 'DbTable')) !== false)
      $class_name = substr($class_name, 0, $pos);

    $table_name = to_under_scores($class_name);

    return $table_name;
  }

  protected function _definePrimaryKeyName()
  {
    return 'id';
  }

  protected function _defineColumns()
  {
    return array();
  }

  protected function _defineConstraints()
  {
    return array();
  }

  public function hasColumn($name)
  {
    return isset($this->_columns[$name]);
  }

  public function getColumns()
  {
    return $this->_columns;
  }

  public function getConstraints()
  {
    return $this->_constraints;
  }

  public function getColumnTypes()
  {
    $types = array();
    foreach(array_keys($this->_columns) as $column_name)
      $types[$column_name] = $this->getColumnType($column_name);

    return $types;
  }

  public function getColumnType($column_name)
  {
    if(!$this->hasColumn($column_name))
      return false;

    return (is_array($this->_columns[$column_name]) &&  isset($this->_columns[$column_name]['type'])) ?
      $this->_columns[$column_name]['type'] :
      '';
  }

  public function getPrimaryKeyName()
  {
    return $this->_primary_key_name;
  }

  public function insert($row)
  {
    $filtered_row = $this->_filterRow($row);

    return $this->_db->sqlInsert($this->_db_table_name, $filtered_row, $this->getColumnTypes());
  }

  protected function _filterRow($row)
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

  public function update($row, $conditions)
  {
    $filtered_row = $this->_filterRow($row);

    return $this->_db->sqlUpdate($this->_db_table_name, $filtered_row, $conditions, $this->getColumnTypes());
  }

  public function updateById($id, $data)
  {
    return $this->update($data, "{$this->_primary_key_name}='{$id}'");
  }

  public function getRowById($id)
  {
    $data = $this->getList($this->_primary_key_name . "='{$id}'");

    return current($data);
  }

  public function getList($conditions='', $order='', $group_by_column='', $start=0, $count=0)
  {
    $this->_db->sqlSelect($this->_db_table_name, '*', $conditions, $order, $start, $count);

    if ($group_by_column === '')
      $group_by_column = $this->_primary_key_name;

    if($group_by_column)
      return $this->_db->getArray($group_by_column);
    else
      return $this->_db->getArray();
  }

  public function delete($conditions='')
  {
    $affected_rows = $this->_prepareAffectedRows($conditions);

    $this->_deleteOperation($conditions, $affected_rows);

    $this->_cascadeDelete($affected_rows);

    return true;
  }

  protected function _deleteOperation($conditions, $affected_rows)
  {
    $this->_db->sqlDelete($this->_db_table_name, $conditions);
  }

  public function deleteById($id)
  {
    return $this->delete(array($this->_primary_key_name => $id));
  }

  public function getLastInsertId()
  {
    return $this->_db->getSqlInsertId($this->_db_table_name, $this->_primary_key_name);
  }

  public function getMaxId()
  {
    return $this->_db->getMaxColumnValue($this->_db_table_name, $this->_primary_key_name);
  }

  public function getTableName()
  {
    return $this->_db_table_name;
  }

  protected function _cascadeDelete($affected_rows)
  {
    if(self :: autoConstraintsEnabled())
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

        $db_table = Limb :: toolkit()->createDBTable($class_name);

        if(!$db_table->hasColumn($column_name))
        {
          throw new SQLException('column not found while cascade deleting',
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

  protected function _mapTableNameToClass($table_name)
  {
    //this probably should be moved to toolkit...
    return toStudlyCaps($table_name);

  }

  protected function _prepareAffectedRows($conditions)
  {
    $affected_rows = array();

    if(self :: autoConstraintsEnabled())
      return $affected_rows;

    return $this->getList($conditions);
  }

  static public function autoConstraintsEnabled()
  {
    return (defined('DB_AUTO_CONSTRAINTS') &&  DB_AUTO_CONSTRAINTS == true);
  }

  public function getColumnsForSelect($table_name = '', $exclude_columns = array())
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