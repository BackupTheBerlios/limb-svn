<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: action.tag.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
include_once(LIMB_DIR . '/core/lib/util/ini.class.php');

class admin_list_action_tag_info
{
  var $tag = 'admin:list:action';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'admin_list_action_tag';
}

register_tag(new  admin_list_action_tag_info());

class  admin_list_action_tag extends compiler_directive_tag
{
  function check_nesting_level()
  {
    if (!is_a($this->parent, 'admin_list_actions_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('tag' => $this->tag,
          'enclosing_tag' => ' admin:list:actions',
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