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

class EmailRule extends DomainRule
{
  function checkUser($value)
  {
    if (!preg_match('/^[a-z0-9]{1}([-a-z0-9_.]+)*$/', $value))
      $this->error(Strings :: get('invalid_email', 'error'));
  }

  function checkDomain($value)
  {
    parent :: check($value);
  }

  function check($value)
  {
    if (is_integer(strpos($value, '@')))
    {
      list($user, $domain) = split('@', $value, 2);
      $this->checkUser($user);
      $this->checkDomain($domain);
    }
    else
    {
      $this->error(Strings :: get('invalid_email', 'error'));
    }
  }
}

?>