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
//inspired by PEAR::Date package
require_once(LIMB_DIR . '/class/lib/date/DateTimeZone.class.php');

define('DATE_FORMAT_ISO', "%Y-%m-%d %T"); //YYYY-MM-DD HH:MM:SS
define('DATE_SHORT_FORMAT_ISO', "%Y-%m-%d"); //YYYY-MM-DD

class Date
{
  var $year = 0;
  var $month = 0;
  var $day = 0;
  var $hour = 0;
  var $minute = 0;
  var $second = 0;
  var $tz = 0;

  function Date($date=null, $format=DATE_SHORT_FORMAT_ISO)
  {
    $this->tz = DateTimeZone::getDefault();

    if (is_object($date) &&  (get_class($date) == 'date'))
      $this->copy($date);
    elseif(is_numeric($date))
      $this->setByDays($date);
    elseif(is_string($date))
      $this->setByString($date);
    else
      $this->setByStamp();
  }

  function create($year=0, $month=0, $day=0, $hour=0, $minute=0, $second=0)
  {
    $d = new Date();

    $d->setYear($year);
    $d->setMonth($month);
    $d->setDay($day);
    $d->setHour($hour);
    $d->setMinute($minute);
    $d->setSecond($second);

    return $d;
  }

  function reset()
  {
    $this->year   = 0;
    $this->month  = 0;
    $this->day    = 0;
    $this->hour   = 0;
    $this->minute = 0;
    $this->second = 0;
  }

  function isValid()
  {
    if ($this->year < 0 ||  $this->year > 9999)
      return false;

    return checkdate($this->month, $this->day, $this->year);
  }

  function setByString($string, $format=DATE_SHORT_FORMAT_ISO)
  {
    $this->reset();

    switch ($format)
    {
      case DATE_SHORT_FORMAT_ISO:
        if(ereg('([0-9]{4})-([0-9]{2})-([0-9]{2})', $string, $regs))
        {
          $this->year   = (int)$regs[1];
          $this->month  = (int)$regs[2];
          $this->day    = (int)$regs[3];
        }
        break;
      case DATE_FORMAT_ISO:
        if (ereg('([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})', $string, $regs))
        {
          $this->year   = (int)$regs[1];
          $this->month  = (int)$regs[2];
          $this->day    = (int)$regs[3];
          $this->hour   = (int)$regs[4];
          $this->minute = (int)$regs[5];
          $this->second = (int)$regs[6];
        }
        break;
    }
  }

  function setByLocaleString($locale, $string, $format)
  {
    $this->reset();

    $arr = $this->_parseTimeString($locale, $string, $format);

    $this->year   = $arr['year'];
    $this->month  = $arr['month'];
    $this->day    = $arr['day'];
    $this->hour   = $arr['hour'];
    $this->minute = $arr['minute'];
    $this->second = $arr['second'];
  }

  /*
    Tries to guess time values in time string $time_string formatted with $fmt
    Returns an array('hour','minute','second','month','day','year')
    At this moment only most common tags are supported.
  */
  function _parseTimeString($locale, $time_string, $fmt)
  {
    $hour = 0;
    $minute = 0;
    $second = 0;
    $month = 0;
    $day = 0;
    $year = 0;

    if(!($time_array = $this->_explodeTimeStringByFormat($time_string, $fmt)))
      return -1;

    foreach($time_array as $time_char => $value)
    {
      switch($time_char)
      {
        case '%p':
        case '%P':
          if(strtolower($value) == $locale->getPmName())
            $hour += 12;
        break;

        case '%I':
        case '%H':
          $hour = (int)$value;
        break;

        case '%M':
          $minute = (int)$value;
        break;

        case '%S':
          $second = (int)$value;
        break;

        case '%m':
          $month = (int)$value;
        break;

        case '%b':
        case '%h':
          if(($index = array_search($value, $locale->getMonthNames(true))) !== false)
          {
            if($index !== false)
              $month = $index;
          }
        break;

        case '%B':
          if(($index = array_search($value, $locale->getMonthNames())) !== false)
          {
            if($index !== false)
              $month = $index;
          }
        break;

        case '%d':
          $day = (int)$value;
        break;

        case '%Y':
          $year = (int)$value;
        break;
        case '%y':
          if($value < 40)
            $year = 2000 + $value;
          else
            $year = 1900 + $value;
        break;

        case '%T':
          if ($regs = explode(':', $value))
          {
            $hour   = (int)$regs[1];
            $minute = (int)$regs[2];
            $second = (int)$regs[3];
          }
        break;

        case '%D':
          if ($regs = explode('/', $value))
          {
            $hour   = (int)$regs[1];
            $minute = (int)$regs[2];
            $second = (int)$regs[3];
          }
        break;

        case '%R':
          if ($regs = explode(':', $value))
          {
            $hour   = (int)$regs[1];
            $minute = (int)$regs[2];
          }
        break;
      }
    }

    return array('hour' => $hour, 'minute' => $minute, 'second' => $second, 'month' => $month, 'day' => $day, 'year' => $year);
  }

