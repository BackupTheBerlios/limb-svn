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

// The constant telling us what day starts the week. Monday (1) is the
// international standard. Redefine this to 0 if you want weeks to
// begin on Sunday.
if (!defined('DATE_CALC_BEGIN_WEEKDAY'))
    define('DATE_CALC_BEGIN_WEEKDAY', 1);
    
require_once(LIMB_DIR . 'core/lib/date/date.class.php');
require_once(LIMB_DIR . 'core/lib/date/date_span.class.php');

class calendar
{
	function calendar()
	{
	}
		
  function & get_now()
  {
  	return new date();
  }
  
  function _check_date(& $date)
  {
  	if($date == null)
  		$date = new date();
  }
  
  function & add_seconds($sec, $date=null)
  {          
  	return $this->add_span(new date_span($sec), $date);
  }

  function & add_span($span, $date=null)
  {
  	$this->_check_date(&$date);
  	
  	$second = $date->second + $span->second;
  	$minute = $date->minute + $span->minute;
  	$hour = $date->hour + $span->hour;
  	$days_to_add = $span->day;
  	  	
    if ($second >= 60) 
    {
      $minute++;
      $second -= 60;
    }

    if ($minute >= 60) 
    {
      $hour++;
      if ($hour >= 24) 
      {
      	$days_to_add++;
        $hour -= 24;
      }
      $minute -= 60;
    }

    if ($hour >= 24) 
    {
    	$days_to_add++;
      $hour -= 24;
    }
    
    $date->second = $second;
    $date->minute = $minute;
    $date->hour = $hour;
    
    return $this->add_days($days_to_add, $date);
  }

  function & sub_seconds($sec, $date=null)
  {
   	return $this->sub_span($date, new date_span($sec));
  }

  function & sub_span($span, $date=null)
  {
  	$this->_check_date(&$date);
  	
   	$second = $date->second - $span->second;
  	$minute = $date->minute - $span->minute;
  	$hour = $date->hour - $span->hour;
  	$days_to_sub = $span->day;

    if ($second < 0) 
    {
      $minute--;
      $second += 60;
    }

    if ($minute < 0) 
    {
      $hour--;
      if ($hour < 0) 
      {
        $days_to_sub++;
        $hour += 24;
      }
      $minute += 60;
    }

    if ($hour < 0) 
    {
    	$days_to_sub++;
      $hour += 24;
    }

    $date->second = $second;
    $date->minute = $minute;
    $date->hour = $hour;
    
    return $this->sub_days($days_to_add, $date);
  }
  
  function & add_days($days, $date=null)
  {
  	$this->_check_date(&$date);
  	
		$d =& new date($date);
		$d->set_by_days($d->date_to_days() + $days);
		
		return $d;
  }
  
  function & sub_days($days, $date=null)
  {
  	$this->_check_date(&$date);
  	
		$d =& new date($date);
		$d->set_by_days($d->date_to_days() - $days);
		
		return $d;
  }
  
  function is_future($date)
  {
    $now = $this->get_now();

    if ($date->year > $now->year)
    	return true;
    elseif ($date->year == $now->year) 
    {
      if ($date->month >  $now->month)
      	return true;
      elseif ($date->month ==  $now->month) 
      {
        if ($date->day >  $now->day)
        	return true;
      }
    }

    return false;
  }

  function is_past($date)
  {
    $now = $this->get_now();

    if ($date->year < $now->year)
    	return true;
    elseif ($date->year == $now->year) 
    {
      if ($date->month <  $now->month)
      	return true;
      elseif ($date->month ==  $now->month) 
      {
        if ($date->day < $now->day)
        	return true;
      }
    }

    return false;
  }

  function get_days_in_month($date=null)
  {
  	$this->_check_date(&$date);
  	
  	$month = $date->month;
  	
  	if ($month == 2) 
  	{
    	if ($date->is_leap_year())
      	return 29;
      else
      	return 28;
    } 
    elseif ($month == 4 || $month == 6 || $month == 9 || $month == 11)
    	return 30;
    else
    	return 31;
  }

