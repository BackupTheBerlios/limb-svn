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
require_once(LIMB_DIR . '/class/template/components/form/InputFormElement.class.php');

class ColorPickerComponent extends InputFormElement
{
  function initColorPicker()
  {
    if (defined('COLOR_PICKER_LOAD_SCRIPT'))
      return;

    echo "<script type='text/javascript' src='/shared/js/color_picker.js'></script>";

    $this->setAttribute('onChange', "relateColor(this.id, this.value)");
    if(!$this->getAttribute('size'))
      $this->setAttribute('size', "10");

    define('COLOR_PICKER_LOAD_SCRIPT',1);
  }

  function renderColorPicker()
  {
    $id = $this->getAttribute('id');

    echo "&nbsp;<a href=\"javascript:pickColor('{$id}');\" id=\"{$id}_picker\"
          style=\"border: 1px solid #000000; font-family:Verdana; font-size:10px;
          text-decoration: none;\">&nbsp;&nbsp;&nbsp;</a>
          <script language=\"javascript\">relateColor('{$id}', getObj('{$id}').value);</script>";

  }

}
?>