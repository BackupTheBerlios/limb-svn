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
require_once(LIMB_DIR . '/class/template/components/form/FormElement.class.php');

class InputCheckboxComponent extends FormElement
{
  /**
  * Overrides then calls with the parent render_attributes() method dealing
  * with the special case of the checked attribute
  */
  function renderAttributes()
  {
    $value = $this->getValue();

    if ($value)
      $this->attributes['checked'] = 1;
    else
    unset($this->attributes['checked']);

    parent :: renderAttributes();
  }
}
?>