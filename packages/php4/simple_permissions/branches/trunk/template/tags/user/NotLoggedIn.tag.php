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
class UserNotLoggedInTagInfo
{
  public $tag = 'user:NOT_LOGGED_IN';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'user_not_logged_in_tag';
}

registerTag(new UserNotLoggedInTagInfo());

class UserNotLoggedInTag extends CompilerDirectiveTag
{
  function generateContents($code)
  {
    $code->writePhp("\$toolkit =& Limb :: toolkit();\$user =& \$toolkit->getUser();");
    $code->writePhp("if (!\$user->is_logged_in()) {");
      parent :: generateContents($code);
    $code->writePhp("}");
  }
}

?>