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
class FormErrorStatusTagInfo
{
  var $tag = 'form:ERROR_STATUS';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'error_status_tag';
}

registerTag(new FormErrorStatusTagInfo());

class ErrorStatusTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if (!$this->findParentByClass('form_status_tag'))
    {
      return new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'form_status',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }
}

?>