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

class PollResultTagInfo
{
  public $tag = 'poll:RESULT';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'poll_result_tag';
}

registerTag(new PollResultTagInfo());

class PollResultTag extends CompilerDirectiveTag
{
}

?>