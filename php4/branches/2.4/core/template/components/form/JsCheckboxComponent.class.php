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
require_once(LIMB_DIR . '/core/template/components/form/InputFormElement.class.php');

class JsCheckboxComponent extends InputFormElement
{
  function renderAttributes()
  {
    unset($this->attributes['value']);
    parent :: renderAttributes();
  }

  function renderJsCheckbox()
  {
    $id = $this->getAttribute('id');
    $name = $this->getAttribute('name');

    if ($this->getAttribute('value'))
      $checked = 'checked=\'on\'';
    else
      $checked = '';

    $name = $this->_processNameAttribute($name);
    $js = "onclick=\"this.form.elements['{$name}'].value = 1*this.checked\"";

    echo "<input type='checkbox' id='{$id}_checkbox' {$checked} {$js}>";

  }

}
?>