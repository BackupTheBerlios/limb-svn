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

require_once(LIMB_DIR . '/core/util/Ini.class.php');

@define('LOCALE_DIR', LIMB_DIR . '/core/i18n/locale');

class Locale
{
  var $is_valid = false;

  var $date_format = ''; // format of dates
  var $short_date_format = ''; // format of short dates
  var $time_format = ''; // format of times
  var $date_time_format = '';
  var $short_date_time_format = '';
  var $short_time_format = ''; // format of short times
  var $is_monday_first = false; // true if monday is the first day of the week
  var $am_name = 'am';
  var $pm_name = 'pm';
  var $charset = '';
  var $override_charset = '';
  var $locale_code = '';
  var $http_locale_code = '';
  var $LC_ALL = '';
  // numbers
  var $decimal_symbol = '';
  var $thousand_separator = '';
  var $fract_digits = '';
  var $negative_symbol = '';
  var $positive_symbol = '';
  // currency
  var $currency_name = '';
  var $currency_short_name = '';
  var $currency_decimal_symbol = '';
  var $currency_thousand_separator = '';
  var $currency_fract_digits = '';
  var $currency_negative_symbol = '';
  var $currency_positive_symbol = '';
  var $currency_symbol = '';
  var $currency_positive_format = '';
  var $currency_negative_format = '';
  // help arrays
  var $short_month_names = array();
  var $long_month_names = array();
  var $short_day_names = array();
  var $long_day_names = array();
  var $week_days = array();
  var $months = array();

  var $country = '';
  var $country_code = '';
  var $country_variation = '';
  var $country_comment = '';
  var $language_comment = '';
  // Objects
  var $locale_ini = array('default' => null, 'variation' => null);
  var $country_ini = array('default' => null, 'variation' => null);
  var $language_ini = array('default' => null, 'variation' => null);

  var $language_code = ''; // the language code, for instance nor-NO, or eng-GB
  var $language_name = ''; // name of the language
  var $intl_language_name = ''; // internationalized name of the language
  var $language_direction = 'ltr';

  /*
   Initializes the locale with the locale string $locale_string.
   All locale data is read from locale $locale_string.ini
  */
  function locale($locale_string = '')
  {
    $this->http_locale_code = '';

    $this->week_days = array(0, 1, 2, 3, 4, 5, 6);
    $this->months = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);

