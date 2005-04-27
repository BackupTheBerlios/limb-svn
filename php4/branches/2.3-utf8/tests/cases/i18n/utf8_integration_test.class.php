<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: fs_test.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/i18n/utf8.inc.php');
require_once(dirname(__FILE__) . '/utf8_test.class.php');

class utf8_global_functions_imp
{
  //__call overloading won't work properly in PHP4 :(
  function utf8_global_functions_imp(){}

  function utf8_encodeFN($file, $safe=true){return utf8_encodeFN($file, $safe);}
  function utf8_decodeFN($file){return utf8_decodeFN($file);}
  function utf8_isASCII($str){return utf8_isASCII($str);}
  function utf8_strip($str){return utf8_strip($str);}
  function utf8_check($str){return utf8_check($str);}
  function utf8_strlen($string){return utf8_strlen($string);}
  function utf8_substr($str, $start, $length=null){return utf8_substr($str, $start, $length);}
  function utf8_explode($sep, $str){return utf8_explode($sep, $str);}
  function utf8_str_replace($s, $r, $str){return utf8_str_replace($s, $r, $str);}
  function utf8_ltrim($str, $charlist=''){return utf8_ltrim($str, $charlist);}
  function  utf8_rtrim($str, $charlist=''){return utf8_rtrim($str, $charlist);}
  function  utf8_trim($str, $charlist=''){return utf8_trim($str, $charlist);}
  function utf8_strtolower($string){return utf8_strtolower($string);}
  function utf8_strtoupper($string){return utf8_strtoupper($string);}
  function utf8_deaccent($string, $case=0){return utf8_deaccent($string, $case);}
  function utf8_stripspecials($string, $repl='', $keep=''){return utf8_stripspecials($string, $repl, $keep);}
  function utf8_strpos($haystack, $needle, $offset=0){return utf8_strpos($haystack, $needle, $offset);}
  function utf8_to_unicode($str){return utf8_to_unicode($str);}
  function unicode_to_utf8($str){return unicode_to_utf8($str);}
}

class utf8_integration_test extends utf8_test
{
  function _create_utf8_imp()
  {
    return new utf8_global_functions_imp();
  }
}

?>