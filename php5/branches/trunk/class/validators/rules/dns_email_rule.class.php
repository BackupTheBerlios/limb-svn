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
require_once(LIMB_DIR . '/class/validators/rules/email_rule.class.php');

/**
* check for a valid email address and verify that a mail server
* DNS record exists for this address.
* 
*/
class dns_email_rule extends email_rule
{
	protected function check_domain($value)
	{
		parent::check_domain($value);
		
		if ($this->is_valid())
		{
			if (!checkdnsrr($value, "MX"))
			{
				$this->error('EMAIL_DNS');
			} 
		} 
	} 
}

?>