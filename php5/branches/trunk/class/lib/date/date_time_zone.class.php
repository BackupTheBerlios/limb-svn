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
//inspired by PEAR::Date package

/**
* time_zone representation class, along with time zone information data.
* 
* time_zone representation class, along with time zone information data.
* The default timezone is set from the first valid timezone id found
* in one of the following places, in this order: <br>
* 1) global $_DATE_TIMEZONE_DEFAULT<br>
* 2) system environment variable PHP_TZ<br>
* 3) system environment variable TZ<br>
* 4) the result of date('T')<br>
* If no valid timezone id is found, the default timezone is set to 'UTC'.
* You may also manually set the default timezone by passing a valid id to
* date_time_zone::set_default().<br>
* 
* This class includes time zone data (from zoneinfo) in the form of a global array, $_DATE_TIMEZONE_DATA.
*/
class date_time_zone
{
	protected $id;
	protected $longname; //Long Name of this time zone (ie Central Standard Time)
	protected $shortname; //Short Name of this time zone (ie CST)
	protected $hasdst; //true if this time zone observes daylight savings time
	protected $dstlongname; //DST Long Name of this time zone
	protected $dstshortname; //DST Short Name of this timezone
	protected $offset; //offset, in milliseconds, of this timezone
	
	protected $default; //System Default Time Zone
	
	function __construct($id)
	{
		global $_DATE_TIMEZONE_DATA;

		if (date_time_zone::is_valid_id($id))
		{
			$this->id = $id;
			$this->longname = $_DATE_TIMEZONE_DATA[$id]['longname'];
			$this->shortname = $_DATE_TIMEZONE_DATA[$id]['shortname'];
			$this->offset = $_DATE_TIMEZONE_DATA[$id]['offset'];

			if ($_DATE_TIMEZONE_DATA[$id]['hasdst'])
			{
				$this->hasdst = true;
				$this->dstlongname = $_DATE_TIMEZONE_DATA[$id]['dstlongname'];
				$this->dstshortname = $_DATE_TIMEZONE_DATA[$id]['dstshortname'];
			} 
			else
			{
				$this->hasdst = false;
				$this->dstlongname = $this->longname;
				$this->dstshortname = $this->shortname;
			} 
		} 
		else
		{
			$this->id = 'UTC';
			$this->longname = $_DATE_TIMEZONE_DATA[$this->id]['longname'];
			$this->shortname = $_DATE_TIMEZONE_DATA[$this->id]['shortname'];
			$this->hasdst = $_DATE_TIMEZONE_DATA[$this->id]['hasdst'];
			$this->offset = $_DATE_TIMEZONE_DATA[$this->id]['offset'];
		} 
	} 

	static public function get_default()
	{
		global $_DATE_TIMEZONE_DEFAULT;
		return new date_time_zone($_DATE_TIMEZONE_DEFAULT);
	} 

	static public function set_default($id)
	{
		global $_DATE_TIMEZONE_DEFAULT;
		if (date_time_zone::is_valid_id($id))
			$_DATE_TIMEZONE_DEFAULT = $id;
	} 

	static public function is_valid_id($id)
	{
		global $_DATE_TIMEZONE_DATA;
		if (isset($_DATE_TIMEZONE_DATA[$id]))
			return true;
		else
			return false;
	} 

	/**
	* Is this time zone equal to another
	*/
	public function is_equal($tz)
	{
		if (strcasecmp($this->id, $tz->get_id()) == 0)
			return true;
		else
			return false;
	} 

	/**
	* Is this time zone equivalent to another
	* 
	* Tests to see if this time zone is equivalent to
	* a given time zone object.  Equivalence in this context
	* is defined by the two time zones having an equal raw
	* offset and an equal setting of "hasdst".  This is not true
	* equivalence, as the two time zones may have different rules
	* for the observance of DST, but this implementation does not
	* know DST rules.
	*/
	public function is_equivalent($tz)
	{
		if ($this->offset == $tz->get_raw_offset() && $this->hasdst == $tz->has_daylight_time())
			return true;
		else
			return false;
	} 

	/**
	* Returns true if this zone observes daylight savings time
	*/
	public function has_daylight_time()
	{
		return $this->hasdst;
	} 

	/**
	* Is the given date/time in DST for this time zone
	* 
	* Attempts to determine if a given date object represents a date/time
	* that is in DST for this time zone.  WARNINGS: this basically attempts to
	* "trick" the system into telling us if we're in DST for a given time zone.
	* This uses putenv() which may not work in safe mode, and relies on unix time
	* which is only valid for dates from 1970 to ~2038.  This relies on the
	* underlying OS calls, so it may not work on Windows or on a system where
	* zoneinfo is not installed or configured properly.
	*/
	static public function in_daylight_time($date)
	{
		$env_tz = '';
		if (getenv('TZ'))
			$env_tz = getenv('TZ');

		putenv('TZ=' . $this->id);
		$ltime = localtime($date->get_stamp(), true);
		putenv('TZ=' . $env_tz);
		return $ltime['tm_isdst'];
	} 

	/**
	* Get the DST offset for this time zone
	* 
	* Returns the DST offset of this time zone, in milliseconds,
	* if the zone observes DST, zero otherwise.  Currently the
	* DST offset is hard-coded to one hour.
	*/
	public function get_DST_savings()
	{
		if ($this->hasdst)
			return 3600000;
		else
			return 0;
	} 

	/**
	* Get the DST-corrected offset to UTC for the given date
	* 
	* Attempts to get the offset to UTC for a given date/time, taking into
	* account daylight savings time, if the time zone observes it and if
	* it is in effect.  Please see the WARNINGS on date_time_zone::in_daylight_time().
	*/
	public function get_offset($date)
	{
		if ($this->in_daylight_time($date))
			return $this->offset + $this->get_DST_savings();
		else
			return $this->offset;
	} 
		
	/**
	* Returns the list of valid time zone id strings
	*/
	static public function get_available_ids()
	{
		global $_DATE_TIMEZONE_DATA;
		return array_keys($_DATE_TIMEZONE_DATA);
	} 

	/**
	* Returns the time zone id  for this time zone, i.e. "America/Chicago"
	*/
	public function get_id()
	{
		return $this->id;
	} 

	/**
	* Returns the long name for this time zone,
	* i.e. "Central Standard Time"
	*/
	public function get_long_name()
	{
		return $this->longname;
	} 

	/**
	* Returns the short name for this time zone, i.e. "CST"
	*/
	public function get_short_name()
	{
		return $this->shortname;
	} 

	/**
	* Returns the DST long name for this time zone, i.e. "Central Daylight Time"
	*/
	public function get_DST_long_name()
	{
		return $this->dstlongname;
	} 

	/**
	* Returns the DST short name for this time zone, i.e. "CDT"
	*/
	public function get_DST_short_name()
	{
		return $this->dstshortname;
	} 

	/**
	* Returns the raw (non-DST-corrected) offset from UTC/GMT for this time zone
	*/
	public function get_raw_offset()
	{
		return $this->offset;
	} 
} 

$GLOBALS['_DATE_TIMEZONE_DATA'] = array('UTC' => array('offset' => 0,
		'longname' => "Coordinated Universal Time",
		'shortname' => 'UTC',
		'hasdst' => false),
	);

if (isset($_DATE_TIMEZONE_DEFAULT) && date_time_zone::is_valid_id($_DATE_TIMEZONE_DEFAULT))
	date_time_zone::set_default($_DATE_TIMEZONE_DEFAULT);
else
	date_time_zone::set_default('UTC');

?>