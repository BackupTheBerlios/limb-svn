<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class uploaded_files_parser
{
  function parse($files)
  {
    $result = array();

    foreach($files as $key => $chunk)
    {
      if($this->_is_simple($chunk))
        $result[$key] = $chunk;
      else
        $result[$key] = $this->_parse_complex_chunk($chunk);
    }

    return $result;

  }

  function _is_simple($chunk)
  {
    if((isset($chunk['name']) && !is_array($chunk['name'])) &&
       (isset($chunk['error']) && !is_array($chunk['error'])) &&
       (isset($chunk['type']) && !is_array($chunk['type'])) &&
       (isset($chunk['size']) && !is_array($chunk['size'])) &&
       (isset($chunk['tmp_name']) && !is_array($chunk['tmp_name'])))
      return true;
    else
      return false;
  }

  function _parse_complex_chunk($chunk)
  {
    $result = array();
    foreach($chunk as $property_name => $data_set)
    {
      foreach($data_set as $arg_name => $value)
        $this->_parse_recursive_property_value($result[$arg_name], $property_name, $value);
    }
    return $result;
  }

  function _parse_recursive_property_value(&$result, $property_name, $data_set)
  {
    if(!is_array($data_set))
    {
      $result[$property_name] = $data_set;
      return;
    }

    foreach($data_set as $arg_name => $value)
    {
      $this->_parse_recursive_property_value($result[$arg_name], $property_name, $value);
    }
  }
}

?>