  function _explodeTimeStringByFormat($time_string, $fmt)
  {
    $fmt_len = strlen($fmt);
    $time_string_len = strlen($time_string);

    $time_array = array();

    $fmt_pos = 0;
    $time_string_pos = 0;

    while(($fmt_pos = strpos($fmt, '%', $fmt_pos)) !== false)
    {
      $current_time_char = $fmt{++$fmt_pos};

      if(($fmt_pos+1) >= $fmt_len)
        $delimiter_pos = $time_string_len;
      elseif($time_string_pos <= $time_string_len)
      {
        $current_delimiter = $fmt{++$fmt_pos};
        $delimiter_pos = strpos($time_string, $current_delimiter, $time_string_pos);
        if($delimiter_pos === false)
          $delimiter_pos = $time_string_len;
      }

      $delimiter_len = $delimiter_pos - $time_string_pos;

      $value = substr($time_string, $time_string_pos, $delimiter_len);

      $time_array['%' . $current_time_char] = $value;

      $time_string_pos += ($delimiter_len + 1);
    }

    return $time_array;
  }

  function setByStamp($time=-1)
  {
    if($time == -1)
      $time = time();

    $arr = getdate($time);

    $this->year   = $arr['year'];
    $this->month  = $arr['mon'];
    $this->day    = $arr['mday'];
    $this->hour   = $arr['hours'];
    $this->minute = $arr['minutes'];
    $this->second = $arr['seconds'];
  }

  function setByDays($days)
  {
    $this->reset();

    $days    -= 1721119;
    $century =  floor(( 4 * $days - 1) / 146097);
    $days    =  floor(4 * $days - 1 - 146097 * $century);
    $day     =  floor($days / 4);

    $year    =  floor(( 4 * $day +  3) / 1461);
    $day     =  floor(4 * $day +  3 - 1461 * $year);
    $day     =  floor(($day +  4) / 4);

    $month   =  floor(( 5 * $day - 3) / 153);
    $day     =  floor(5 * $day - 3 - 153 * $month);
    $day     =  floor(($day +  5) /  5);

    if ($month < 10)
      $month +=3;
    else
    {
      $month -=9;
      if ($year++ == 99)
      {
        $year = 0;
        $century++;
      }
    }

    $year = sprintf('%02d', $century) . sprintf('%02d', $year);

    $this->day = (int)$day;
    $this->month = (int)$month;
    $this->year = (int)$year;
  }

  /**
   * Copy values from another date object
   * Makes this date a copy of another date object.
   */
  function copy($date)
  {
    $this->year = $date->getYear();
    $this->month = $date->getMonth();
    $this->day = $date->getDay();
    $this->hour = $date->getHour();
    $this->minute = $date->getMinute();
    $this->second = $date->getSecond();
    $this->tz = $date->getTimeZone();
  }

