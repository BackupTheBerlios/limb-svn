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

class JsCheckboxTagInfo
{
  public $tag = 'js_checkbox';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'js_checkbox_tag';
}

registerTag(new JsCheckboxTagInfo());

class JsCheckboxTag extends ControlTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/js_checkbox_component';
  }

  public function getRenderedTag()
  {
    return 'input';
  }

  public function preGenerate($code)
  {
    $this->attributes['type'] = 'hidden';

    parent :: preGenerate($code);
  }

  public function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->render_js_checkbox();');
  }
}

?>