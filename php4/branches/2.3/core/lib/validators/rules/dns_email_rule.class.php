<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/validators/rules/email_rule.class.php');

/**
* check for a valid email address and verify that a mail server
* DNS record exists for this address.
*
*/
class dns_email_rule extends email_rule
{
  /**
  * Constructs a dns_email_rule
  *
  * @param string $ fieldname to validate
  * @param array $ of acceptable values
  * @access public
  */
  function dns_email_rule($fieldname)
  {
    parent::email_rule($fieldname);
  }

  /**
  * Performs validation of a single value
  *
  * @access protected
  */
  function check_domain($value)
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