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
require_once(LIMB_DIR . '/class/validators/rules/SingleFieldRule.class.php');

class UsZipRule extends SingleFieldRule
{
  protected function check($value)
  {
    $value = "$value";

    if(strlen($value) == 5)
    {
      if(!preg_match("~^\d{5}$~", $value))
        $this->error(Strings :: get('error_invalid_zip_format', 'error'));
      else
        return;
    }
    elseif(strlen($value) == 10)
    {
      if(!preg_match("~^\d{5}\s\d{4}$~", $value))
        $this->error(Strings :: get('error_invalid_zip_format', 'error'));
      else
        return;
    }
    else
      $this->error(Strings :: get('error_invalid_zip_format', 'error'));
  }
}
?>