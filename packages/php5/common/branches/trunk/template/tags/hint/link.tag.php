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
class hint_link_tag_info
{
  public $tag = 'hint:LINK';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'hint_link_tag';
}

register_tag(new hint_link_tag_info());

class hint_link_tag extends compiler_directive_tag
{
}

?>