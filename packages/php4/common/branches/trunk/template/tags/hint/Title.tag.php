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
  var $tag = 'hint:TITLE';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'hint_title_tag';
}

registerTag(new HintTitleTagInfo());

class HintTitleTag extends CompilerDirectiveTag
{
}

?>