  function & get_begin_of_next_month($date=null)
  {
  	$this->_check_date(&$date);
  	
  	$day = $date->day;
  	$month = $date->month;
  	$year = $date->year;
  	
    if ($month < 12) 
    {
      $month++;
      $day = 1;
    } 
    else 
    {
      $year++;
      $month = 1;
      $day = 1;
    }
        
    return date :: create($month, $year, $day);
  }

  function & get_end_of_next_month($date=null)
  { 
  	$this->_check_date(&$date);
  	 
  	$month = $date->month;
  	$year = $date->year;

    if ($month < 12)
    	$month++;
    else 
    {
      $year++;
      $month = 1;
    }
    
    $d1 =& date :: create($year, $month);
    
    $day = $this->days_in_month($d1);

    return date :: create($year, $month, $day);
  }

  function & get_begin_of_prev_month($date=null)
  {
  	$this->_check_date(&$date);
  	
  	$day = $date->day;
  	$month = $date->month;
  	$year = $date->year;

    if ($month > 1) 
    {
	    $month--;
	    $day = 1;
    } 
    else 
    {
	    $year--;
	    $month = 12;
	    $day = 1;
    }

    return date :: create($year, $month, $day);
  }

  function & get_end_of_prev_month($date=null)
  {
  	$this->_check_date(&$date);
  	
  	$month = $date->month;
  	$year = $date->year;

    if ($month > 1)
    	$month--;
    else
    	$year--;
      $month = 12;

    $d1 =& date :: create($year, $month);
    
    $day = $this->days_in_month($d1);

    return date :: create($year, $month, $day);
  }

  function & get_next_week_day($date=null)
  {
  	$this->_check_date(&$date);
  	
    $day_of_week = $date->get_day_of_week();

    if($day_of_week == 5)
    	$days = 3;
    elseif($day_of_week == 6)
    	$days = 2;
    else
      $days = 1;
		
		return $this->add_days($days, $date);
  }

  function & get_prev_week_day($date=null)
  {
  	$this->_check_date(&$date);
  	
    $day_of_week = $date->get_day_of_week();

    if($day_of_week == 1)
    	$days = 3;
    elseif($day_of_week == 0)
    	$days = 2;
    else
      $days = 1;
		
		return $this->sub_days($days, $date);
  }

  function & get_next_day_of_week($dow, $date=null, $on_or_after=false)
  {
  	$this->_check_date(&$date);
  	
    $curr_weekday = $date->get_day_of_week();

    if ($curr_weekday == $dow) 
    {
      if (!$on_or_after)
      	$days = 7;
    }
    elseif ($curr_weekday > $dow)
    	$days = 7 - ( $curr_weekday - $dow );
    else
    	$days = $dow - $curr_weekday;

		return $this->add_days($days, $date);
  }

  function & get_get_prev_day_of_week($dow, $date=null, $on_or_before=false)
  {
  	$this->_check_date(&$date);
  	
    $days = $date->date_to_days();
    $curr_weekday = $date->get_day_of_week();

    if ($curr_weekday == $dow) 
    {
      if (!$on_or_before)
      	$days = 7;
    }
    elseif ($curr_weekday < $dow)
    	$days = 7 - ( $dow - $curr_weekday );
    else
    	$days = $curr_weekday - $dow;

		return $this->sub_days($days, $date);
  }

  function & get_next_day($date=null)
  {
  	return $this->add_days(1, $date);
  }

  function & get_prev_day($date=null)
  {
		return $this->sub_days(1, $date);
	}

  function get_dates_diff($d1, $d2=null)
  {
  	$this->_check_date(&$d2);
  	
	  if (!$d1->is_valid())
	  	return -1;

	  if (!$d2->is_valid())
	  	return -1;
	
	  return abs($d1->date_to_days() - $d2->date_to_days());
  }

