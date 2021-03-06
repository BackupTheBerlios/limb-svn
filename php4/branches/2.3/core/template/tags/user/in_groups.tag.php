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


class user_in_groups_tag_info
{
  var $tag = 'user:IN_GROUPS';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'user_in_groups_tag';
}

register_tag(new user_in_groups_tag_info());

class user_in_groups_tag extends compiler_directive_tag
{
  function pre_parse()
  {
    $groups = $this->attributes['groups'];
    if (empty($groups))
    {
      error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'attribute' => 'groups',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function generate_contents(&$code)
  {
    $groups = $this->attributes['groups'];

    $user = '$' . $code->get_temp_variable();
    $code->write_php("{$user} =& user :: instance();");

    $code->write_php("if ({$user}->is_logged_in() && ({$user}->is_in_groups('{$groups}'))) {");
      parent :: generate_contents($code);
    $code->write_php("}");
  }
}

?>