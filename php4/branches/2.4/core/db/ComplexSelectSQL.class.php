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
  var $_template_sql;
  var $_processed_sql;
  var $_fields;
  var $_tables;
  var $_constraints;
  var $_left_join_constraints;
  var $_order;
  var $_group_by;

  function ComplexSelectSQL($template_sql)
  {
    $this->_template_sql = $template_sql;
    $this->reset();
  }

  function reset()
  {
    $this->_processed_sql = $this->_template_sql;
    $this->_fields = array();
    $this->_tables = array();
    $this->_constraints = array();
    $this->_left_join_constraints = array();
    $this->_order = array();
    $this->_group_by = array();
  }

  function addField($field)
  {
    $this->_fields[] = $field;
  }

  function addTable($table)
  {
    $this->_tables[] = $table;
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

  function addLeftJoin($table, $connect_by)
  {
    $this->_left_join_constraints[$table] = $connect_by;
  }

  function addCondition($condition)
  {
    $this->_constraints[] = $condition;
  }

  function toString()
  {
    $replace = array('%fields%' => $this->_createFieldsClause(),
                     '%tables%' => $this->_createTablesClause(),
                     '%left_join%' => $this->_createLeftJoinClause(),
                     '%where%' => $this->_createWhereClause(),
                     '%order%' => $this->_createOrderClause(),
                     '%group_by%' => $this->_createGroupClause());

    return trim(strtr($this->_processed_sql, $replace));
  }

  function _getNoTagsSQL()
  {
    $replace = array('%fields%' => '',
                     '%tables%' => '',
                     '%left_join%' => '',
                     '%where%' => '',
                     '%order%' => '',
                     '%group_by%' => '');

    return strtr($this->_template_sql, $replace);
  }

  function _createFieldsClause()
  {
    $fields = implode(',', $this->_fields);

    if($this->_selectFieldsExist())
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

  function _createTablesClause()
  {
    if (count($this->_tables) == 0)
      return '';

    return ',' . implode(',', $this->_tables);
  }

  function _createLeftJoinClause()
  {
    $join = array();
    foreach ($this->_left_join_constraints as $table => $connect_by)
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

    if($this->_whereClauseExists())
      return $where;
    else
      return 'WHERE ' . $where;
  }

  function _createGroupClause()
  {
    if (count($this->_group_by) == 0)
      return '';

    $group = implode(',', $this->_group_by);

    if($this->_groupByClauseExists($group_by_args))
    {
      //primitive check if comma is required
      if($group_by_args)
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

    if($this->_orderByClauseExists($order_by_args))
    {
      //primitive check if comma is required
      if($order_by_args)
        return ',' . $order;
      else
        return $order;
    }
    else
      return 'ORDER BY ' . $order;
  }

  function _orderByClauseExists(&$args)
  {
    //!!!make it better later
    if(preg_match('~(?<=from).+order\s+by\s(.*)$~i', $this->_getNoTagsSQL(), $matches))
    {
      $args = trim($matches[1]);
      return true;
    }

    return false;
  }

  function _groupByClauseExists(&$args)
  {
    //!!!make it better later
    if(preg_match('~(?<=from).+group\s+by\s(.*)$~i', $this->_getNoTagsSQL(), $matches))
    {
      $args = trim($matches[1]);
      return true;
    }

    return false;
  }

  function _selectFieldsExist()
  {
    //!!!make it better later
    return preg_match('~^select\s+[a-zA-Z].*?from~i', $this->_getNoTagsSQL());
  }

  function _whereClauseExists()
  {
    //primitive check if WHERE was already in sql
    //!!!make it better later
    return preg_match('~(?<=from).+where\s~i', $this->_getNoTagsSQL());
  }
}
?>
