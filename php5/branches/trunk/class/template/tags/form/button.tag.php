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
require_once(LIMB_DIR . '/class/template/tags/form/control_tag.class.php');

class button_tag_info
{
  public $tag = 'button';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'button_tag';
}

register_tag(new button_tag_info());

/**
* Compile time component for button tags
*/
class button_tag extends control_tag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/button_component';
  }
}

?>