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
  var $tag = 'hint:CONTENT';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'hint_content_tag';
}

registerTag(new HintContentTagInfo());

class HintContentTag extends CompilerDirectiveTag
{
}

?>