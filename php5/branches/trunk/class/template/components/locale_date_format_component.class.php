<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'class/lib/date/date.class.php');
require_once(LIMB_DIR . 'class/i18n/locale.class.php');

class locale_date_format_component extends component
{
	protected $date = null;
	
	protected $date_type = 'string';
		
	protected $format_string = '';
	
	protected $locale_type = CONTENT_LOCALE_ID;

	public function prepare()
	{
		$this->date = new date();
	}
	
	public function set_format_string($string)
	{
		$this->format_string = $string;
	}
	
	public function set_date_type($type)
	{
		$this->date_type = $type;
	}

	public function set_locale_type($locale_type)
	{
		if ($locale_type == 'management')
			$this->locale_type = MANAGEMENT_LOCALE_ID;
		else
			$this->locale_type = CONTENT_LOCALE_ID;
	}
	
	public function set_locale_format_type($type)
	{		
		$locale = locale :: instance($this->locale_type);

		switch($type)
		{			
			case 'time':
				$this->format_string = $locale->get_time_format();
			break;
			
			case 'short_time':
				$this->format_string = $locale->get_short_time_format();
			break;
			
			case 'date':
				$this->format_string = $locale->get_date_format();
			break;
			
			case 'short_date':
				$this->format_string = $locale->get_short_date_format();
			break;

			case 'date_time':
				$this->format_string = $locale->get_date_time_format();
			break;

			case 'short_date_time':
				$this->format_string = $locale->get_short_date_time_format();
			break;
			
			default:
				$this->format_string = $locale->get_short_date_format();
		}
	}
	
	public function set_date($date_string)
	{
		switch($this->date_type)
		{
			case 'string':
				$this->date->set_by_string($date_string);
			break;
			
			case 'stamp':
				$this->date->set_by_stamp((int)$date_string);
			break;
		}
	}
	
	public function format()
	{
		if($this->format_string)
			$format_string = $this->format_string;
		else
		{
			$locale = locale :: instance($this->locale_type);
			$format_string = $locale->get_short_date_format();
		}	
		
		echo $this->date->format($format_string);
	}
	
} 

?>