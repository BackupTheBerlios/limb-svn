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

class CanadaZipRule extends SingleFieldRule
{
  //The Canadian postal code is LNL NLN
  //where N=number and L=letter
  function check($value)
  {
    $value = "$value";

    if(!preg_match("~^[a-zA-Z]\d[a-zA-Z]\s\d[a-zA-Z]\d$~", $value))
      $this->error(Strings :: get('error_invalid_zip_format', 'error'));
  }
}
?>