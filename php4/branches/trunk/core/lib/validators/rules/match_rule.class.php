<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/validators/rules/single_field_rule.class.php');

class match_rule extends single_field_rule
{
  var $match_field;

  var $match_field_name;

  function match_rule($field_name, $match_field, $match_field_name = '')
  {
    $this->match_field = $match_field;
    if (!$match_field_name)
      $this->match_field_name = $match_field;
    else
      $this->match_field_name = $match_field_name;

    parent :: single_field_rule($field_name);
  }

  function validate(&$dataspace)
  {
    $value1 = $dataspace->get($this->field_name);
    $value2 = $dataspace->get($this->match_field);

    if (isset($value1) && isset($value2))
    {
      if (strcmp($value1, $value2))
      {
        $this->error(strings :: get('error_no_match', 'error'), array('match_field' => $this->match_field_name));
      }
    }
  }
}
?>