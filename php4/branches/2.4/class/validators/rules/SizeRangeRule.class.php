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

class SizeRangeRule extends SingleFieldRule
{
  var $min_len;
  var $max_len;

  function SizeRangeRule($field_name, $min_len, $max_len = null)
  {
    parent :: SingleFieldRule($field_name);

    if (is_null($max_len))
    {
      $this->min_len = null;
      $this->max_len = $min_len;
    }
    else
    {
      $this->min_len = $min_len;
      $this->max_len = $max_len;
    }
  }

  function check($value)
  {
    if (!is_null($this->min_len) &&  (strlen($value) < $this->min_len))
    {
      $this->error(Strings :: get('size_too_small', 'error'));
    }
    elseif (strlen($value) > $this->max_len)
    {
      $this->error(Strings :: get('size_too_big', 'error'));
    }
  }
}

?>