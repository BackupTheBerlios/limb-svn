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
/*

Inspired by EZpublish(http://ez.no) locale class

Handles locale information and can format time, date, numbers and currency
for correct display for a given locale. The locale conversion uses plain numerical values for
dates, times, numbers and currency, if you want more elaborate conversions consider using the
date, time, date_time and currency classes.

The first time a locale object is created (ie. locale :: instance() ) you must be sure to set
a language using setlanguage before using any textual conversions.

Countries are specified by the ISO 3166 country Code
http://www.iso.ch/iso/en/prods-services/iso3166ma/index.html
User-assigned code elements
http://www.iso.ch/iso/en/prods-services/iso3166ma/04background-on-iso-3166/reserved-and-user-assigned-codes.html#userassigned

language is specified by the ISO 639 language Code
http://www.w3.org/WAI/ER/IG/ert/iso639.htm

currency/funds are specified by the ISO 4217
http://www.bsi-global.com/Technical+Information/Publications/_Publications/tig90.xalter
*/

require_once(LIMB_DIR . '/class/lib/util/Ini.class.php');

if(!defined('LOCALE_DIR'))
  define('LOCALE_DIR', LIMB_DIR . '/class/i18n/locale');

class Locale
{
  public $is_valid = false;

  public $date_format = ''; // format of dates
  public $short_date_format = ''; // format of short dates
  public $time_format = ''; // format of times
  public $date_time_format = '';
  public $short_date_time_format = '';
  public $short_time_format = ''; // format of short times
  public $is_monday_first = false; // true if monday is the first day of the week
  public $am_name = 'am';
  public $pm_name = 'pm';
  public $charset = '';
  public $override_charset = '';
  public $locale_code = '';
  public $http_locale_code = '';
  public $LC_ALL = '';
  // numbers
  public $decimal_symbol = '';
  public $thousand_separator = '';
  public $fract_digits = '';
  public $negative_symbol = '';
  public $positive_symbol = '';
  // currency
  public $currency_name = '';
  public $currency_short_name = '';
  public $currency_decimal_symbol = '';
  public $currency_thousand_separator = '';
  public $currency_fract_digits = '';
  public $currency_negative_symbol = '';
  public $currency_positive_symbol = '';
  public $currency_symbol = '';
  public $currency_positive_format = '';
  public $currency_negative_format = '';
  // help arrays
  public $short_month_names = array();
  public $long_month_names = array();
  public $short_day_names = array();
  public $long_day_names = array();
  public $week_days = array();
  public $months = array();

  public $country = '';
  public $country_code = '';
  public $country_variation = '';
  public $country_comment = '';
  public $language_comment = '';
  // Objects
  public $locale_ini = array('default' => null, 'variation' => null);
  public $country_ini = array('default' => null, 'variation' => null);
  public $language_ini = array('default' => null, 'variation' => null);

  public $language_code = ''; // the language code, for instance nor-NO, or eng-GB
  public $language_name = ''; // name of the language
  public $intl_language_name = ''; // internationalized name of the language
  public $language_direction = 'ltr';

  /*
   Initializes the locale with the locale string $locale_string.
   All locale data is read from locale $locale_string.ini
  */
  public function locale($locale_string = '')
  {
    $this->http_locale_code = '';

    $this->week_days = array(0, 1, 2, 3, 4, 5, 6);
    $this->months = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);

    $locale = self :: getLocaleInformation($locale_string);

    $this->country_code = $locale['country'];
    $this->country_variation = $locale['country-variation'];
    $this->language_code = $locale['language'];
    $this->locale_code = $locale['locale'];
    $this->charset = $locale['charset'];
    $this->override_charset = $locale['charset'];

    // Figure out if we use one locale file or separate country/language file.
    $locale_ini = $this->getLocaleIni();
    $country_ini = $locale_ini;
    $language_ini = $locale_ini;

    if ($locale_ini === null)
    {
      $country_ini = $this->getCountryIni();
      $language_ini = $this->getLanguageIni();
    }

    $this->_reset();

    $this->is_valid = true;

    if ($country_ini !== null)
      $this->_initCountrySettings($country_ini);
    else
    {
      $this->is_valid = false;
      throw new LimbException('Could not load country settings', array('country_code' => $this->country_code));
    }

