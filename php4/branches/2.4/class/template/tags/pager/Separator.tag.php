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
class PagerSeparatorTagInfo
{
  var $tag = 'pager:SEPARATOR';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'pager_separator_tag';
}

registerTag(new PagerSeparatorTagInfo());

/**
* Compile time component for seperators in a Pager
*/
class PagerSeparatorTag extends SilentCompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('pager_separator_tag'))
    {
      return throw(new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }
    if (!$this->findParentByClass('pager_navigator_tag'))
    {
      return throw(new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'pager:navigator',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }
  }
}

?>