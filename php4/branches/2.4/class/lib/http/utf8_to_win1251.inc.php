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
function utf8ToWin1251($string)
{
  static $transition_table;

  if(!isset($transition_table))
    $transition_table = array(
         "%A8" => "%D0%81",
         "%B8" => "%D1%91",
         "%C0" => "%D0%90",
         "%C1" => "%D0%91",
         "%C2" => "%D0%92",
         "%C3" => "%D0%93",
         "%C4" => "%D0%94",
         "%C5" => "%D0%95",
         "%C6" => "%D0%96",
         "%C7" => "%D0%97",
         "%C8" => "%D0%98",
         "%C9" => "%D0%99",
         "%CA" => "%D0%9A",
         "%CB" => "%D0%9B",
         "%CC" => "%D0%9C",
         "%CD" => "%D0%9D",
         "%CE" => "%D0%9E",
         "%CF" => "%D0%9F",
         "%D0" => "%D0%A0",
         "%D1" => "%D0%A1",
         "%D2" => "%D0%A2",
         "%D3" => "%D0%A3",
         "%D4" => "%D0%A4",
         "%D5" => "%D0%A5",
         "%D6" => "%D0%A6",
         "%D7" => "%D0%A7",
         "%D8" => "%D0%A8",
         "%D9" => "%D0%A9",
         "%DA" => "%D0%AA",
         "%DB" => "%D0%AB",
         "%DC" => "%D0%AC",
         "%DD" => "%D0%AD",
         "%DE" => "%D0%AE",
         "%DF" => "%D0%AF",
         "%E0" => "%D0%B0",
         "%E1" => "%D0%B1",
         "%E2" => "%D0%B2",
         "%E3" => "%D0%B3",
         "%E4" => "%D0%B4",
         "%E5" => "%D0%B5",
         "%E6" => "%D0%B6",
         "%E7" => "%D0%B7",
         "%E8" => "%D0%B8",
         "%E9" => "%D0%B9",
         "%EA" => "%D0%BA",
         "%EB" => "%D0%BB",
         "%EC" => "%D0%BC",
         "%ED" => "%D0%BD",
         "%EE" => "%D0%BE",
         "%EF" => "%D0%BF",
         "%F0" => "%D1%80",
         "%F1" => "%D1%81",
         "%F2" => "%D1%82",
         "%F3" => "%D1%83",
         "%F4" => "%D1%84",
         "%F5" => "%D1%85",
         "%F6" => "%D1%86",
         "%F7" => "%D1%87",
         "%F8" => "%D1%88",
         "%F9" => "%D1%89",
         "%FA" => "%D1%8A",
         "%FB" => "%D1%8B",
         "%FC" => "%D1%8C",
         "%FD" => "%D1%8D",
         "%FE" => "%D1%8E",
         "%FF" => "%D1%8F"
         );

  static $flipped_transition_table;

  if(!isset($flipped_transition_table))
    $flipped_transition_table = array_flip($transition_table);


  static $html_entities_table;

  if(!isset($html_entities_table))
    $html_entities_table = array(
         "%C2%A7" => "&#167;",
         "%C2%A9" => "&#169;",
         "%C2%AB" => "&#171;",
         "%C2%AE" => "&#174;",
         "%C2%B0" => "&#176;",
         "%C2%B1" => "&#177;",
         "%C2%BB" => "&#187;",
         "%E2%80%93" => "&#150;",
         "%E2%80%94" => "&#151;",
         "%E2%80%9C" => "&#147;",
         "%E2%80%9D" => "&#148;",
         "%E2%80%9E" => "&#132;",
         "%E2%80%A6" => "&#133;",
         "%E2%84%96" => "&#8470;",
         "%E2%84%A2" => "&#153;",
         "%C2%A4" => "&curren;",
         "%C2%B6" => "&para;",
         "%C2%B7" => "&middot;",
         "%E2%80%98" => "&#145;",
         "%E2%80%99" => "&#146;",
         "%E2%80%A2" => "&#149;",
        );

  $string = strtr(urlencode($string), $flipped_transition_table);
  $string = strtr($string, $html_entities_table);
  return urldecode($string);
}

?>