  /**
   *  date pretty printing, similar to strftime()
   *
   *  Formats the date in the given format, much like
   *  strftime().  Most strftime() attributes are supported.
   *  %a    abbreviated weekday name (Sun, Mon, Tue)
   *  %A    full weekday name (Sunday, Monday, Tuesday)
   *  %b    abbreviated month name (Jan, Feb, Mar)
   *  %B    full month name (January, February, March)
   *  %C    century number (the year divided by 100 and truncated to an integer, range 00 to 99)
   *  %d    day of month (range 00 to 31)
   *  %D    same as "%m/%d/%y"
   *  %e    day of month, single digit (range 0 to 31)
   *  %E    number of days since unspecified epoch
   *  %H    hour as decimal number (00 to 23)
   *  %I    hour as decimal number on 12-hour clock (01 to 12)
   *  %j    day of year (range 001 to 366)
   *  %m    month as decimal number (range 01 to 12)
   *  %M    minute as a decimal number (00 to 59)
   *  %n    newline character (\n)
   *  %O    dst-corrected timezone offset expressed as "+/-HH:MM"
   *  %o    raw timezone offset expressed as "+/-HH:MM"
   *  %p    either 'am' or 'pm' depending on the time
   *  %P    either 'AM' or 'PM' depending on the time
   *  %r    time in am/pm notation, same as "%I:%M:%S %p"
   *  %R    time in 24-hour notation, same as "%H:%M"
   *  %S    seconds as a decimal number (00 to 59)
   *  %t    tab character (\t)
   *  %T    current time, same as "%H:%M:%S"
   *  %w    weekday as decimal (0 = Sunday)
   *  %U    week number of current year, first sunday as first week
   *  %y    year as decimal (range 00 to 99)
   *  %Y    year as decimal including century (range 0000 to 9999)
   *  %%    literal '%'
   */
  function format($locale, $format=DATE_SHORT_FORMAT_ISO)
  {
    $output = '';

    for($strpos = 0; $strpos < strlen($format); $strpos++)
    {
      $char = substr($format, $strpos, 1);
      if ($char == '%')
      {
        $nextchar = substr($format, $strpos + 1, 1);
        switch ($nextchar)
        {
          case 'a':
              $output .= $locale->getDayName($this->getDayOfWeek(), true);
              break;
          case 'A':
              $output .= $locale->getDayName($this->getDayOfWeek());
              break;
          case 'b':
              $output .= $locale->getMonthName($this->month - 1, true);
              break;
          case 'B':
              $output .= $locale->getMonthName($this->month - 1);
              break;
          case 'p':
              $output .= $locale->getMeridiemName($this->hour);
              break;
          case 'P':
              $output .= $locale->getMeridiemName($this->hour, true);
              break;
          case 'C':
              $output .= sprintf("%02d", intval($this->year/100));
              break;
          case 'd':
              $output .= sprintf("%02d", $this->day);
              break;
          case 'D':
              $output .= sprintf("%02d/%02d/%02d", $this->month, $this->day, $this->year);
              break;
          case 'e':
              $output .= $this->day;
              break;
          case 'E':
              $output .= $this->dateToDays();
              break;
          case 'H':
              $output .= sprintf("%02d", $this->hour);
              break;
          case 'I':
              $hour = ($this->hour + 1) > 12 ? $this->hour - 12 : $this->hour;
              $output .= sprintf("%02d", $hour==0 ? 12 : $hour);
              break;
          case 'j':
              $output .= $this->getDayOfYear();
              break;
          case 'm':
              $output .= sprintf("%02d",$this->month);
              break;
          case 'M':
              $output .= sprintf("%02d",$this->minute);
              break;
          case 'n':
              $output .= "\n";
              break;
          case 'O':
              $offms = $this->tz->getOffset($this);
              $direction = $offms >= 0 ? '+' : '-';
              $offmins = abs($offms) / 1000 / 60;
              $hours = $offmins / 60;
              $minutes = $offmins % 60;
              $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
              break;
          case 'o':
              $offms = $this->tz->getRawOffset($this);
              $direction = $offms >= 0 ? '+' : '-';
              $offmins = abs($offms) / 1000 / 60;
              $hours = $offmins / 60;
              $minutes = $offmins % 60;
              $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
              break;
          case 'r':
              $hour = ($this->hour + 1) > 12 ? $this->hour - 12 : $this->hour;
              $output .= sprintf("%02d:%02d:%02d %s", $hour==0 ?  12 : $hour, $this->minute, $this->second, $this->hour >= 12 ? "PM" : "AM");
              break;
          case 'R':
              $output .= sprintf("%02d:%02d", $this->hour, $this->minute);
              break;
          case 'S':
              $output .= sprintf("%02d", $this->second);
              break;
          case 't':
              $output .= "\t";
              break;
          case 'T':
              $output .= sprintf("%02d:%02d:%02d", $this->hour, $this->minute, $this->second);
              break;
          case 'w':
              $output .= $this->getDayOfWeek();
              break;
          case 'U':
              $output .= $this->getWeekOfYear();
              break;
          case 'y':
              $output .= substr($this->year, 2, 2);
              break;
          case 'Y':
              $output .= $this->year;
              break;
          case 'Z':
              $output .= $this->tz->isInDaylightTime($this) ? $this->tz->getDSTShortName() : $this->tz->getShortName();
              break;
          case '%':
              $output .= '%';
              break;
          default:
              $output .= $char.$nextchar;
        }
        $strpos++;
      }
      else
        $output .= $char;
    }
    return $output;
  }

