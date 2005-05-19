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
class search_left_mark_tag_info
{
  var $tag = 'search:LEFT_MARK';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'search_left_mark_tag';
}

register_tag(new search_left_mark_tag_info());

class search_left_mark_tag extends compiler_directive_tag
{
  function check_nesting_level()
  {
    if ($this->find_parent_by_class('search_left_mark_tag'))
    {
      error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    if (!is_a($this->parent, 'search_datasource_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'enclosing_tag' => 'search:DATASOURCE',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function pre_parse()
  {
    return PARSER_FORBID_PARSING;
  }

  function generate_contents(&$code)
  {
    $temp = '$' . $code->get_temp_variable();

    $code->write_php('ob_start();');

    parent::generate_contents($code);

    $code->write_php($temp . ' = ob_get_contents();');

    $code->write_php('ob_end_clean();');

    $code->write_php($this->parent->get_component_ref_code().
                     '->set_left_mark("' . $temp . '");');
  }
}

?>