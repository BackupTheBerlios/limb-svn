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
  public $tag = 'grid:ITEM';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'grid_item_tag';
}

registerTag(new GridItemTagInfo());

class GridItemTag extends CompilerDirectiveTag
{
  public function checkNestingLevel()
  {
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