    if ($language_ini !== null)
      $this->_initLanguageSettings($language_ini);
    else
    {
      $this->is_valid = false;
      throw new LimbException('Could not load language settings', array('language_code' => $this->language_code));
    }
    // load variation if any
    $locale_variation_ini = $this->getLocaleIni(true);
    $country_variation_ini = $locale_variation_ini;
    $language_variation_ini = $locale_variation_ini;
    if ($locale_variation_ini === null)
    {
      $country_variation_ini = $this->getCountryIni(true);
      $language_variation_ini = $this->getLanguageIni(true);
    }

    if ($country_variation_ini !== null &&  $country_variation_ini->getOriginalFile() != $country_ini->getOriginalFile())
      $this->_initCountrySettings($country_variation_ini);

    if ($language_variation_ini !== null &&  $language_variation_ini->getOriginalFile() != $language_ini->getOriginalFile())
      $this->_initLanguageSettings($language_variation_ini);
  }

  protected function _reset()
  {
    $this->time_format = '';
    $this->short_time_format = '';
    $this->date_format = '';
    $this->short_date_format = '';
    $this->date_time_format = '';
    $this->short_date_time_format = '';

    $this->is_monday_first = false;

    $this->country = '';
    $this->country_comment = '';

    $this->decimal_symbol = '';
    $this->thousand_separator = '';
    $this->fract_digits = 2;
    $this->negative_symbol = '-';
    $this->positive_symbol = '';

    $this->currency_decimal_symbol = '';
    $this->currency_name = '';
    $this->currency_short_name = '';
    $this->currency_thousand_separator = '';
    $this->currency_fract_digits = 2;
    $this->currency_negative_symbol = '-';
    $this->currency_positive_symbol = '';
    $this->currency_symbol = '';
    $this->currency_positive_format = '';
    $this->currency_negative_format = '';

    $this->language_name = '';
    $this->language_comment = '';
    $this->intl_language_name = '';

    $this->short_day_names = array();
    $this->long_day_names = array();
    foreach ($this->week_days as $day)
    {
      $this->short_day_names[$day] = '';
      $this->long_day_names[$day] = '';
    }

    $this->short_month_names = array();
    $this->long_month_names = array();
    foreach ($this->months as $month)
    {
      $this->short_month_names[$month] = '';
      $this->long_month_names[$month] = '';
    }

    $this->short_day_names = array();
    $this->long_day_names = array();
    foreach($this->week_days as $wday)
    {
      $this->short_day_names[$wday] = '';
      $this->long_day_names[$wday] = '';
    }
  }

  /*
    return true if the locale is valid, ie the locale file could be loaded.
  */
  public function isValid()
  {
    return $this->is_valid;
  }

  protected function _initCountrySettings($country_ini)
  {
    $country_ini->assignOption($this->time_format, 'time_format', 'date_time');
    $country_ini->assignOption($this->short_time_format, 'short_time_format', 'date_time');
    $country_ini->assignOption($this->date_format, 'date_format', 'date_time');
    $country_ini->assignOption($this->short_date_format, 'short_date_format', 'date_time');
    $country_ini->assignOption($this->date_time_format, 'date_time_format', 'date_time');
    $country_ini->assignOption($this->short_date_time_format, 'short_date_time_format', 'date_time');

    if ($country_ini->hasOption('is_monday_first', 'date_time'))
      $this->is_monday_first = strtolower($country_ini->getOption('is_monday_first', 'date_time')) == 'yes';

    if ($this->is_monday_first)
      $this->week_days = array(1, 2, 3, 4, 5, 6, 0);
    else
      $this->week_days = array(0, 1, 2, 3, 4, 5, 6);

    $country_ini->assignOption($this->country, 'country', 'regional_settings');
    $country_ini->assignOption($this->country_comment, 'country_comment', 'regional_settings');

    $country_ini->assignOption($this->decimal_symbol, 'decimal_symbol', 'numbers');
    $country_ini->assignOption($this->thousand_separator, 'thousand_separator', 'numbers');
    $country_ini->assignOption($this->fract_digits, 'fract_digits', 'numbers');
    $country_ini->assignOption($this->negative_symbol, 'negative_symbol', 'numbers');
    $country_ini->assignOption($this->positive_symbol, 'positive_symbol', 'numbers');

    $country_ini->assignOption($this->currency_decimal_symbol, 'decimal_symbol', 'currency');
    $country_ini->assignOption($this->currency_name, 'name', 'currency');
    $country_ini->assignOption($this->currency_short_name, 'short_name', 'currency');
    $country_ini->assignOption($this->currency_thousand_separator, 'thousand_separator', 'currency');
    $country_ini->assignOption($this->currency_fract_digits, 'fract_digits', 'currency');
    $country_ini->assignOption($this->currency_negative_symbol, 'negative_symbol', 'currency');
    $country_ini->assignOption($this->currency_positive_symbol, 'positive_symbol', 'currency');
    $country_ini->assignOption($this->currency_symbol, 'symbol', 'currency');
    $country_ini->assignOption($this->currency_positive_format, 'positive_format', 'currency');
    $country_ini->assignOption($this->currency_negative_format, 'negative_format', 'currency');
  }

  protected function _initLanguageSettings($language_ini)
  {
    $language_ini->assignOption($this->language_name, 'language_name', 'regional_settings');
    $language_ini->assignOption($this->intl_language_name, 'international_language_name', 'regional_settings');
    $language_ini->assignOption($this->language_comment, 'language_comment', 'regional_settings');
    $language_ini->assignOption($this->language_direction, 'language_direction', 'regional_settings');
    $language_ini->assignOption($this->LC_ALL, 'LC_ALL', 'regional_settings');

    $language_ini->assignOption($this->http_locale_code, 'content_language', 'http');

    if ($this->override_charset == '')
    {
      $charset = false;
      if ($language_ini->hasOption('preferred', 'charset'))
      {
        $charset = $language_ini->getOption('preferred', 'charset');
        if ($charset != '')
          $this->charset = $charset;
      }
    }

    if (!is_array($this->short_day_names))
      $this->short_day_names = array();
    if (!is_array($this->long_day_names))
      $this->long_day_names = array();

    foreach ($this->week_days as $day)
    {
      if ($language_ini->hasOption($day, 'short_day_names'))
        $this->short_day_names[$day] = $language_ini->getOption($day, 'short_day_names');
      if ($language_ini->hasOption($day, 'long_day_names'))
        $this->long_day_names[$day] = $language_ini->getOption($day, 'long_day_names');
    }

    if (!is_array($this->short_month_names))
      $this->short_month_names = array();
    if (!is_array($this->long_month_names))
      $this->long_month_names = array();

    foreach ($this->months as $month)
    {
      if ($language_ini->hasOption($month, 'short_month_names'))
        $this->short_month_names[$month] = $language_ini->getOption($month, 'short_month_names');
      if ($language_ini->hasOption($month, 'long_month_names'))
        $this->long_month_names[$month] = $language_ini->getOption($month, 'long_month_names');
    }

    if (!is_array($this->short_day_names))
      $this->short_day_names = array();
    if (!is_array($this->long_day_names))
      $this->long_day_names = array();

    foreach($this->week_days as $wday)
    {
      if ($language_ini->hasOption($wday, 'short_day_names'))
        $this->short_day_names[$wday] = $language_ini->getOption($wday, 'short_day_names');
      if ($language_ini->hasOption($wday, 'long_day_names'))
        $this->long_day_names[$wday] = $language_ini->getOption($wday, 'long_day_names');
    }
  }

  /*
   return a regexp which can be used for locale matching.
   The following groups are defiend
   - 1 - The language identifier
   - 2 - The separator and the country (3)
   - 3 - The country identifier
   - 4 - The separator and the charset (5)
   - 5 - The charset
   - 6 - The separator and the variation (7)
   - 7 - The variation
  */
  static public function localRegexp()
  {
    return "([a-zA-Z]+)([_-]([a-zA-Z]+))?(\.([a-zA-Z-]+))?(@([a-zA-Z0-9]+))?";
  }

  /*
   Decodes a locale string into language, country and charset and returns an array with the information.
   country and charset is optional, country is specified with a - or _ followed by the country code (NO, GB),
   charset is specified with a . followed by the charset name.
   Examples of locale strings are: nor-NO, en_GB.utf8, nn_NO
  */
  static public function getLocaleInformation($locale_string)
  {
    $info = null;
    if (preg_match('/^' . self :: localRegexp() . '/', $locale_string, $regs))
    {
      $info = array();
      $language = strtolower($regs[1]);
      $country = '';
      if (isset($regs[3]))
        $country = strtoupper($regs[3]);
      $charset = '';
      if (isset($regs[5]))
        $charset = strtolower($regs[5]);
      $country_variation = '';
      if (isset($regs[7]))
        $country_variation = strtolower($regs[7]);
      $locale = $language;
      if ($country !== '')
        $locale .= '-' . $country;
      $info['language'] = $language;
      $info['country'] = $country;
      $info['country-variation'] = $country_variation;
      $info['charset'] = $charset;
      $info['locale'] = $locale;
    }
    else
    {
      $info = array();
      $locale = strtolower($locale_string);
      $language = $locale;
      $info['language'] = $language;
      $info['country'] = '';
      $info['country-variation'] = '';
      $info['charset'] = '';
      $info['locale'] = $locale;
    }
    return $info;
  }

  public function setlocale()
  {
    setlocale(LC_ALL, $this->LC_ALL);
  }

  /*
   Returns the charset for this locale.
   note It returns an empty string if no charset was set from the locale file.
  */
  public function getCharset()
  {
    return $this->charset;
  }

  public function getLanguageDirection()
  {
    return $this->language_direction;
  }

  public function getCountryName()
  {
    return $this->country;
  }

  public function getCountryComment()
  {
    return $this->country_comment;
  }

  public function getCountryCode()
  {
    return $this->country_code;
  }

  public function getCountryVariation()
  {
    return $this->country_variation;
  }

  public function getLanguageCode()
  {
    return $this->language_code;
  }

  public function getLanguageComment()
  {
    return $this->language_comment;
  }

  /*
   Returns the locale code for this language which is the language and the country with a dash (-) between them,
   for instance nor-NO or eng-GB.
  */
  public function getLocaleCode()
  {
    return $this->locale_code;
  }

  /*
   Same as locale_code() but appends the country variation if it is set.
  */
  public function getLocaleFullCode()
  {
    $locale = $this->locale_code;
    $variation = $this->countryVariation();
    if ($variation)
      $locale .= '@' . $variation;
    return $locale;
  }

  /*
   return the locale code which can be set in either http headers or the HTML file.
   The locale code is first check for in the regional_settings/http_locale setting in site.ini,
   if that is empty it will use the value from locale_code().
  */
  public function getHttpLocaleCode()
  {
    if ($this->http_locale_code != '')
      $locale_code = $this->http_locale_code;

    if ($locale_code == '')
      $locale_code = $this->getLocaleCode();

    return $locale_code;
  }

  static public function getCurrentLocaleCode()
  {
    return $this->localeCode();
  }

  public function getLanguageName()
  {
    return $this->language_name;
  }

  public function getIntlLanguageName()
  {
    return $this->intl_language_name;
  }

  public function getCurrencySymbol()
  {
    return $this->currency_symbol;
  }

  public function getCurrencyName()
  {
    return $this->currency_name;
  }

  public function getCurrencyShortName()
  {
    return $this->currency_short_name;
  }

  public function getTimeFormat()
  {
    return $this->time_format;
  }

  public function getShortTimeFormat()
  {
    return $this->short_time_format;
  }

  public function getDateFormat()
  {
    return $this->date_format;
  }

  public function getShortDateFormat()
  {
    return $this->short_date_format;
  }

  public function getShortDateTimeFormat()
  {
    return $this->short_date_time_format;
  }

  public function getDateTimeFormat()
  {
    return $this->date_time_format;
  }

  public function isMondayFirst()
  {
    return $this->is_monday_first;
  }

  public function getAvailableLocalesData()
  {
    $available_locales = Limb :: toolkit()->getINI('common.ini')->getOption('codes', 'Locales');

    $locales_data = array();

    foreach($available_locales as $locale_id)
    {
      $locale_data = self :: instance($locale_id);
      $locales_data[$locale_id] = $locale_data->getLanguageName() ? $locale_data->getLanguageName() : $locale_id;
    }

    return $locales_data;
  }

  public function isValidLocaleId($locale_id)
  {
    if(!$available_locales = Limb :: toolkit()->getINI('common.ini')->getOption('codes', 'Locales'))
      return false;

    return in_array($locale_id, $available_locales);
  }

  /*
   Returns an array with the days of the week according to locale information.
   Each entry in the array can be supplied to the short_day_name() and long_day_name() functions.
  */
  public function getWeekDays()
  {
    return $this->week_days;
  }

  public function getMonths()
  {
    return $this->months;
  }

  /*
   Returns the same array as in week_days() but with all days translated to text.
  */
  public function getWeekDayNames($short = false)
  {
    if ($short)
      return $this->short_day_names;
    else
      return $this->long_day_names;
  }

  public function getMonthNames($short = false)
  {
    if ($short)
      return $this->short_month_names;
    else
      return $this->long_month_names;
  }

  /*
   Returns the name for the meridiem ie am (ante meridiem) or pm (post meridiem).
  */
  public function getMeridiemName($hour, $upcase = false)
  {
    $name = ($hour < 12) ? $this->am_name : $this->pm_name;
    return ($upcase) ? strtoupper($name) : $name;
  }

  public function getPmName()
  {
    return $this->pm_name;
  }

  public function getAmName()
  {
    return $this->am_name;
  }

  public function getDayName($num, $short = false)
  {
    if ($num >= 0 &&  $num <= 6)
    {
      if ($short)
        $name = $this->short_day_names[$num];
      else
        $name = $this->long_day_names[$num];
    }
    else
      $name = null;

    return $name;
  }

  /*
   Returns the short name of the month number $num.
  */
  public function getMonthName($num, $short = false)
  {
    if ($num >= 1 &&  $num <= 12)
    {
      if ($short)
        $name = $this->short_month_names[$num];
      else
        $name = $this->long_month_names[$num];
    }
    else
      $name = null;

    return $name;
  }

  protected function _getIni($with_variation = false, $directory = '')
  {
    $type = $with_variation ? 'variation' : 'default';
    $country = $this->getCountryCode();
    $country_variation = $this->getCountryVariation();
    $language = $this->getLanguageCode();
    $locale = $language;

    if ($country !== '')
      $locale .= '-' . $country;
    if ($with_variation)
    {
      if ($country_variation !== '')
        $locale .= '@' . $country_variation;
    }
    $file_name = $locale . '.ini';

    if (file_exists($directory . '/' . $file_name))
      return new Ini($directory . '/' . $file_name);
    else
      return null;
  }

  /*
   Returns the ini object for the locale ini file.
   warning Do not modify this object.
  */
  public function getLocaleIni($with_variation = false)
  {
    $type = $with_variation ? 'variation' : 'default';
    if (get_class($this->locale_ini[$type]) != 'ini')
      $this->locale_ini[$type] = $this->_getIni($with_variation, LOCALE_DIR);

    return $this->locale_ini[$type];
  }

  /*
   Returns the ini object for the country ini file.
   warning Do not modify this object.
  */
  public function getCountryIni($with_variation = false)
  {
    $type = $with_variation ? 'variation' : 'default';
    if (get_class($this->country_ini[$type]) != 'ini')
      $this->country_ini[$type] = $this->_getIni($with_variation, LOCALE_DIR . 'country/');

    return $this->country_ini[$type];
  }

  /*
   Returns the ini object for the language ini file.
   warning Do not modify this object.
  */
  public function getLanguageIni($with_variation = false)
  {
    $type = $with_variation ? 'variation' : 'default';
    if (get_class($this->language_ini[$type]) != 'ini')
      $this->language_ini[$type] = $this->_getIni($with_variation, LOCALE_DIR . 'language/');

    return $this->language_ini[$type];
  }

  /*
   Returns an unique instance of the locale class for a given locale string. If $locale_string is not
   specified the default local string in site.ini is used.
   Use this instead of newing locale to benefit from speed and unified access.
  */
  static public function instance($locale_id = '')
  {
    if (!$locale_id &&  defined('CONTENT_LOCALE_ID'))
      $locale_id = CONTENT_LOCALE_ID;
    elseif (!$locale_id &&  !defined('CONTENT_LOCALE_ID'))
      $locale_id = DEFAULT_CONTENT_LOCALE_ID;

    if (isset($GLOBALS['global_locale_' . $locale_id]))
    {
      return $GLOBALS['global_locale_' . $locale_id];
    }

    $obj = new Locale($locale_id);
    $GLOBALS['global_locale_' . $locale_id] = $obj;

    return $obj;
  }
}
?>