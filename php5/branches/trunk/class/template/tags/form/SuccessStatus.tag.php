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
class FormSuccessStatusTagInfo
{
  public $tag = 'form:SUCCESS_STATUS';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'success_status_tag';
}

registerTag(new FormSuccessStatusTagInfo());

class SuccessStatusTag extends CompilerDirectiveTag
{
  public function checkNestingLevel()
  {
    if (!$this->findParentByClass('form_status_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }
}

?>