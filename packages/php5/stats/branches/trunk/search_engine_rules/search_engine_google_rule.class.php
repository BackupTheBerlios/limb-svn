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
require_once(dirname(__FILE__) . '/search_engine_regex_rule.class.php');
require_once(LIMB_DIR . '/class/lib/http/utf8_to_win1251.inc.php');

class search_engine_google_rule extends search_engine_regex_rule
{	
	public function __construct()
	{
		parent :: __construct('google', '/^.*google\..*?q=(cache:[^\s]*\s)?([^&]*).*$/', 2);
	}
	
	public function get_matching_phrase()
	{
		return utf8_to_win1251(parent :: get_matching_phrase());
	}
}

?>