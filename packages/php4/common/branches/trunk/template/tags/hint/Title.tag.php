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
class HintTitleTagInfo
{
  public $tag = 'hint:TITLE';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'hint_title_tag';
}

registerTag(new HintTitleTagInfo());

class HintTitleTag extends CompilerDirectiveTag
{
}

?>