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
require_once(LIMB_DIR . '/class/lib/date/Date.class.php');
require_once(LIMB_DIR . '/class/i18n/Locale.class.php');

class LocaleDateFormatComponent extends Component
{
  var $date = null;

  var $date_type = 'string';

  var $format_string = '';

  var $locale_type = CONTENT_LOCALE_ID;

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

  function setLocaleType($locale_type)
  {
    if ($locale_type == 'management')
      $this->locale_type = MANAGEMENT_LOCALE_ID;
    else
      $this->locale_type = CONTENT_LOCALE_ID;
  }

  function setLocaleFormatType($type)
  {
    $toolkit =& Limb :: toolkit();
    $locale =& $toolkit->getLocale($this->locale_type);

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

  function setDate($date_string, $format=DATE_SHORT_FORMAT_ISO)
  {
    switch($this->date_type)
    {
      case 'string':
        $toolkit =& Limb :: toolkit();
        $locale =& $toolkit->getLocale($this->locale_type);
        $this->date->setByLocaleString($locale, $date_string, $format);
      break;

      case 'stamp':
        $this->date->setByStamp((int)$date_string);
      break;
    }
  }

  function format()
  {
    $toolkit =& Limb :: toolkit();
    $locale =& $toolkit->getLocale($this->locale_type);

    if($this->format_string)
      $format_string = $this->format_string;
    else
      $format_string = $locale->getShortDateFormat();

    echo $this->date->format($locale, $format_string);
  }

}

?>