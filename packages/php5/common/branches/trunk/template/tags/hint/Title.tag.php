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
class hint_title_tag_info
{
  public $tag = 'hint:TITLE';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'hint_title_tag';
}

register_tag(new hint_title_tag_info());

class hint_title_tag extends compiler_directive_tag
{
}

?>