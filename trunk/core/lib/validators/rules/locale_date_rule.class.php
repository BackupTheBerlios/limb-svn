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
require_once(LIMB_DIR . '/core/lib/validators/rules/domain_rule.class.php');
require_once(LIMB_DIR . '/core/lib/i18n/locale.class.php');
require_once(LIMB_DIR . '/core/lib/date/date.class.php');

class locale_date_rule extends single_field_rule 
{
	var $locale_id = '';
	
	function locale_date_rule($fieldname, $locale_id = '')
	{
		if (!$locale_id && !defined('CONTENT_LOCALE_ID'))
			$this->locale_id = DEFAULT_CONTENT_LOCALE_ID;
		elseif(!$locale_id)
			$this->locale_id = CONTENT_LOCALE_ID;
		else	
			$this->locale_id = $locale_id;
		
		parent :: single_field_rule($fieldname);
	} 

	function check($value)
	{
		$locale =& locale :: instance($this->locale_id);
		$date =& new date();
		
		$date->set_by_string($value, $locale->get_short_date_format());
		
		if(!$date->is_valid())
			$this->error('INVALID_DATE');
	} 
} 

?>