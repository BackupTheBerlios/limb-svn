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
require_once(WACT_ROOT . '/validation/rule.inc.php');

class UsZipRule extends SingleFieldRule
{
  function check($value)
  {
    $value = "$value";

    if(strlen($value) == 5)
    {
      if(!preg_match("~^\d{5}$~", $value))
        $this->error('ERROR_INVALID_ZIP_FORMAT');
      else
        return;
    }
    elseif(strlen($value) == 10)
    {
      if(!preg_match("~^\d{5}\s\d{4}$~", $value))
        $this->error('ERROR_INVALID_ZIP_FORMAT');
      else
        return;
    }
    else
      $this->error('ERROR_INVALID_ZIP_FORMAT');
  }
}
?>