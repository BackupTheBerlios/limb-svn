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
class GridCounterTagInfo
{
  public $tag = 'grid:COUNTER';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'grid_counter_tag';
}

registerTag(new GridCounterTagInfo());

class GridCounterTag extends CompilerDirectiveTag
{
  public function checkNestingLevel()
  {
    if (!$this->parent instanceof GridIteratorTag)
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:ITERATOR',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function generateContents($code)
  {
    $grid = $this->findParentByClass('grid_list_tag');

    $code->writePhp('echo ' . $grid->getComponentRefCode() . '->get_counter();');
  }
}

?>