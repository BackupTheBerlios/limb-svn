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

/*
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

require_once(LIMB_DIR . 'core/lib/util/ini.class.php');

define( 'LOCALE_DEBUG_INTERNALS', false );

define('LOCALE_DIR', LIMB_DIR . '/core/locale');

class locale
{
  var $is_valid = false;

  var $date_format = ''; 				// format of dates
  var $short_date_format = ''; 	// format of short dates
  var $time_format = '';				// format of times
  var $date_time_format = '';
  var $short_date_time_format = '';
  var $short_time_format = ''; 	// format of short times
  var $is_monday_first = false; // true if monday is the first day of the week
  var $am_name = 'am'; 
  var $pm_name = 'pm';
  var $charset = '';
  var $override_charset = '';
  var $locale_code = '';
  var $http_locale_code = '';
  
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
  var $week_days  = array();
  var $months = array();

  var $country = '';
  var $country_code = '';
  var $country_variation = '';
  var $country_comment = '';
  var $language_comment = '';
  
  // Objects
  var $locale_ini = array( 'default' => null, 'variation' => null );
  var $country_ini = array( 'default' => null, 'variation' => null );
  var $language_ini = array( 'default' => null, 'variation' => null );
  
  var $language_code = '';			// the language code, for instance nor-NO, or eng-GB
  var $language_name = '';			// name of the language
  var $intl_language_name = '';	// internationalized name of the language
  var $language_direction = 'ltr';
  
  /*
   Initializes the locale with the locale string $locale_string.
   All locale data is read from locale $locale_string.ini
  */
  function locale( $locale_string = '')
  {  	
    $this->http_locale_code = '';

    $this->week_days = array( 0, 1, 2, 3, 4, 5, 6 );
    $this->months = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 );

    $locale = locale :: get_locale_information($locale_string);

    $this->country_code =& $locale['country'];
    $this->country_variation =& $locale['country-variation'];
    $this->language_code =& $locale['language'];
    $this->locale_code =& $locale['locale'];
    $this->charset = $locale['charset'];
    $this->override_charset = $locale['charset'];

    // Figure out if we use one locale file or separate country/language file.
    $locale_ini =& $this->get_locale_ini();
    $country_ini =& $locale_ini;
    $language_ini =& $locale_ini;
    
    if ($locale_ini === null)
    {
      $country_ini =& $this->get_country_ini();
      $language_ini =& $this->get_language_ini();
    }

    $this->_reset();

    $this->is_valid = true;

    if ( $country_ini !== null )
    	$this->_init_country_settings( $country_ini );
    else
    {
      $this->is_valid = false;
      debug :: write_error( 'Could not load country settings for ' . $this->country_code, __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__ );
    }

    if ( $language_ini !== null )
    	$this->_init_language_settings( $language_ini );
    else
    {
    	$this->is_valid = false;
      debug::write_error( 'Could not load language settings for ' . $this->language_code, __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__ );
    }
    
    // load variation if any
    $locale_variation_ini =& $this->get_locale_ini( true );    
    $country_variation_ini =& $locale_variation_ini;
    $language_variation_ini =& $locale_variation_ini;
    if ($locale_variation_ini === null)
    {
      $country_variation_ini =& $this->get_country_ini( true );
      $language_variation_ini =& $this->get_language_ini( true );
    }

    if ($country_variation_ini !== null &&
    		$country_variation_ini->file_name() != $country_ini->file_name())
    	$this->_init_country_settings($country_variation_ini);

    if ($language_variation_ini !== null &&
    		$language_variation_ini->file_name() != $language_ini->file_name())
    	$this->_init_language_settings($language_variation_ini);
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
    foreach ( $this->week_days as $day )
    {
      $this->short_day_names[$day] = '';
      $this->long_day_names[$day] = '';
    }

    $this->short_month_names = array();
    $this->long_month_names = array();
    foreach ( $this->months as $month )
    {
      $this->short_month_names[$month] = '';
      $this->long_month_names[$month] = '';
    }

    $this->short_day_names = array();
    $this->long_day_names = array();
    foreach( $this->week_days as $wday )
    {
      $this->short_day_names[$wday] = '';
      $this->long_day_names[$wday] = '';
    }
  }

  /*
  	return true if the locale is valid, ie the locale file could be loaded.
  */
  function is_valid()
  {
  	return $this->is_valid;
  }

  function _init_country_settings( &$country_ini )
  {
    $country_ini->assign( 'date_time', 'time_format', $this->time_format );
    $country_ini->assign( 'date_time', 'short_time_format', $this->short_time_format );
    $country_ini->assign( 'date_time', 'date_format', $this->date_format );
    $country_ini->assign( 'date_time', 'short_date_format', $this->short_date_format );
    $country_ini->assign( 'date_time', 'date_time_format', $this->date_time_format );
    $country_ini->assign( 'date_time', 'short_date_time_format', $this->short_date_time_format );

    if ( $country_ini->has_variable( 'date_time', 'is_monday_first' ) )
    	$this->is_monday_first = strtolower( $country_ini->variable( 'date_time', 'is_monday_first' ) ) == 'yes';
    	
    if ($this->is_monday_first)
        $this->week_days = array( 1, 2, 3, 4, 5, 6, 0 );
    else
        $this->week_days = array( 0, 1, 2, 3, 4, 5, 6 );

    $country_ini->assign( 'regional_settings', 'country', $this->country );
    $country_ini->assign( 'regional_settings', 'country_comment', $this->country_comment );

    $country_ini->assign( 'numbers', 'decimal_symbol', $this->decimal_symbol );
    $country_ini->assign( 'numbers', 'thousand_separator', $this->thousand_separator );
    $country_ini->assign( 'numbers', 'fract_digits', $this->fract_digits );
    $country_ini->assign( 'numbers', 'negative_symbol', $this->negative_symbol );
    $country_ini->assign( 'numbers', 'positive_symbol', $this->positive_symbol );

    $country_ini->assign( 'currency', 'decimal_symbol', $this->currency_decimal_symbol );
    $country_ini->assign( 'currency', 'name', $this->currency_name );
    $country_ini->assign( 'currency', 'short_name', $this->currency_short_name );
    $country_ini->assign( 'currency', 'thousand_separator', $this->currency_thousand_separator );
    $country_ini->assign( 'currency', 'fract_digits', $this->currency_fract_digits );
    $country_ini->assign( 'currency', 'negative_symbol', $this->currency_negative_symbol );
    $country_ini->assign( 'currency', 'positive_symbol', $this->currency_positive_symbol );
    $country_ini->assign( 'currency', 'symbol', $this->currency_symbol );
    $country_ini->assign( 'currency', 'positive_format', $this->currency_positive_format );
    $country_ini->assign( 'currency', 'negative_format', $this->currency_negative_format );
  }

  function _init_language_settings( &$language_ini )
  {
    $language_ini->assign( 'regional_settings', 'language_name', $this->language_name );
    $language_ini->assign( 'regional_settings', 'international_language_name', $this->intl_language_name );
    $language_ini->assign( 'regional_settings', 'language_comment', $this->language_comment );
    
    if ($language_ini->has_variable('regional_settings', 'language_direction'))
    	$language_ini->assign( 'regional_settings', 'language_direction', $this->language_direction );

    $language_ini->assign( 'http', 'content_language', $this->http_locale_code );
    if ( $this->override_charset == '' )
    {
      $charset = false;
      if ( $language_ini->has_variable('charset', 'preferred'))
      {
        $charset = $language_ini->variable('charset', 'preferred');
        if ( $charset != '' )
        	$this->charset = $charset;
      }
    }

    if ( !is_array( $this->short_day_names ) )
    	$this->short_day_names = array();
    if ( !is_array( $this->long_day_names ) )
    	$this->long_day_names = array();
    	
    foreach ( $this->week_days as $day )
    {
      if ( $language_ini->has_variable( 'short_day_names', $day ) )
      	$this->short_day_names[$day] = $language_ini->variable( 'short_day_names', $day );
      if ( $language_ini->has_variable( 'long_day_names', $day ) )
      	$this->long_day_names[$day] = $language_ini->variable( 'long_day_names', $day );
    }

    if ( !is_array( $this->short_month_names ) )
    	$this->short_month_names = array();
    if ( !is_array( $this->long_month_names ) )
    	$this->long_month_names = array();
    
    foreach ( $this->months as $month )
    {
      if ( $language_ini->has_variable( 'short_month_names', $month ) )
      	$this->short_month_names[$month] = $language_ini->variable( 'short_month_names', $month );
      if ( $language_ini->has_variable( 'long_month_names', $month ) )
      	$this->long_month_names[$month] = $language_ini->variable( 'long_month_names', $month );
    }

    if ( !is_array( $this->short_day_names ) )
    	$this->short_day_names = array();
    if ( !is_array( $this->long_day_names ) )
    	$this->long_day_names = array();
    	
    foreach( $this->week_days as $wday )
    {
      if ( $language_ini->has_variable( 'short_day_names', $wday ) )
      	$this->short_day_names[$wday] = $language_ini->variable( 'short_day_names', $wday );
      if ( $language_ini->has_variable( 'long_day_names', $wday ) )
      	$this->long_day_names[$wday] = $language_ini->variable( 'long_day_names', $wday );
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
  function local_regexp()
  {
  	return "([a-zA-Z]+)([_-]([a-zA-Z]+))?(\.([a-zA-Z-]+))?(@([a-zA-Z0-9]+))?";
  }

  /*
   Decodes a locale string into language, country and charset and returns an array with the information.
   country and charset is optional, country is specified with a - or _ followed by the country code (NO, GB),
   charset is specified with a . followed by the charset name.
   Examples of locale strings are: nor-NO, en_GB.utf8, nn_NO
  */
  function get_locale_information( $locale_string )
  {
    $info = null;
    if ( preg_match( '/^' . locale :: local_regexp(). '/', $locale_string, $regs ))
    {
      $info = array();
      $language = strtolower( $regs[1] );
      $country = '';
      if ( isset( $regs[3] ) )
          $country = strtoupper( $regs[3] );
      $charset = '';
      if ( isset( $regs[5] ) )
          $charset = strtolower( $regs[5] );
      $country_variation = '';
      if ( isset( $regs[7] ) )
          $country_variation = strtolower( $regs[7] );
      $locale = $language;
      if ( $country !== '' )
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
	    $locale = strtolower( $locale_string );
	    $language = $locale;
	    $info['language'] = $language;
	    $info['country'] = '';
	    $info['country-variation'] = '';
	    $info['charset'] = '';
	    $info['locale'] = $locale;
    }
    return $info;
  }

  /*
   Sets locale information in PHP. This means that some of the string/sort functions in
   PHP will work with non-latin1 characters.
   Make sure setlanguage is called before this.
  */
  function init_php( $charset = false )
  {
      if ( $charset === false )
          $charset = $this->language->code();
      set_locale( LC_ALL, $charset );
  }

  /*
   Returns the charset for this locale.
   note It returns an empty string if no charset was set from the locale file.
  */
  function get_charset()
  {
   	return $this->charset;
  }
  
  function get_language_direction()
  {
  	return $this->language_direction;
  }

  function get_country_name()
  {
   	return $this->country;
  }

  function get_country_comment()
  {
  	return $this->country_comment;
  }

  function get_country_code()
  {
  	return $this->country_code;
  }

  function get_country_variation()
  {
  	return $this->country_variation;
  }

  function get_language_code()
  {
  	return $this->language_code;
  }

  function get_language_comment()
  {
  	return $this->language_comment;
  }

  /*
   Returns the locale code for this language which is the language and the country with a dash (-) between them,
   for instance nor-NO or eng-GB.
  */
  function get_locale_code()
  {
  	return $this->locale_code;
  }

  /*
   Same as locale_code() but appends the country variation if it is set.
  */
  function get_locale_full_code()
  {
    $locale = $this->locale_code;
    $variation = $this->country_variation();
    if ( $variation )
    	$locale .= '@' . $variation;
    return $locale;
  }

  /*
   return the locale code which can be set in either http headers or the HTML file.
   The locale code is first check for in the regional_settings/http_locale setting in site.ini,
   if that is empty it will use the value from locale_code().
  */
  function get_http_locale_code()
  {    	
    if ( $this->http_locale_code != '' )
   		$locale_code = $this->http_locale_code;
   		
    if ( $locale_code == '' )
    	$locale_code = $this->get_locale_code();
    	
    return $locale_code;
  }

  /*
   static
   Returns the current locale code for this language which is the language and the country with a dash (-) between them,
   for instance nor-NO or eng-GB.
  */
  function get_current_locale_code()
  {
    $locale =& locale :: instance();
    return $locale->locale_code();
  }

  function get_language_name()
  {
  	return $this->language_name;
  }

  function get_intl_language_name()
  {
  	return $this->intl_language_name;
  }

  function get_currency_symbol()
  {
  	return $this->currency_symbol;
  }

  function get_currency_name()
  {
  	return $this->currency_name;
  }

  function get_currency_short_name()
  {
  	return $this->currency_short_name;
  }

  function get_time_format()
  {
  	return $this->time_format;
  }

  function get_short_time_format()
  {
  	return $this->short_time_format;
  }

  function get_date_format()
  {
  	return $this->date_format;
  }
  
  function get_short_date_format()
  {
  	return $this->short_date_format;
  }

  function get_short_date_time_format()
  {
  	return $this->short_date_time_format;
  }

  function get_date_time_format()
  {
  	return $this->date_time_format;
  }

  function is_monday_first()
  {
  	return $this->is_monday_first;
  }

  /*
   Returns an array with the days of the week according to locale information.
   Each entry in the array can be supplied to the short_day_name() and long_day_name() functions.
  */
  function get_week_days()
  {
  	return $this->week_days;
  }

  function get_months()
  {
  	return $this->months;
  }

  /*
   Returns the same array as in week_days() but with all days translated to text.
  */
  function get_week_day_names($short = false)
  {
    if ($short)
    	return $this->short_day_names;
    else
    	return $this->long_day_names;
  }
  
  function get_month_names($short = false)
  {
    if ($short)
    	return $this->short_month_names;
    else
    	return $this->long_month_names;
  }
           
  /*
   Returns the name for the meridiem ie am (ante meridiem) or pm (post meridiem).
  */
  function get_meridiem_name($hour, $upcase = false)
  {
    $name = ($hour < 12) ? $this->am_name : $this->pm_name;
    return ($upcase) ? strtoupper( $name ) : $name;
  }
  
  function get_pm_name()
  {
  	return $this->pm_name;
  }
  
  function get_am_name()
  {
  	return $this->am_name;
  }
  
  function get_day_name($num, $short=false)
  {
    if ( $num >= 0 && $num <= 6 )
    {      
      if($short)
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
   This functions is usually used together with months().
  */
  function get_month_name($num, $short=false)
  {
    if ( $num >= 1 && $num <= 12 )
    {
      if($short)
      	$name = $this->short_month_names[$num];
      else
      	$name = $this->long_month_names[$num];
    }
    else
      $name = null;
      
    return $name;
  }

  function &_get_ini( $with_variation = false, $directory = '')
  {
    $type = $with_variation ? 'variation' : 'default';
    $country = $this->get_country_code();
    $country_variation = $this->get_country_variation();
    $language = $this->get_language_code();
    $locale = $language;
      
    if ( $country !== '' )
    	$locale .= '-' . $country;
    if ( $with_variation )
    {
      if ( $country_variation !== '' )
      	$locale .= '@' . $country_variation;
    }
    $file_name = $locale . '.ini';
      
    if ( locale :: is_debug_enabled() )
    	debug :: write_notice( "Requesting $file_name", 'locale :: _get_locale_ini' );
    	
    if ( ini :: exists( $file_name, $directory ) )
    	return ini :: instance( $file_name, $directory );
    else
    	return null;
  }

  /*
   Returns the ini object for the locale ini file.
   warning Do not modify this object.
  */
  function &get_locale_ini( $with_variation = false)
  {
    $type = $with_variation ? 'variation' : 'default';
    if ( get_class( $this->locale_ini[$type] ) != 'ini' )
    	$this->locale_ini[$type] = locale :: _get_ini($with_variation, LOCALE_DIR);
    	
    return $this->locale_ini[$type];
  }

  /*
   Returns the ini object for the country ini file.
   warning Do not modify this object.
  */
  function &get_country_ini( $with_variation = false )
  {
    $type = $with_variation ? 'variation' : 'default';
    if ( get_class( $this->country_ini[$type] ) != 'ini' )
    	$this->country_ini[$type] = locale :: _get_ini($with_variation, LOCALE_DIR . 'country/');
    	
    return $this->country_ini[$type];
  }

  /*
   Returns the ini object for the language ini file.
   warning Do not modify this object.
  */
  function &get_language_ini( $with_variation = false )
  {
    $type = $with_variation ? 'variation' : 'default';
    if ( get_class( $this->language_ini[$type] ) != 'ini' )
    	$this->language_ini[$type] = locale :: _get_ini($with_variation, LOCALE_DIR . 'language/');
    	
    return $this->language_ini[$type];
  }

  /*
   static
   Returns an unique instance of the locale class for a given locale string. If $locale_string is not
   specified the default local string in site.ini is used.
   Use this instead of newing locale to benefit from speed and unified access.
  */
  function &instance($locale_string = '')
  {
  	if(!$locale_string && defined('CONTENT_LOCALE_ID'))
  		$locale_string = CONTENT_LOCALE_ID;
  	elseif(!$locale_string && !defined('CONTENT_LOCALE_ID'))
			$locale_string = DEFAULT_CONTENT_LOCALE_ID;
			
		$obj =& $GLOBALS['global_locale_' . $locale_string];
		
    if ( get_class( $obj ) != 'locale' )
    	$obj =& new locale( $locale_string );
    	
    return $obj;
  }

  /*
   static
   return true if debugging of internals is enabled, this will display
   which files are loaded and when cache files are created.
   Set the option with set_debug_enabled().
  */
  function is_debug_enabled()
  {
    if ( !isset( $GLOBALS['global_locale_debug_internal_enabled'] ) )
    	$GLOBALS['global_locale_debug_internal_enabled'] = LOCALE_DEBUG_INTERNALS;
    return $GLOBALS['global_locale_debug_internal_enabled'];
  }

  /*
   static
   Sets whether internal debugging is enabled or not.
  */
  function set_debug_enabled( $debug )
  {
  	$GLOBALS['global_locale_debug_internal_enabled'] = $debug;
  }
};

?>
