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
require_once(LIMB_DIR . '/class/i18n/strings.class.php');
require_once(LIMB_DIR . '/class/validators/rules/rule.class.php');

class single_field_rule extends rule
{
  protected $field_name;

  function __construct($field_name)
  {
    $this->field_name = $field_name;
  }

  public function get_field_name()
  {
    return $this->field_name;
  }

  protected function error($error, $params=array())
  {
    $this->is_valid = false;

    if($this->error_list)
      $this->error_list->add_error($this->field_name, $error, $params);
  }

  public function validate($dataspace)
  {
    $this->is_valid = true;
    $value = $dataspace->get($this->field_name);
    if (isset($value) && $value !== '')
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