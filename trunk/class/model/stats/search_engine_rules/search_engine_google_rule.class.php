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
require_once(LIMB_DIR . '/class/model/stats/search_engine_rules/search_engine_regex_rule.class.php');
require_once(LIMB_DIR . '/class/lib/http/utf8_to_win1251.inc.php');

class search_engine_google_rule extends search_engine_regex_rule
{	

	function search_engine_google_rule()
	{
		parent :: search_engine_regex_rule('google', '/^.*google\..*?q=(cache:[^\s]*\s)?([^&]*).*$/', 2);
	}
	
	function get_matching_phrase()
	{
		return utf8_to_win1251(parent :: get_matching_phrase());
	}
}

?>