  function getStamp()
  {
    return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
  }

  function setTimeZone($tz)
  {
    $this->tz = $tz;
  }

  function setTimeZoneById($id)
  {
    if (DateTimeZone::isValidId($id))
      $this->tz = new DateTimeZone($id);
    else
      $this->tz = DateTimeZone::getDefault();
  }

  function isInDaylightTime()
  {
    return $this->tz->isInDaylightTime($this);
  }

  function toUTC()
  {
    if ($this->tz->getOffset($this) > 0)
      $this->subSeconds(intval($this->tz->getOffset($this) / 1000));
    else
      $this->addSeconds(intval(abs($this->tz->getOffset($this)) / 1000));

    $this->tz = new DateTimeZone('UTC');
  }

  /**
   * Converts this date to a new time zone
   *
   * Converts this date to a new time zone.
   * WARNING: This may not work correctly if your system does not allow
   * putenv() or if localtime() does not work in your environment.  See
   * date::time_zone::is_in_daylight_time() for more information.
   *
   */
  function convertToTimeZone($tz)
  {
    // convert to UTC
    if ($this->tz->getOffset($this) > 0)
      $this->subSeconds(intval(abs($this->tz->getOffset($this)) / 1000));
    else
      $this->addSeconds(intval(abs($this->tz->getOffset($this)) / 1000));

    // convert UTC to new timezone
    if ($tz->getOffset($this) > 0)
      $this->addSeconds(intval(abs($tz->getOffset($this)) / 1000));
    else
      $this->subSeconds(intval(abs($tz->getOffset($this)) / 1000));

    $this->tz = $tz;
  }

  function convertToTimeZoneById($id)
  {
   if (DateTimeZone::isValidId($id))
    $tz = new DateTimeZone($id);
   else
    $tz = DateTimeZone::getDefault();

   $this->convertToTimeZone($tz);
  }

  /**
   * Compares object with $d date object.
   * return int 0 if the dates are equal, -1 if is before, 1 if is after than $d
   */
  function compare($d, $use_time_zone=false)
  {
    if($use_time_zone)
    {
      $this->convertToTimeZone(new DateTimeZone('UTC'));
      $d->convertToTimeZone(new DateTimeZone('UTC'));
    }

    $days1 = $this->dateToDays();
    $days2 = $d->dateToDays();
    if ($days1 < $days2) return -1;
    if ($days1 > $days2) return 1;
    if ($this->hour < $d->getHour()) return -1;
    if ($this->hour > $d->getHour()) return 1;
    if ($this->minute < $d->getMinute()) return -1;
    if ($this->minute > $d->getMinute()) return 1;
    if ($this->second < $d->getSecond()) return -1;
    if ($this->second > $d->getSecond()) return 1;
    return 0;
  }

  function isBefore($when, $use_time_zone=false)
  {
    if ($this->compare($when, $use_time_zone) == -1)
      return true;
    else
      return false;
  }

  function isAfter($when, $use_time_zone=false)
  {
    if ($this->compare($when, $use_time_zone) == 1)
      return true;
    else
      return false;
  }

  function isEqual($when, $use_time_zone=false)
  {
    if ($this->compare($when, $use_time_zone) == 0)
      return true;
    else
      return false;
  }

  function isLeapYear()
  {
    return (($this->year % 4 == 0 &&  $this->year % 100 != 0) ||  $this->year % 400 == 0);
  }

  function getDayOfYear()
  {
    $days = array(0,31,59,90,120,151,181,212,243,273,304,334);

    $julian = ($days[$this->month - 1] + $this->day);

    if ($this->month > 2 &&  $this->isLeapYear())
      $julian++;

    return $julian;
  }

  function getDayOfWeek()
  {
    $year = $this->year;
    $month = $this->month;
    $day = $this->day;

    if ($month > 2)
      $month -= 2;
    else
      $month += 10;
      $year--;

    $day = ( floor((13 * $month - 1) / 5) +
        $day + ($year % 100) +
        floor(($year % 100) / 4) +
        floor(($year / 100) / 4) - 2 *
        floor($year / 100) + 77);

    $weekday_number = (($day - 7 * floor($day / 7)));

    return $weekday_number;
  }

