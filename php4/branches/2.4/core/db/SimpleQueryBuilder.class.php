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

  function _buildWhere($conditions)
  {
    if(!empty($conditions))
    {
      if(is_array($conditions))
        return 'WHERE (' . SimpleQueryBuilder :: andCondition($conditions) . ')';
      else
        return 'WHERE ' . $conditions;
    }
    else
      return '';
  }

  function buildSelectSQL($table, $fields = '*', $conditions = array(), $order = '')
  {
    $where = SimpleQueryBuilder :: _buildWhere($conditions);

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
    $where = SimpleQueryBuilder :: _buildWhere($conditions);

    $prefix = SimpleQueryBuilder :: getUpdatePrefix();

    $implode_values = array();
    foreach($names as $key)
      $implode_values[] = "{$key}=:{$prefix}{$key}";

    $fields_str = implode(', ', $implode_values);


    return trim("UPDATE {$table} SET {$fields_str} {$where}");
  }

  function buildDeleteSQL($table, $conditions = array())
  {
    $where = SimpleQueryBuilder :: _buildWhere($conditions);

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

    foreach($conditions as $key => $value)
    {
      //we need to accept arrays like array('value') and
      //array('c1' => 'value'), the only way to make difference IMHO is
      //by analyzing the key
      if(is_string($key))
        $implode_values[] = "({$key}=:{$key})";
      else
        $implode_values[] = "({$value}=:{$value})";
    }

    return implode(' AND ', $implode_values);
  }
}

?>
