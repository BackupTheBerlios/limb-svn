<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CanadaZipRule.class.php 1006 2005-01-10 15:48:07Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/validation/rule.inc.php');

class IdentifierRule extends SingleFieldRule
{
  //identifier should contain only letters and digits
  function check($value)
  {
    $value = "$value";

    if (!preg_match("/^[a-zA-Z0-9.-]+$/i", $value))
        $this->Error('INVALID');
  }
}
?>