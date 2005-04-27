<?php
require_once(dirname(__FILE__) . '/utf8_base_imp.class.php');

class utf8_mbstring_imp extends utf8_base_imp
{
  function utf8_strlen($string)
  {
    return mb_strlen($string, 'utf-8');
  }

  //mb_substr has buggy implementation???
  //function utf8_substr($str, $start, $length=null)
  //{
  //  return mb_substr($str, $start, $length, 'utf-8');
  //}

  //implement with mb_split ?
  //function utf8_explode($sep, $str){}

  //implement with mb_ereg_* ?
  //function utf8_str_replace($s,$r,$str){}

  //should be implemented with mb_split
  //function  utf8_ltrim($str, $charlist=''){}

  //should be implemented with mb_split
  //function  utf8_rtrim($str, $charlist=''){}

  //should be implemented with mb_split
  //function  utf8_trim($str,$charlist=''){}

  function utf8_strtolower($string)
  {
    return mb_strtolower($string, 'utf-8');
  }

  function utf8_strtoupper($string)
  {
    return mb_strtoupper($string, 'utf-8');
  }

  function utf8_strpos($haystack, $needle, $offset=0)
  {
    return mb_strpos($haystack, $needle, $offset, 'utf-8');
  }

  function utf8_strrpos($haystack, $needle)
  {
    return mb_strrpos($haystack, $needle, 'utf-8');
  }
}

