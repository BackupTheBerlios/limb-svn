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

class search_engine_aport_rule extends search_engine_regex_rule
{	
	public function __construct()
	{
		parent :: __construct('aport', '/^.*sm\.aport.*r=([^&]*).*$/', 1);
	}
}

?>