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

class ComplexArray
{
  protected function __construct(){}


  static public function map($map_array, $src_array, &$dest_array)
  {
    foreach($map_array as $src => $dest)
      if(isset($src_array[$src]))
        $dest_array[$dest] = $src_array[$src];
  }

  static public function array_merge($a1, $a2)
  {
    $n = $a1;
    foreach($a2 as $k => $v)
      if(is_array($v) &&  isset($n[$k]) &&  is_array($n[$k]))
        $n[$k] = ComplexArray :: array_merge($n[$k], $v);
      else
        $n[$k] = $v;
    return $n;
  }

  static public function & arrayGet($arr_def, &$res_array, $default_value='')
  {
    if($size = sizeof($arr_def))
    {
      $key = array_shift($arr_def);

      if(is_array($res_array) &&  isset($res_array[$key]))
        if($size > 1)
          return self :: arrayGet($arr_def, $res_array[$key]);
        elseif($size == 1)
          return $res_array[$key];
    }

    return $default_value;
  }

  static public function arraySet($arr_def, &$res_array, $value)
  {
    if($size = sizeof($arr_def))
    {
      $key = array_shift($arr_def);

      if($size > 1)
      {
        if (!isset($res_array[$key]))
          $res_array[$key] = array();

        self :: arraySet($arr_def, $res_array[$key], $value);
      }
      elseif($size == 1)
        $res_array[$key] = $value;
    }
  }

  static public function getColumnValues($column_name, $array)
  {
    $result = array();
    foreach($array as $item)
      $result[] = $item[$column_name];

    return $result;
  }

  static public function getMaxColumnValue($column_name, $array, &$index)
  {
    $index = 0;

    if(!$values = self :: getColumnValues($column_name, $array))
      return false;

    $max = max($values);

    $index = array_search($max, $values);

    return $max;
  }

  static public function getMinColumnValue($column_name, $array, &$index)
  {
    $index = 0;

    if(!$values = self :: getColumnValues($column_name, $array))
      return false;

    $min = min($values);

    $index = array_search($min, $values);

    return $min;
  }

  static public function toFlatArray($array, &$result, $prefix='')
  {
    foreach($array as $key => $value)
    {
      $string_key = ($prefix) ? '[' . $key . ']' : $key;

      if(is_array($value))
        self :: toFlatArray($value, $result, $prefix . $string_key);
      else
        $result[$prefix . $string_key] = $value;
    }
  }

  static public function arrayMapRecursive($in_func, &$in_array)
  {
    foreach (array_keys($in_array) as $key)
    {
      $value =& $in_array[$key];

      if (is_array($value))
        self :: arrayMapRecursive($in_func, $value);
      else
        $value = call_user_func_array($in_func, array($value));
    }
    return $in_array;
  }

  //e.g, $sort_params = array('field1' => 'DESC', 'field2' => 'ASC')
  static public function & sortArray($array, $sort_params, $preserve_keys = true)
  {
   $array_mod = array();
   foreach ($array as $key => $value)
     $array_mod['_' . $key] = $value;

   $i = 0;
   $multi_sort_line = "return array_multisort( ";
   foreach ($sort_params as $name => $sort_type)
   {
     $i++;
     foreach ($array_mod as $row_key => $row)
      $sort_values[$i][] = $row[$name];

     if($sort_type	== 'DESC')
      $sort_args[$i] = SORT_DESC;
     else
      $sort_args[$i] = SORT_ASC;

     $multi_sort_line .= '$sort_values[' . $i . '], $sort_args[' . $i . '], ';
   }
   $multi_sort_line .= '$array_mod );';

   eval($multi_sort_line);

   $array = array();
   foreach ($array_mod as $key => $value)
   {
     if($preserve_keys)
      $array[ substr($key, 1) ] = $value;
     else
      $array[] = $value;
   }

   return $array;
  }
}

?>