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

class search_engine_rambler_rule extends search_engine_regex_rule
{	
	function search_engine_rambler_rule()
	{
		parent :: search_engine_regex_rule('rambler', '/^.*rambler.*words=([^&]*).*$/', 1);
	}
}

?>