<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/i18n/locale.class.php');
require_once(LIMB_DIR . '/core/lib/i18n/utf8.inc.php');

class currency
{
  function currency()
  {
  }

  function locale_format($number, $locale_string = null)
  {
    $locale = locale :: instance($locale_string);

    $neg = $number < 0;
    $num = $neg ? -$number : $number;
    $num_text =& number_format( $num, $locale->get_currency_fract_digits(),
                                $locale->get_currency_decimal_symbol(), $locale->get_currency_thousand_separator() );
    $text =& utf8_str_replace(array('%c', '%p', '%q' ),
                              array($locale->get_currency_symbol(),
                                 $neg ? $locale->get_currency_negative_symbol() : $locale->get_currency_positive_symbol(),
                                 $num_text ),
                          $neg ? $locale->get_currency_negative_format() : $locale->get_currency_positive_format());
    return $text;

  }
}
?>