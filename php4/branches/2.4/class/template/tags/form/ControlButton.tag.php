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

class ControlButtonTagInfo
{
  var $tag = 'control_button';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'control_button_tag';
}

registerTag(new ControlButtonTagInfo());

/**
* Compile time component for button tags
*/
class ControlButtonTag extends ControlTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/control_button_component';
  }

  function preParse()
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


  function getRenderedTag()
  {
    return 'button';
  }
}

?>