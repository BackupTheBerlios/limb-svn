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

class control_button_tag_info
{
  public $tag = 'control_button';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'control_button_tag';
}

register_tag(new control_button_tag_info());

/**
* Compile time component for button tags
*/
class control_button_tag extends control_tag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/control_button_component';
  }

  public function pre_parse()
  {
    if (!isset($this->attributes['action']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'action',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }


  public function get_rendered_tag()
  {
    return 'button';
  }
}

?>