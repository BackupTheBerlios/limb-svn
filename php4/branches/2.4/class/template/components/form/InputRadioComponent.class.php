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

class InputRadioComponent extends FormElement
{
  /**
  * Overrides then calls with the parent render_attributes() method dealing
  * with the special case of the checked attribute
  */
  public function renderAttributes()
  {
    $value = $this->getValue();

    if (isset($this->attributes['value']) &&  $value == $this->attributes['value'])
    {
      $this->attributes['checked'] = 1;
    }
    else
    {
      unset($this->attributes['checked']);
    }
    parent::renderAttributes();
  }
}
?>