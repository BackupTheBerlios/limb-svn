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
require_once(LIMB_DIR . 'core/datasource/datasource.class.php');
require_once(LIMB_DIR . 'core/lib/i18n/locale.class.php');
require_once(LIMB_DIR . 'core/lib/security/user.class.php');

class available_locales_datasource extends datasource
{	
	function get_options_array()
	{
		return locale :: get_available_locales_data();
	}
	
	function get_default_option()
	{
		return MANAGEMENT_LOCALE_ID;
	}
}
?>