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

class InvalidValueRule extends SingleFieldRule
{
  var $invalid_value;

  function InvalidValueRule($field_name, $invalid_value)
  {
    $this->invalid_value = $invalid_value;

    parent :: SingleFieldRule($field_name);
  }

  function check($value)
  {
    $invalid_value = $this->invalid_value;

    settype($invalid_value, 'string');//???

    if ($value == $invalid_value)
    {
      $this->error('ERROR_INVALID_VALUE');
    }
  }
}

?>