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

/**
* For fields have a minimum and maximum length
*/

class size_range_rule extends single_field_rule
{
  /**
  * Minumum length
  *
  * @var int
  * @access private
  */
  var $min_len;
  /**
  * Maximum length
  *
  * @var int
  * @access private
  */
  var $max_len;

  /**
  * Constructs size_range_rule
  *
  * @param string $ field_name to validate
  * @param int $ Minumum length
  * @param int $ Maximum length (optional)
  * @access public
  */
  function size_range_rule($field_name, $min_len, $max_len = null)
  {
    parent :: single_field_rule($field_name);

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

  /**
  * Performs validation of a single value
  *
  * @access protected
  */
  function check($value)
  {
    if (!is_null($this->min_len) && (strlen($value) < $this->min_len))
    {
      $this->error(strings :: get('size_too_small', 'error'));
    }
    elseif (strlen($value) > $this->max_len)
    {
      $this->error(strings :: get('size_too_big', 'error'));
    }
  }
}

?>