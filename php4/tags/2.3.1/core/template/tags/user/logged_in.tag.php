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


class user_logged_in_tag_info
{
  var $tag = 'user:LOGGED_IN';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'user_logged_in_tag';
}

register_tag(new user_logged_in_tag_info());

class user_logged_in_tag extends compiler_directive_tag
{
  function generate_contents(&$code)
  {
    $user = '$' . $code->get_temp_variable();
    $code->write_php("{$user} =& user :: instance();");

    $code->write_php("if ({$user}->is_logged_in()) {");
      parent :: generate_contents($code);
    $code->write_php("}");
  }
}

?>