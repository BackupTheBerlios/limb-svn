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
  var $tag = 'textarea';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'text_area_tag';
}

registerTag(new TextAreaTagInfo());

class TextAreaTag extends ControlTag
{
  function TextAreaTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/text_area_component';
  }

  function generateContents($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->render_contents();');
  }
}

?>