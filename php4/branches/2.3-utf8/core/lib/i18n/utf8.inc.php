<?php
//original ideas taken from http://dev.splitbrain.org/view/darcs/dokuwiki/inc/utf8.php
//this a bit improved and refactored version of that module
//with proper credits to original authors in utf8_base_imp.class.php

if(function_exists('mb_strlen'))//?
{
  include_once(dirname(__FILE__) . '/utf8_mbstring_imp.class.php');
  $GLOBALS['UTF8_DRIVER_IMP'] = new utf8_mbstring_imp();
}
else
{
  include_once(dirname(__FILE__) . '/utf8_base_imp.class.php');
  $GLOBALS['UTF8_DRIVER_IMP'] = new utf8_base_imp();
}

/**
 * URL-Encode a filename to allow unicodecharacters
 *
 * Slashes are not encoded
 *
 * When the second parameter is true the string will
 * be encoded only if non ASCII characters are detected -
 * This makes it safe to run it multiple times on the
 * same string (default is true)
 */
function utf8_encodeFN($file, $safe=true)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_encodeFN($file, $safe);
}

/**
 * URL-Decode a filename
 *
 * This is just a wrapper around urldecode
 */
function utf8_decodeFN($file)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_decodeFN($file);
}

/**
 * Checks if a string contains 7bit ASCII only
 */
function utf8_isASCII($str)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_isASCII($str);
}

/**
 * Strips all highbyte chars
 *
 * Returns a pure ASCII7 string
 */
function utf8_strip($str)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_strip($str);
}

/**
 * Tries to detect if a string is in Unicode encoding
 */
function utf8_check($str)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_check($str);
}

/**
 * Unicode aware replacement for strlen()
 */
function utf8_strlen($string)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_strlen($string);
}

/**
 * Unicode aware replacement for substr()
 */
function utf8_substr($str, $start, $length=null)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_substr($str, $start, $length);
}

/**
 * Unicode aware replacement for explode
 */
function utf8_explode($sep, $str)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_explode($sep, $str);
}

/**
 * Unicode aware replacement for strrepalce()
 */
function utf8_str_replace($s, $r, $str)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_str_replace($s, $r, $str);
}

/**
 * Unicode aware replacement for ltrim()
 */
function utf8_ltrim($str, $charlist='')
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_ltrim($str, $charlist);
}

/**
 * Unicode aware replacement for ltrim()
 */
function  utf8_rtrim($str, $charlist='')
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_rtrim($str, $charlist);
}

/**
 * Unicode aware replacement for trim()
 */
function  utf8_trim($str, $charlist='')
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_trim($str, $charlist);
}

/**
 * This is a unicode aware replacement for strtolower()
 */
function utf8_strtolower($string)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_strtolower($string);
}

/**
 * This is a unicode aware replacement for strtoupper()
 */
function utf8_strtoupper($string)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_strtoupper($string);
}

/**
 * Replace accented UTF-8 characters by unaccented ASCII-7 equivalents
 *
 * Use the optional parameter to just deaccent lower ($case = -1) or upper ($case = 1)
 * letters. Default is to deaccent both cases ($case = 0)
 */
function utf8_deaccent($string, $case=0)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_deaccent($string, $case);
}

/**
 * Removes special characters (nonalphanumeric) from a UTF-8 string
 */
function utf8_stripspecials($string, $repl='', $keep='')
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_stripspecials($string, $repl, $keep);
}

/**
 * This is an Unicode aware replacement for strpos
 */
function utf8_strpos($haystack, $needle, $offset=0)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_strpos($haystack, $needle, $offset);
}

/**
 * This function returns any UTF-8 encoded text as a list of
 * Unicode values:
 */
function utf8_to_unicode($str)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->utf8_to_unicode($str);
}

/**
 * This function converts a Unicode array back to its UTF-8 representation
 */
function unicode_to_utf8($str)
{
  return $GLOBALS['UTF8_DRIVER_IMP']->unicode_to_utf8($str);
}
