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
require_once(LIMB_DIR . '/class/validators/rules/single_field_rule.class.php');

class canada_zip_rule extends single_field_rule
{
  //The Canadian postal code is LNL NLN
  //where N=number and L=letter
  protected function check($value)
  {
    $value = "$value";

    if(!preg_match("~^[a-zA-Z]\d[a-zA-Z]\s\d[a-zA-Z]\d$~", $value))
      $this->error(strings :: get('error_invalid_zip_format', 'error'));
  }
}
?>