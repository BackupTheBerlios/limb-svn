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
  protected $field_name;

  function __construct($field_name)
  {
    $this->field_name = $field_name;
  }

  public function getFieldName()
  {
    return $this->field_name;
  }

  protected function error($error, $params=array())
  {
    $this->is_valid = false;

    if($this->error_list)
      $this->error_list->addError($this->field_name, $error, $params);
  }

  public function validate($dataspace)
  {
    $this->is_valid = true;
    $value = $dataspace->get($this->field_name);
    if (isset($value) &&  $value !== '')
    {
      $this->check($value);
    }
    return $this->is_valid;
  }

  protected function check($value)
  {
  }
}

?>