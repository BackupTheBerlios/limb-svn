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
require_once(LIMB_DIR . '/core/lib/validators/rules/domain_rule.class.php');

/**
* check for a valid domain name with a valid DNS Record.
* If DNS is down, data will not be considered invalid,
* possibly preventing data entry when connectivity is bad.
*/
class dns_domain_rule extends domain_rule
{
  function dns_domain_rule($fieldname)
  {
    parent :: domain_rule($fieldname);
  } 

  /**
  * Performs validation of a single value
  * 
  * @access protected 
  */
  function check($value)
  {
    parent::check($value);
    if ($this->is_valid())
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