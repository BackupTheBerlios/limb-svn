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

class InvalidValueRule extends SingleFieldRule
{
  var $invalid_value;

  function InvalidValueRule($field_name, $invalid_value)
  {
    parent :: SingleFieldRule($field_name);

    $this->invalid_value = $invalid_value;
  }

  function validate($dataspace)
  {
    $value = $dataspace->get($this->field_name);

    $invalid_value = $this->invalid_value;

    settype($invalid_value, 'string');//???

    if ($value == $invalid_value)
    {
      $this->error(Strings :: get('error_invalid_value', 'error'));
    }
  }
}

?>