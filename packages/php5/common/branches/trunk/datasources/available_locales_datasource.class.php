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
require_once(LIMB_DIR . 'class/datasources/options_datasource.interface.php'); 
require_once(LIMB_DIR . 'class/i18n/locale.class.php');

class available_locales_datasource implements options_datasource
{	
	public function get_options_array()
	{
		return locale :: get_available_locales_data();
	}
	
	public function get_default_option()
	{
		return MANAGEMENT_LOCALE_ID;
	}
}
?>