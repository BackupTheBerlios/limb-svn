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
require_once(LIMB_DIR . '/class/validators/rules/DomainRule.class.php');

/**
* check for a valid domain name with a valid DNS Record.
* If DNS is down, data will not be considered invalid,
* possibly preventing data entry when connectivity is bad.
*/
class DnsDomainRule extends DomainRule
{
  function check($value)
  {
    parent::check($value);

    if ($this->isValid())
    {
      if (!checkdnsrr($value, 'A'))
      {
        $this->error('BAD_DOMAIN_DNS');
      }
    }
  }
}
}
?>