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

class ComplexSelectSQL
{
  var $_raw_sql;
  var $_joins;
  var $_fields;
  var $_constraints;
  var $_join_constraints;
  var $_order;
  var $_group_by;

  function ComplexSelectSQL($raw_sql)
  {
    $this->_raw_sql = $raw_sql;
    $this->reset();
  }

  function reset()
  {
    $this->_joins = array();
    $this->_fields = array();
    $this->_constraints = array();
    $this->_join_constraints = array();
    $this->_order = array();
    $this->_group_by = array();
  }

  function addField($field)
  {
    $this->_fields[] = $field;
  }

  function addOrder($field, $type='ASC')
  {
    $this->_order[] = "$field $type";
  }

  function addGroupBy($group)
  {
    $this->_group_by[] = $group;
  }

  function addJoin($table, $connect_by)
  {
    $this->_join_constraints[$table] = $connect_by;
  }

  function addCondition($condition)
  {
    $this->_constraints[] = $condition;
  }

  function toString()
  {
    $replace = array('%fields%' => $this->_createFieldsClause(),
                     '%join%' => $this->_createJoinClause(),
                     '%where%' => $this->_createWhereClause(),
                     '%order%' => $this->_createOrderClause(),
                     '%group_by%' => $this->_createGroupClause());

    return trim(strtr($this->_raw_sql, $replace));
  }

  function _getCleanSQL()
  {
    $replace = array('%fields%' => '',
                     '%join%' => '',
                     '%where%' => '',
                     '%order%' => '',
                     '%group_by%' => '');

    return strtr($this->_raw_sql, $replace);
  }

  function _createFieldsClause()
  {
    $sql = $this->_getCleanSQL();
    $fields = implode(',', $this->_fields);

    //primitive check if select fields were already in sql
    //!!!make it better later
    if(preg_match('~^select\s+[a-zA-Z].*?from~i', $sql))
    {
      if(count($this->_fields))
        return ',' . $fields;
      else
        return '';
    }
    elseif(count($this->_fields) == 0)
      return '*';
    else
      return $fields;
  }

  function _createJoinClause()
  {
    $join = array();
    foreach ($this->_join_constraints as $table => $connect_by)
    {
      $foreign_key = key($connect_by);
      $alias_key = reset($connect_by);
      $join[] = "LEFT JOIN $table ON $foreign_key=$alias_key";
    }

    return implode(' ', $join);
  }

  function _createWhereClause()
  {
    if (count($this->_constraints) == 0)
      return '';

    $where = '(' . implode(') AND (', $this->_constraints) . ')';

    $sql = $this->_getCleanSQL();

    //primitive check if WHERE was already in sql
    //!!!make it better later
    if(preg_match('~(?<=from).+where\s~i', $sql))
      return $where;
    else
      return 'WHERE ' . $where;
  }

  function _createGroupClause()
  {
    if (count($this->_group_by) == 0)
      return '';

    $group = implode(',', $this->_group_by);

    $sql = $this->_getCleanSQL();

    //primitive check if GROUP BY was already in sql
    //!!!make it better later
    if(preg_match('~(?<=from).+group\s+by\s(.*)$~i', $sql, $matches))
    {
      //primitive check if comma is required
      if(trim($matches[1]) != '')
        return ',' . $group;
      else
        return $group;
    }
    else
      return 'GROUP BY ' . $group;
  }

  function _createOrderClause()
  {
    if (count($this->_order) == 0)
      return '';

    $order = implode(',', $this->_order);

    $sql = $this->_getCleanSQL();

    //primitive check if ORDER BY was already in sql
    //!!!make it better later
    if(preg_match('~(?<=from).+order\s+by\s(.*)$~i', $sql, $matches))
    {
      //primitive check if comma is required
      if(trim($matches[1]) != '')
        return ',' . $order;
      else
        return $order;
    }
    else
      return 'ORDER BY ' . $order;
  }
}
?>
