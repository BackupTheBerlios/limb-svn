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

class SimpleQueryBuilder
{
  function getUpdatePrefix()
  {
    return '_';
  }

  function buildSelectSQL($table, $fields = '*', $conditions = array(), $order = '')
  {
    if($conditions)
      $where = 'WHERE (' . SimpleQueryBuilder :: andCondition($conditions) . ')';
    else
      $where = '';

    if($order != '')
      $order = 'ORDER BY ' . $order;

    $fields_str = '';
    if(is_array($fields))
      $fields_str = implode(', ', $fields);
    else
      $fields_str = $fields;

    return trim("SELECT {$fields_str} FROM {$table} {$where} {$order}");
  }

  function buildInsertSQL($table, $names)
  {
    $str_names = '(' . implode(', ', $names) . ')';
    $str_values = '(:' . implode(', :', $names) . ')';

    return "INSERT INTO {$table} {$str_names} VALUES {$str_values}";
  }

  function buildUpdateSQL($table, $names, $conditions = array())
  {
    if($conditions)
      $where = 'WHERE (' . SimpleQueryBuilder :: andCondition($conditions) . ')';
    else
      $where = '';

    $prefix = SimpleQueryBuilder :: getUpdatePrefix();

    $implode_values = array();
    foreach($names as $key)
      $implode_values[] = "{$key}=:{$prefix}{$key}";

    $fields_str = implode(', ', $implode_values);


    return trim("UPDATE {$table} SET {$fields_str} {$where}");
  }

  function buildDeleteSQL($table, $conditions = array())
  {
    if($conditions)
      $where = 'WHERE (' . SimpleQueryBuilder :: andCondition($conditions) . ')';
    else
      $where = '';

    return trim("DELETE FROM {$table} {$where}");
  }

  function inCondition($column_name, $names)
  {
    $in = ':' . implode(', :', $names);

    return $column_name . ' IN (' . $in . ')';
  }

  function andCondition($conditions)
  {
    $implode_values = array();

    foreach($conditions as $key)
      $implode_values[] = "({$key}=:{$key})";

    return implode(' AND ', $implode_values);
  }
}

?>
