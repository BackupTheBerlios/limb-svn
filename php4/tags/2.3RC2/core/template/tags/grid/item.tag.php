<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/


class grid_item_tag_info
{
  var $tag = 'grid:ITEM';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'grid_item_tag';
}

register_tag(new grid_item_tag_info());

class grid_item_tag extends compiler_directive_tag
{
  function check_nesting_level()
  {
    if (!is_a($this->parent, 'grid_list_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'enclosing_tag' => 'grid:ITEM',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }
}

?>