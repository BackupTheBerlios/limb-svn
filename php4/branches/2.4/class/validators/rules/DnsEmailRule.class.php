<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/validators/rules/EmailRule.class.php');

/**
* check for a valid email address and verify that a mail server
* DNS record exists for this address.
*
*/
class DnsEmailRule extends EmailRule
{
  function checkDomain($value)
  {
    parent::checkDomain($value);

    if ($this->isValid())
    {
      if (!checkdnsrr($value, "MX"))
      {
        $this->error('EMAIL_DNS');
      }
    }
  }
}

?>