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

//inspired by Marcus Baker's Changes :)

class SimpleSelectSQL
{
  var $_table;
  var $_joins;
  var $_fields;
  var $_aliases;
  var $_constraints;
  var $_join_constraints;
  var $_order;
  var $_group_by;

  function SimpleSelectSQL($table)
  {
    $this->_table = $table;

    $this->reset();
  }

  function reset()
  {
    $this->_joins = array();
    $this->_aliases = array();
    $this->_fields[$this->_table] = array();
    $this->_constraints = array();
    $this->_join_constraints = array();
    $this->_order = array();
    $this->_group_by = array();
  }

  function addField($field, $table = null)
  {
    if(!$table)
      $this->_fields[$this->_table][] = $field;
    else
      $this->_fields[$table][] = $field;
  }

  function addOrder($field, $type='ASC')
  {
    $this->_order[] = "$field $type";
  }

  function addGroupBy($group)
  {
    $this->_group_by[] = $group;
  }

  function addJoin($table, $connect_by, $fields = array(), $alias = null)
  {
    if(!$alias)
      $alias = $table;

    $foreign_key = key($connect_by);
    $alias_key = reset($connect_by);

    $this->_fields[$alias] = array();

    foreach($fields as $key => $value)
    {
      if(is_numeric($key))
        $this->_fields[$alias][] = $value;
      else
        $this->_fields[$alias][] = array($key => $value);
    }

    $this->_aliases[$alias] = $table;
    $this->_join_constraints[$alias] = $this->_table. ".$foreign_key = $alias.$alias_key";
  }

  function addCondition($condition)
  {
    $this->_constraints[] = $condition;
  }

  function toString()
  {
    $clauses = array(
            $this->_createSelectClause(),
            $this->_createFromClause(),
            $this->_createWhereClause(),
            $this->_createGroupClause(),
            $this->_createOrderClause());
    return trim(implode(' ', $clauses));
  }

  function _createSelectClause()
  {
    $select = array();
    foreach ($this->_fields as $table => $fields)
    {
      if(!sizeof($fields))
      {
        $select[] = "$table.*";
        continue;
      }

      foreach ($fields as $field_value)
      {
        if(is_array($field_value))
        {
          $field = key($field_value);
          $alias = current($field_value);

          $select[] = "$table.$field AS $alias";
        }
        else
          $select[] = "$table.$field_value";
      }
    }

    return 'SELECT ' . implode(',', $select);
  }

  function _createFromClause()
  {
    $tables = array($this->_table);
    foreach ($this->_aliases as $alias => $table)
      $tables[] = "LEFT JOIN $table AS $alias ON " . $this->_join_constraints[$alias];

    return 'FROM ' . implode(' ', $tables);
  }

  function _createWhereClause()
  {
    if (count($this->_constraints) == 0)
      return '';

    return 'WHERE (' . implode(') AND (', $this->_constraints) . ')';
  }

  function _createGroupClause()
  {
    if (count($this->_group_by) == 0)
      return '';

    return 'GROUP BY ' . implode(',', $this->_group_by);
  }

  function _createOrderClause()
  {
    if (count($this->_order) == 0)
      return '';

    return 'ORDER BY ' . implode(',', $this->_order);
  }

}
?>
