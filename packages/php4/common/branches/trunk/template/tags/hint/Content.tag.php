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
class HintContentTagInfo
{
  public $tag = 'hint:CONTENT';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'hint_content_tag';
}

registerTag(new HintContentTagInfo());

class HintContentTag extends CompilerDirectiveTag
{
}

?>