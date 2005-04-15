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
class grid_separator_tag_info
{
  var $tag = 'grid:SEPARATOR';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'grid_separator_tag';
}

register_tag(new grid_separator_tag_info());

class grid_separator_tag extends compiler_directive_tag
{
  var $count;

  /**
  *
  * @return void
  * @access private
  */
  function check_nesting_level()
  {
    if ($this->find_parent_by_class('grid_separator_tag'))
    {
      error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    if (!is_a($this->parent, 'grid_iterator_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'enclosing_tag' => 'grid:ITERATOR',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function pre_parse()
  {
    if (!isset($this->attributes['count']))
      $this->count = 1;
    else
      $this->count = $this->attributes['count'];

    return PARSER_REQUIRE_PARSING;
  }

  function pre_generate(&$code)
  {
    parent::pre_generate($code);

    $counter = '$' . $code->get_temp_variable();

    $code->write_php($counter . ' = trim(' . $this->get_dataspace_ref_code() . '->get_counter());');

    $code->write_php(
        "if (	($counter > 0) &&
              ($counter < " . $this->get_dataspace_ref_code() . "->get_total_row_count()) &&
              ($counter % " . $this->count . " == 0)) {");
  }

  function post_generate(&$code)
  {
    $code->write_php('}');
    parent::post_generate($code);
  }
}

?>