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

class TextAreaTagInfo
{
  public $tag = 'textarea';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'text_area_tag';
}

registerTag(new TextAreaTagInfo());

class TextAreaTag extends ControlTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/text_area_component';
  }

  public function generateContents($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->render_contents();');
  }
}

?>