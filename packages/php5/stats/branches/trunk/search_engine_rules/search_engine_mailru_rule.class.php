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
require_once(dirname(__FILE__) . '/search_engine_regex_rule.class.php');

class search_engine_mailru_rule extends search_engine_regex_rule
{	
	public function __construct()
	{
		parent :: __construct('mail.ru', '/^.*go\.mail\.ru.*(words|q)=([^&]*).*$/', 2);
	}
}

?>