<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: locale_date_rule.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/lib/validators/rules/domain_rule.class.php');
require_once(LIMB_DIR . '/core/lib/locale/locale.class.php');
require_once(LIMB_DIR . '/core/lib/date/date.class.php');

class locale_date_rule extends single_field_rule 
{
	function locale_date_rule($fieldname)
	{
		parent :: single_field_rule($fieldname);
	} 

	function check($value)
	{
		$locale =& locale :: instance();
		$date =& new date();
		
		$date->set_by_string($value, $locale->get_short_date_format());
		
		if(!$date->is_valid())
			$this->error('INVALID_DATE');
	} 
} 

?>