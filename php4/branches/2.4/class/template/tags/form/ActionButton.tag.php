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
require_once(LIMB_DIR . '/class/template/tags/form/Button.tag.php');

class ActionButtonTagInfo
{
  var $tag = 'action_button';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'action_button_tag';
}

registerTag(new ActionButtonTagInfo());

class ActionButtonTag extends ButtonTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_submit_component';
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

  function prepare()
  {
    parent :: prepare();

    if(!isset($this->attributes['type']))
      $this->attributes['type'] = 'submit';

    $this->attributes['onclick'] = "addFormHiddenParameter(this.form, 'action', '{$this->attributes['action']}');";

    if(isset($this->attributes['reload_parent']))
    {
      $this->attributes['onclick'] .= "add_form_action_parameter(this.form, 'reload_parent', '1')";
    unset($this->attributes['reload_parent']);
    }

    unset($this->attributes['action']);
  }

  function getRenderedTag()
  {
    return 'input';
  }
}

?>