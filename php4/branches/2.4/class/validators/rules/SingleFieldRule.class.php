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
require_once(LIMB_DIR . '/class/i18n/Strings.class.php');
require_once(LIMB_DIR . '/class/validators/rules/Rule.class.php');

class SingleFieldRule extends Rule
{
  var $field_name;

  function SingleFieldRule($field_name)
  {
    $this->field_name = $field_name;
  }

  function getFieldName()
  {
    return $this->field_name;
  }

  function error($error, $params=array())
  {
    $this->is_valid = false;

    if($this->error_list)
      $this->error_list->addError($this->field_name, $error, $params);
  }

  function validate($dataspace)
  {
    $this->is_valid = true;
    $value = $dataspace->get($this->field_name);
    if (isset($value) &&  $value !== '')
    {
      $this->check($value);
    }
    return $this->is_valid;
  }

  function check($value)
  {
  }
}

?>