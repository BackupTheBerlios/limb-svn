<?php 
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: domain_rule.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/validators/rules/single_field_rule.class.php');

/**
* Check for a valid url.
*/
class url_rule extends single_field_rule
{
	function url_rule($fieldname)
	{
		parent :: single_field_rule($fieldname);
	} 

	function check($value)
	{ 
		//very primitive check, full check will be implemented later
		if (!preg_match("~^(https?://[a-zA-Z\.]+)?/?([a-zA-Z\.]+/?)+(\??[^\?]*)#?[^#]*$~i", $value))
		{
			$this->error('BAD_URL');
		} 

	} 
} 
?>