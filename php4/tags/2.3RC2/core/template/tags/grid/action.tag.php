<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
include_once(LIMB_DIR . '/core/lib/util/ini.class.php');

class grid_action_tag_info
{
  var $tag = 'grid:action';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'grid_action_tag';
}

register_tag(new grid_action_tag_info());

class grid_action_tag extends compiler_directive_tag
{
  /**
  *
  * @return void
  * @access protected
  */
  function check_nesting_level()
  {
    if (!is_a($this->parent, 'grid_actions_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('tag' => $this->tag,
          'enclosing_tag' => 'gird:actions',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function pre_parse()
  {
    $action = array();

    if(!isset($this->attributes['action']) && !isset($this->attributes['shortcut']))
    {
      error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('tag' => $this->tag,
          'attribute' => 'path or shortcut',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if(isset($this->attributes['shortcut']))
    {
      $action['action'] = get_ini_option('grid_actions.ini', $this->attributes['shortcut'], 'action');
      $action['path'] = get_ini_option('grid_actions.ini', $this->attributes['shortcut'],  'path');
      unset($this->attributes['shortcut']);
    }
    else
    {
      $action['action'] = $this->attributes['action'];

      if(isset($this->attributes['path']))
        $action['path'] = $this->attributes['locale_value'];
    }


    foreach($this->attributes as $attr_name => $attr_value)
      $action[$attr_name] = $attr_value;

    $this->parent->register_action($action);

    return PARSER_REQUIRE_PARSING;
  }
}

?>