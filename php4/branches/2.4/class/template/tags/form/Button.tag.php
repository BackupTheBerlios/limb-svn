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
  public $tag = 'button';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'button_tag';
}

registerTag(new ButtonTagInfo());

/**
* Compile time component for button tags
*/
class ButtonTag extends ControlTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/button_component';
  }
}

?>