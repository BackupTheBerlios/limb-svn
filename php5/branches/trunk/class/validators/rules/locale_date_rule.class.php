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
require_once(LIMB_DIR . '/class/validators/rules/domain_rule.class.php');
require_once(LIMB_DIR . '/class/i18n/locale.class.php');
require_once(LIMB_DIR . '/class/lib/date/date.class.php');

class locale_date_rule extends single_field_rule 
{
	private $locale_id = '';
	
	function __construct($fieldname, $locale_id = '')
	{
		if (!$locale_id && !defined('CONTENT_LOCALE_ID'))
			$this->locale_id = DEFAULT_CONTENT_LOCALE_ID;
		elseif(!$locale_id)
			$this->locale_id = CONTENT_LOCALE_ID;
		else	
			$this->locale_id = $locale_id;
		
		parent :: __construct($fieldname);
	} 

	protected function check($value)
	{
		$date = new date();
		
		$date->set_by_string($value, locale :: instance($this->locale_id)->get_short_date_format());
		
		if(!$date->is_valid())
			$this->error('INVALID_DATE');
	} 
} 

?>