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
require_once(LIMB_DIR . '/core/i18n/Locale.class.php');

class LocaleNumberFormatComponent extends Component
{
  var $locale;

  var $fract_digits;
  var $decimal_symbol;
  var $thousand_separator;

  function format($value)
  {
    $toolkit =& Limb :: toolkit();
    $locale =& $toolkit->getLocale($this->locale);

    if(!$this->fract_digits)
      $this->fract_digits = $locale->fract_digits;

    if(!$this->decimal_symbol)
      $this->decimal_symbol = $locale->decimal_symbol;

    if(!$this->thousand_separator)
      $this->thousand_separator = $locale->thousand_separator;

    return number_format($value, $this->fract_digits, $this->decimal_symbol, $this->thousand_separator);
  }

  function setFractDigits($fract_digits)
  {
    $this->fract_digits = $fract_digits;
  }

  function setLocale($locale)
  {
    $this->locale = $locale;
  }

  function setDecimalSymbol($decimal_symbol)
  {
    $this->decimal_symbol = $decimal_symbol;
  }

  function setThousandSeparator($thousand_separator)
  {
    $this->thousand_separator = $thousand_separator;
  }

}

?>