  function compare_dates($d1, $d2=null)
  {
  	$this->_check_date(&$d2);
  	
	  $ndays1 = $d1->date_to_days();
	  $ndays2 = $d2->date_to_days();
	  
	  if ($ndays1 == $ndays2)
	  	return 0;
	
	  return ($ndays1 > $ndays2) ? 1 : -1;
  }

  function get_weeks_in_month($date=null)
  {
  	$this->_check_date(&$date);
  	
  	$d = $this->get_first_of_month_day($date);
  	$week_day = $d->get_day_of_week();
  	
    if (DATE_CALC_BEGIN_WEEKDAY == 1) 
    {
	    if ($week_day == 0)
	    	$first_week_days = 1;
	    else
	    	$first_week_days = 7 - $week_day - 1;
    } 
    else
    	$first_week_days = 7 - $week_day;

    return ceil((($this->get_days_in_month($date) - $first_week_days) / 7) + 1);
  }

  function & get_first_of_month_day($date=null)
  {
  	$this->_check_date(&$date);
  		
  	$new_date =& new date($date);
  	$new_date->day = 1;
  	
  	return $new_date;
  }

  function & get_begin_of_week($date=null)
  {
  	$this->_check_date(&$date);
  
    $this_weekday = $date->get_day_of_week();
		
    if(DATE_CALC_BEGIN_WEEKDAY == 1) 
    	$days_to_sub = $this_weekday;
    else
    	if($this_weekday == 6)
    		$days_to_sub = 0;
      else
      	$days_to_sub = $this_weekday + 1;
    
    return $this->sub_days($days_to_sub, $date);
  }

  function & get_end_of_week($date=null)
  {
  	$this->_check_date(&$date);
  	
	  $this_weekday = $date->get_day_of_week();
		
		if(DATE_CALC_BEGIN_WEEKDAY == 1)
	  	$days_to_add = 6 - $this_weekday;
	  else
	  	if($this_weekday == 6)
    		$days_to_add = 6;
      else
      	$days_to_add = 6 - $this_weekday - 1;

		 
	  return $this->add_days($days_to_add, $date);
  }

  function get_begin_of_next_week($date=null)
  {	
		return $this->get_begin_of_week($this->add_days(7, $date));
  }

  function get_begin_of_prev_week($date=null)
  {			
		return $this->get_begin_of_week($this->sub_days(7, $date));
  }

  /**
   * Return an array with days(every day is represented with int number) in week
   */
  function get_calendar_week($date=null)
  {
  	$this->_check_date(&$date);
  	
	  $week_array = array();
	
	  $curr_date = $this->get_begin_of_week($date);
		$curr_date_number = $curr_date->date_to_days();
		
	  for($i=0; $i <= 6; $i++) 
	  {
	    $week_array[$i] = $curr_date_number++;
	  }
	
	  return $week_array;
  }

  /**
   * Return a set of arrays to construct a calendar month for
   * the given date. array $month[$row][$col]
   *
   */
  function get_calendar_month($date=null)
  {
  	$this->_check_date(&$date);
  	
    $month_array = array();
    
    $d = $this->get_first_of_month_day($date);
		$week_day = $d->get_day_of_week();

    // date for the first row, first column of calendar month
    if(DATE_CALC_BEGIN_WEEKDAY == 1)
    	$days_to_sub = $week_day;
    else
    	if($this_weekday == 6)
    		$days_to_sub = 0;
      else
      	$days_to_sub = $week_day + 1;
      	
    $curr_date = $this->sub_days($days_to_sub, $d);

    // number of days in this month
    $days_in_month = $this->get_days_in_month($date);
    $weeks_in_month = $this->get_weeks_in_month($date);
    
    $curr_date_number = $curr_date->date_to_days();
    
    for($row_counter=0; $row_counter < $weeks_in_month; $row_counter++) 
    {
      for($column_counter=0; $column_counter <= 6; $column_counter++) 
      {
        $month_array[$row_counter][$column_counter] = $curr_date_number++;
      }
    }

    return $month_array;
  }

