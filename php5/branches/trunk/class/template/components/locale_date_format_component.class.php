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
require_once(LIMB_DIR . '/class/lib/date/date.class.php');
require_once(LIMB_DIR . '/class/i18n/locale.class.php');

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
		$locale = Limb :: toolkit()->getLocale($this->locale_type);

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
	
	public function set_date($date_string, $format=DATE_SHORT_FORMAT_ISO)
	{
		switch($this->date_type)
		{
			case 'string':
        $locale = Limb :: toolkit()->getLocale($this->locale_type);
				$this->date->set_by_locale_string($locale, $date_string, $format);
			break;
			
			case 'stamp':
				$this->date->set_by_stamp((int)$date_string);
			break;
		}
	}
	
	public function format()
	{
    $locale = Limb :: toolkit()->getLocale($this->locale_type);
    
		if($this->format_string)
			$format_string = $this->format_string;
		else
			$format_string = $locale->get_short_date_format();
		
		echo $this->date->format($locale, $format_string);
	}
	
} 

?>