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
class GridItemTagInfo
{
  var $tag = 'grid:ITEM';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'grid_item_tag';
}

registerTag(new GridItemTagInfo());

class GridItemTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if (!is_a($this->parent, 'GridListTag'))
    {
      return throw(new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:LIST',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }
  }
}

?>