  /**
   * Return a set of arrays to construct a calendar year for
   * the given date.
   *
   * array $year[$month][$row][$col]
   */

  function get_calendar_year($date=null)
  {
  	$this->_check_date(&$date);
  	
	  $year_array = array();
	  
	  for($curr_month=0; $curr_month <=11; $curr_month++)
	  {
	  	$date->month = $curr_month;
	  	$year_array[$curr_month] = $this->get_calendar_month($date);
	  }
	
	  return $year_array;
  }

  /**
   * Calculates the date of the Nth weekday of the month,
   * such as the second Saturday of January 2000.
   *
   * $occurance: 1=first, 2=second, 3=third, etc.
   * $day_of_week: 0=Sunday, 1=Monday, etc.
   *
   */
  function get_n_week_day_of_month($occurance, $day_of_week, $date=null)
  {
  	$this->_check_date(&$date);
  	
	  $year = $date->year;
	  $month = $date->month;
	
	  $DOW1day = (($occurance - 1) * 7 + 1);
	  $d = date :: create($year, $month, $DOW1day);
	  $DOW1 = $d->get_day_of_week();
	
	  $wdate = ($occurance - 1) * 7 + 1 + (7 + $day_of_week - $DOW1) % 7;
	
	  if ( $wdate > $this->get_days_in_month($date))
	  	return null;
	  else 
	    return date :: create($year, $month, $wdate);
  }

  /**
  * Determines julian date of the given season
  * Adapted from previous work in Java by James Mark Hamilton, mhamilton@qwest.net
  *
  *	$season is VERNALEQUINOX, SUMMERSOLSTICE, AUTUMNALEQUINOX, or WINTERSOLSTICE.
  * $year in format CCYY, must be a calendar year between -1000BC and 3000AD.
  *
  * returns float julian date
  */
  function get_date_season($season, $date=null) 
  {
  	$this->_check_date(&$date);
  	
    $year = $date->year;

    if (($year >= -1000) && ($year <= 1000)) 
    {
      $y = $year / 1000.0;
      if ($season == 'VERNALEQUINOX')
      	$julian_date = (((((((-0.00071 * $y) - 0.00111) * $y) + 0.06134) * $y) + 365242.1374) * $y) + 1721139.29189;
      elseif ($season == 'SUMMERSOLSTICE')
      	$julian_date = ((((((( 0.00025 * $y) + 0.00907) * $y) - 0.05323) * $y) + 365241.72562) * $y) + 1721233.25401;
      elseif ($season == 'AUTUMNALEQUINOX')
      	$julian_date = ((((((( 0.00074 * $y) - 0.00297) * $y) - 0.11677) * $y) + 365242.49558) * $y) + 1721325.70455;
      elseif ($season == 'WINTERSOLSTICE')
         $julian_date = (((((((-0.00006 * $y) - 0.00933) * $y) - 0.00769) * $y) + 365242.88257) * $y) + 1721414.39987;
    } 
    elseif (($year > 1000) && ($year <= 3000)) 
    {
      $y = ($year - 2000) / 1000;
      if ($season == 'VERNALEQUINOX')
      	$julian_date = (((((((-0.00057 * $y) - 0.00411) * $y) + 0.05169) * $y) + 365242.37404) * $y) + 2451623.80984;
      elseif ($season == 'SUMMERSOLSTICE')
      	$julian_date = (((((((-0.0003 * $y) + 0.00888) * $y) + 0.00325) * $y) + 365241.62603) * $y) + 2451716.56767;
      elseif ($season == 'AUTUMNALEQUINOX')
      	$julian_date = ((((((( 0.00078 * $y) + 0.00337) * $y) - 0.11575) * $y) + 365242.01767) * $y) + 2451810.21715;
      elseif ($season == 'WINTERSOLSTICE')
      	$julian_date = ((((((( 0.00032 * $y) - 0.00823) * $y) - 0.06223) * $y) + 365242.74049) * $y) + 2451900.05952;
    }

    return $julian_date;
  }
}
?>