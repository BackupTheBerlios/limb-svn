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

class ButtonTagInfo
{
  var $tag = 'button';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'button_tag';
}

registerTag(new ButtonTagInfo());

/**
* Compile time component for button tags
*/
class ButtonTag extends ControlTag
{
  function ButtonTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/button_component';
  }
}

?>