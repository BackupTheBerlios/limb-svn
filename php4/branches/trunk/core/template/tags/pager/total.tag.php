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

class pager_total_count_tag_info
{
  var $tag = 'pager:TOTAL';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'pager_total_count_tag';
}

register_tag(new pager_total_count_tag_info());

/**
* Compile time component for seperators in a Pager
*/
class pager_total_count_tag extends server_component_tag
{
  var $runtime_component_path = '/core/template/component';
  /**
  *
  * @return void
  * @access private
  */
  function check_nesting_level()
  {
    if (!$this->find_parent_by_class('pager_navigator_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'enclosing_tag' => 'pager:navigator',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function pre_generate(&$code)
  {
    $parent = &$this->find_parent_by_class('pager_navigator_tag');
    parent::pre_generate($code);

    $code->write_php($this->get_component_ref_code() . '->set("number", ' . $parent->get_component_ref_code() . '->get_total_items());');
    $code->write_php($this->get_component_ref_code() . '->set("pages_count", ' . $parent->get_component_ref_code() . '->get_pages_count());');
    $code->write_php($this->get_component_ref_code() . '->set("more_than_one_page", ' . $parent->get_component_ref_code() . '->has_more_than_one_page());');
  }

  function &get_dataspace()
  {
    return $this;
  }

  function get_dataspace_ref_code()
  {
    return $this->get_component_ref_code();
  }
}

?>