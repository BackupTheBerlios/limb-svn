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
class grid_item_tag_info
{
  public $tag = 'grid:ITEM';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'grid_item_tag';
}

register_tag(new grid_item_tag_info());

class grid_item_tag extends compiler_directive_tag
{
  public function check_nesting_level()
  {
    if (!$this->parent instanceof grid_list_tag)
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