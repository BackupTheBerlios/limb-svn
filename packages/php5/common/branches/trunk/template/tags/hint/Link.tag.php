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
class HintLinkTagInfo
{
  public $tag = 'hint:LINK';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'hint_link_tag';
}

registerTag(new HintLinkTagInfo());

class HintLinkTag extends CompilerDirectiveTag
{
}

?>