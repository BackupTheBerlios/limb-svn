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
  var $tag = 'grid:SEPARATOR';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'grid_separator_tag';
}

registerTag(new GridSeparatorTagInfo());

class GridSeparatorTag extends CompilerDirectiveTag
{
  var $count;

  function checkNestingLevel()
  {
    if ($this->findParentByClass('grid_separator_tag'))
    {
      return throw(new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }

    if (!is_a($this->parent, 'GridIteratorTag'))
    {
      return throw(new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:ITERATOR',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }
  }

  function preParse()
  {
    if (!isset($this->attributes['count']))
      $this->count = 1;
    else
      $this->count = $this->attributes['count'];

    return PARSER_REQUIRE_PARSING;
  }

  function preGenerate($code)
  {
    parent::preGenerate($code);

    $counter = '$' . $code->getTempVariable();

    $code->writePhp($counter . ' = trim(' . $this->getDataspaceRefCode() . '->get_counter());');

    $code->writePhp(
        "if (	($counter > 0) &&
              ($counter < " . $this->getDataspaceRefCode() . "->get_total_row_count()) &&
              ($counter % " . $this->count . " == 0)) {");
  }

  function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>