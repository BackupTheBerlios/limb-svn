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
class GridDefaultTagInfo
{
  var $tag = 'grid:DEFAULT';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'grid_default_tag';
}

registerTag(new GridDefaultTagInfo());

class GridDefaultTag extends SilentCompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('grid_default_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    if (!$this->parent instanceof GridListTag)
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:LIST',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }
}

?>