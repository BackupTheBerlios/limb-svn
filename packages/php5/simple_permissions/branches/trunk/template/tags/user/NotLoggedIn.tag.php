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
class user_not_logged_in_tag_info
{
  public $tag = 'user:NOT_LOGGED_IN';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'user_not_logged_in_tag';
}

register_tag(new user_not_logged_in_tag_info());

class user_not_logged_in_tag extends compiler_directive_tag
{
  public function generate_contents($code)
  {
    $code->write_php("if (!Limb :: toolkit()->getUser()->is_logged_in()) {");
      parent :: generate_contents($code);
    $code->write_php("}");
  }
}

?>