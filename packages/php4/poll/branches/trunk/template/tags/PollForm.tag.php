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

class PollFormTagInfo
{
  var $tag = 'poll:FORM';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'poll_form_tag';
}

registerTag(new PollFormTagInfo());

class PollFormTag extends CompilerDirectiveTag
{
}

?>