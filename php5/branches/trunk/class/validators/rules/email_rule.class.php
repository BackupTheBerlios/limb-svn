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
require_once(LIMB_DIR . '/class/validators/rules/domain_rule.class.php');

class email_rule extends domain_rule
{
	protected function check_user($value)
	{
		if (!preg_match('/^[a-z0-9]{1}([-a-z0-9_.]+)*$/', $value))
			$this->error(strings :: get('invalid_email', 'error'));
	} 

	protected function check_domain($value)
	{
		parent :: check($value);
	} 

	protected function check($value)
	{
		if (is_integer(strpos($value, '@')))
		{
			list($user, $domain) = split('@', $value, 2);
			$this->check_user($user);
			$this->check_domain($domain);
		} 
		else
		{
			$this->error(strings :: get('invalid_email', 'error'));
		} 
	} 
} 

?>