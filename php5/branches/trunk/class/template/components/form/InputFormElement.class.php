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

abstract class InputFormElement extends FormElement
{
  /**
  * Overrides then calls with the parent render_attributes() method. Makes
  * sure there is always a value attribute, even if it's empty.
  * Called from within a compiled template render function.
  */
  public function renderAttributes()
  {
    $value = $this->getValue();

    if (!is_null($value))
    {
      $this->attributes['value'] = $value;
    }

    parent :: renderAttributes();
  }
}
?>