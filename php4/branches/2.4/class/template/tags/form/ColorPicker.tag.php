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
require_once(LIMB_DIR . '/class/template/tags/form/ControlTag.class.php');

class ColorPickerTagInfo
{
  var $tag = 'color_picker';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'color_picker_tag';
}

registerTag(new ColorPickerTagInfo());

class ColorPickerTag extends ControlTag
{
  function ColorPickerTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/color_picker_component';
  }

  function getRenderedTag()
  {
    return 'input';
  }

  function preGenerate($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->init_color_picker();');

    parent :: preGenerate($code);
  }

  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->render_color_picker();');
  }
}

?>