    $locale = Locale :: getLocaleInformation($locale_string);

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
      return throw(new LimbException('Could not load country settings', array('country_code' => $this->country_code)));
    }

    if ($language_ini !== null)
      $this->_initLanguageSettings($language_ini);
    else
    {
      $this->is_valid = false;
      return throw(new LimbException('Could not load language settings', array('language_code' => $this->language_code)));
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

  function _reset()
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
  function isValid()
  {
    return $this->is_valid;
  }

  function _initCountrySettings($country_ini)
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

  function _initLanguageSettings($language_ini)
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
  function localRegexp()
  {
    return "([a-zA-Z]+)([_-]([a-zA-Z]+))?(\.([a-zA-Z-]+))?(@([a-zA-Z0-9]+))?";
  }

  /*
   Decodes a locale string into language, country and charset and returns an array with the information.
   country and charset is optional, country is specified with a - or _ followed by the country code (NO, GB),
   charset is specified with a . followed by the charset name.
   Examples of locale strings are: nor-NO, en_GB.utf8, nn_NO
  */
  function getLocaleInformation($locale_string)
  {
    $info = null;
    if (preg_match('/^' . Locale :: localRegexp() . '/', $locale_string, $regs))
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

  function setlocale()
  {
    setlocale(LC_ALL, $this->LC_ALL);
  }

  /*
   Returns the charset for this locale.
   note It returns an empty string if no charset was set from the locale file.
  */
  function getCharset()
  {
    return $this->charset;
  }

  function getLanguageDirection()
  {
    return $this->language_direction;
  }

  function getCountryName()
  {
    return $this->country;
  }

  function getCountryComment()
  {
    return $this->country_comment;
  }

  function getCountryCode()
  {
    return $this->country_code;
  }

  function getCountryVariation()
  {
    return $this->country_variation;
  }

  function getLanguageCode()
  {
    return $this->language_code;
  }

  function getLanguageComment()
  {
    return $this->language_comment;
  }

  /*
   Returns the locale code for this language which is the language and the country with a dash (-) between them,
   for instance nor-NO or eng-GB.
  */
  function getLocaleCode()
  {
    return $this->locale_code;
  }

  /*
   Same as locale_code() but appends the country variation if it is set.
  */
  function getLocaleFullCode()
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
  function getHttpLocaleCode()
  {
    if ($this->http_locale_code != '')
      $locale_code = $this->http_locale_code;

    if ($locale_code == '')
      $locale_code = $this->getLocaleCode();

    return $locale_code;
  }

  function getCurrentLocaleCode()
  {
    return $this->localeCode();
  }

  function getLanguageName()
  {
    return $this->language_name;
  }

  function getIntlLanguageName()
  {
    return $this->intl_language_name;
  }

  function getCurrencySymbol()
  {
    return $this->currency_symbol;
  }

  function getCurrencyName()
  {
    return $this->currency_name;
  }

  function getCurrencyShortName()
  {
    return $this->currency_short_name;
  }

  function getTimeFormat()
  {
    return $this->time_format;
  }

  function getShortTimeFormat()
  {
    return $this->short_time_format;
  }

  function getDateFormat()
  {
    return $this->date_format;
  }

  function getShortDateFormat()
  {
    return $this->short_date_format;
  }

  function getShortDateTimeFormat()
  {
    return $this->short_date_time_format;
  }

  function getDateTimeFormat()
  {
    return $this->date_time_format;
  }

  function isMondayFirst()
  {
    return $this->is_monday_first;
  }

  function getAvailableLocalesData()
  {
    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('common.ini');
    $available_locales = $ini->getOption('codes', 'Locales');

    $locales_data = array();

    foreach($available_locales as $locale_id)
    {
      $locale_data = Locale :: instance($locale_id);
      $locales_data[$locale_id] = $locale_data->getLanguageName() ? $locale_data->getLanguageName() : $locale_id;
    }

    return $locales_data;
  }

  function isValidLocaleId($locale_id)
  {
    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('common.ini');
    if(!$available_locales = $ini->getOption('codes', 'Locales'))
      return false;

    return in_array($locale_id, $available_locales);
  }

  /*
   Returns an array with the days of the week according to locale information.
   Each entry in the array can be supplied to the short_day_name() and long_day_name() functions.
  */
  function getWeekDays()
  {
    return $this->week_days;
  }

  function getMonths()
  {
    return $this->months;
  }

  /*
   Returns the same array as in week_days() but with all days translated to text.
  */
  function getWeekDayNames($short = false)
  {
    if ($short)
      return $this->short_day_names;
    else
      return $this->long_day_names;
  }

  function getMonthNames($short = false)
  {
    if ($short)
      return $this->short_month_names;
    else
      return $this->long_month_names;
  }

  /*
   Returns the name for the meridiem ie am (ante meridiem) or pm (post meridiem).
  */
  function getMeridiemName($hour, $upcase = false)
  {
    $name = ($hour < 12) ? $this->am_name : $this->pm_name;
    return ($upcase) ? strtoupper($name) : $name;
  }

  function getPmName()
  {
    return $this->pm_name;
  }

  function getAmName()
  {
    return $this->am_name;
  }

  function getDayName($num, $short = false)
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
  function getMonthName($num, $short = false)
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

  function _getIni($with_variation = false, $directory = '')
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
  function getLocaleIni($with_variation = false)
  {
    $type = $with_variation ? 'variation' : 'default';
    if (!is_a($this->locale_ini[$type], 'Ini'))
      $this->locale_ini[$type] = $this->_getIni($with_variation, LOCALE_DIR);

    return $this->locale_ini[$type];
  }

  /*
   Returns the ini object for the country ini file.
   warning Do not modify this object.
  */
  function getCountryIni($with_variation = false)
  {
    $type = $with_variation ? 'variation' : 'default';
    if (!is_a($this->country_ini[$type], 'Ini'))
      $this->country_ini[$type] = $this->_getIni($with_variation, LOCALE_DIR . 'country/');

    return $this->country_ini[$type];
  }

  /*
   Returns the ini object for the language ini file.
   warning Do not modify this object.
  */
  function getLanguageIni($with_variation = false)
  {
    $type = $with_variation ? 'variation' : 'default';
    if (!is_a($this->language_ini[$type], 'Ini'))
      $this->language_ini[$type] = $this->_getIni($with_variation, LOCALE_DIR . 'language/');

    return $this->language_ini[$type];
  }

  /*
   Returns an unique instance of the locale class for a given locale string. If $locale_string is not
   specified the default local string in site.ini is used.
   Use this instead of newing locale to benefit from speed and unified access.
  */
  function & instance($locale_id = '')
  {
    if (!$locale_id &&  defined('CONTENT_LOCALE_ID'))
      $locale_id = CONTENT_LOCALE_ID;
    elseif (!$locale_id &&  !defined('CONTENT_LOCALE_ID'))
      $locale_id = DEFAULT_CONTENT_LOCALE_ID;

    if (isset($GLOBALS['global_locale_' . $locale_id]))
    {
      return $GLOBALS['global_locale_' . $locale_id];
    }

    $obj =& new Locale($locale_id);
    $GLOBALS['global_locale_' . $locale_id] = $obj;

    return $obj;
  }
}
?>