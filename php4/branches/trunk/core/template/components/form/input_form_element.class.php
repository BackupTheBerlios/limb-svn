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

require_once(LIMB_DIR . '/core/template/components/form/form_element.class.php');

class input_form_element extends form_element
{
  /**
  * Overrides then calls with the parent render_attributes() method. Makes
  * sure there is always a value attribute, even if it's empty.
  * Called from within a compiled template render function.
  *
  * @return void
  * @access protected
  */
  function render_attributes()
  {
    $value = $this->get_value();

    if (!is_null($value))
    {
      $this->attributes['value'] = $value;
    }

    parent :: render_attributes();
  }
}
?>