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
require_once(LIMB_DIR . '/core/date/Date.class.php');
require_once(LIMB_DIR . '/core/i18n/Locale.class.php');

class LocaleDateComponent extends Component
{
  var $date = null;

  var $date_type = 'string';

  var $format_string = '';

  var $locale;
  var $date_locale;

  function prepare()
  {
    $this->date = new Date();
  }

  function setFormatString($string)
  {
    $this->format_string = $string;
  }

  function setDateType($type)
  {
    $this->date_type = $type;
  }

  function setLocale($locale)
  {
    $this->locale = $locale;
  }

  function setDateLocale($locale)
  {
    $this->date_locale = $locale;
  }

  function setFormatType($type)
  {
    $toolkit =& Limb :: toolkit();
    $locale =& $toolkit->getLocale($this->locale);

    switch($type)
    {
      case 'time':
        $this->format_string = $locale->getTimeFormat();
      break;

      case 'short_time':
        $this->format_string = $locale->getShortTimeFormat();
      break;

      case 'date':
        $this->format_string = $locale->getDateFormat();
      break;

      case 'short_date':
        $this->format_string = $locale->getShortDateFormat();
      break;

      case 'date_time':
        $this->format_string = $locale->getDateTimeFormat();
      break;

      case 'short_date_time':
        $this->format_string = $locale->getShortDateTimeFormat();
      break;

      default:
        $this->format_string = $locale->getShortDateFormat();
    }
  }

  function setDate($value, $format=DATE_SHORT_FORMAT_ISO)
  {
    switch($this->date_type)
    {
      case 'string':
        $this->date->setByString($value, $format);
      break;

      case 'localized_string':
        $toolkit =& Limb :: toolkit();
        $locale =& $toolkit->getLocale($this->date_locale);
        $this->date->setByLocaleString($locale, $value, $format);
      break;

      case 'stamp':
        $this->date->setByStamp((int)$value);
      break;
    }
  }

  function format()
  {
    $toolkit =& Limb :: toolkit();
    $locale =& $toolkit->getLocale($this->locale);

    if($this->format_string)
      $format_string = $this->format_string;
    else
      $format_string = $locale->getShortDateFormat();

    echo $this->date->format($locale, $format_string);
  }
}

?>