  function getWeekOfYear()
  {
    $day = $this->day;
    $month = $this->month;
    $year = $this->year;

    $mnth = array (0,31,59,90,120,151,181,212,243,273,304,334);
    $y_isleap = $this->isLeapYear();

    $d = new Date($this);
    $d->setYear($this->year - 1);

    $y_1_isleap = $d->isLeapYear();

    $day_of_year_number = $day + $mnth[$month - 1];
    if ($y_isleap &&  $month > 2)
      $day_of_year_number++;

    // find Jan 1 weekday (monday = 1, sunday = 7)
    $yy = ($year - 1) % 100;
    $c = ($year - 1) - $yy;
    $g = $yy + intval($yy/4);
    $jan1_weekday = 1 + intval((((($c / 100) % 4) * 5) + $g) % 7);

    // weekday for year-month-day
    $h = $day_of_year_number + ($jan1_weekday - 1);
    $weekday = 1 + intval(($h - 1) % 7);

    // find if Y M D falls in YearNumber Y-1, WeekNumber 52 or
    if ($day_of_year_number <= (8 - $jan1_weekday) &&  $jan1_weekday > 4)
    {
      $yearnumber = $year - 1;
      if ($jan1_weekday == 5 ||  ($jan1_weekday == 6 &&  $y_1_isleap))
        $weeknumber = 53;
      else
        $weeknumber = 52;
    }
    else
      $yearnumber = $year;

    // find if Y M D falls in YearNumber Y+1, WeekNumber 1
    if ($yearnumber == $year)
    {
      if ($y_isleap)
        $i = 366;
      else
        $i = 365;

      if (($i - $day_of_year_number) < (4 - $weekday))
      {
        $yearnumber++;
        $weeknumber = 1;
      }
    }
    // find if Y M D falls in YearNumber Y, WeekNumber 1 through 53
    if ($yearnumber == $year)
    {
      $j = $day_of_year_number + (7 - $weekday) + ($jan1_weekday - 1);
      $weeknumber = intval($j / 7); // kludge!!! - JMC
      if ($jan1_weekday > 4)
        $weeknumber--;
    }

    return $weeknumber;
  }

  function getQuarterOfYear()
  {
    return (intval(($this->month - 1) / 3 + 1));
  }

  function dateToDays()
  {
    $century = (int) substr("{$this->year}", 0, 2);
    $year = (int) substr("{$this->year}", 2, 2);
    $month = $this->month;
    $day = $this->day;

    if ($month > 2)
      $month -= 3;
    else
    {
      $month += 9;
      if ($year)
        $year--;
      else
      {
        $year = 99;
        $century --;
      }
    }
    return (
        floor(( 146097 * $century) / 4 ) +
        floor(( 1461 * $year) / 4 ) +
        floor(( 153 * $month + 2) / 5 ) +
        $day + 1721119);
  }

  function getYear()
  {
    return $this->year;
  }

  function getMonth()
  {
    return $this->month;
  }

  function getDay()
  {
    return $this->day;
  }

  function getHour()
  {
    return $this->hour;
  }

  function getMinute()
  {
    return $this->minute;
  }

  function getSecond()
  {
    return $this->second;
  }

  function getTimeZone()
  {
    return $this->tz;
  }

  function setYear($y)
  {
    if ($y < 0 ||  $y > 9999)
      $this->year = 0;
    else
      $this->year = $y;
  }

  function setMonth($m)
  {
    if ($m < 1 ||  $m > 12)
      $this->month = 1;
    else
      $this->month = $m;
  }

  function setDay($d)
  {
    if ($d > 31 ||  $d < 1)
      $this->day = 1;
    else
      $this->day = $d;
  }

  function setHour($h)
  {
    if ($h > 23 ||  $h < 0)
      $this->hour = 0;
    else
      $this->hour = $h;
  }

  function setMinute($m)
  {
    if ($m > 59 ||  $m < 0)
      $this->minute = 0;
    else
      $this->minute = $m;
  }

  function setSecond($s)
  {
    if ($s > 59 ||  $s < 0)
      $this->second = 0;
    else
      $this->second = $s;
  }
}
?>