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
class GridSeparatorTagInfo
{
  public $tag = 'grid:SEPARATOR';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'grid_separator_tag';
}

registerTag(new GridSeparatorTagInfo());

class GridSeparatorTag extends CompilerDirectiveTag
{
  protected $count;

  public function checkNestingLevel()
  {
    if ($this->findParentByClass('grid_separator_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (!$this->parent instanceof GridIteratorTag)
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:ITERATOR',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function preParse()
  {
    if (!isset($this->attributes['count']))
      $this->count = 1;
    else
      $this->count = $this->attributes['count'];

    return PARSER_REQUIRE_PARSING;
  }

  public function preGenerate($code)
  {
    parent::preGenerate($code);

    $counter = '$' . $code->getTempVariable();

    $code->writePhp($counter . ' = trim(' . $this->getDataspaceRefCode() . '->get_counter());');

    $code->writePhp(
        "if (	($counter > 0) &&
              ($counter < " . $this->getDataspaceRefCode() . "->get_total_row_count()) &&
              ($counter % " . $this->count . " == 0)) {");
  }

  public function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>