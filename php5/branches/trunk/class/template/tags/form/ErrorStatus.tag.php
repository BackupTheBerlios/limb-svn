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
  public $tag = 'form:ERROR_STATUS';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'error_status_tag';
}

registerTag(new FormErrorStatusTagInfo());

class ErrorStatusTag extends CompilerDirectiveTag
{
  public function checkNestingLevel()
  {
    if (!$this->findParentByClass('form_status_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'form_status',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }
}

?>