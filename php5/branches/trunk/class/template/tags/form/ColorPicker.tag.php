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
  public $tag = 'color_picker';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'color_picker_tag';
}

registerTag(new ColorPickerTagInfo());

class ColorPickerTag extends ControlTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/color_picker_component';
  }

  public function getRenderedTag()
  {
    return 'input';
  }

  public function preGenerate($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->init_color_picker();');

    parent :: preGenerate($code);
  }

  public function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->render_color_picker();');
  }
}

?>