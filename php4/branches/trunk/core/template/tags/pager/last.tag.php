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


class pager_last_tag_info
{
  var $tag = 'pager:LAST';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'pager_last_tag';
}

register_tag(new pager_last_tag_info());

/**
* Compile time component for "go to end" element of pager.
*/
class pager_last_tag extends server_component_tag
{

  var $runtime_component_path = '/core/template/component';
  /**
  * Switched to TRUE if hide_for_current_page attribute found in tag
  *
  * @var boolean
  * @access private
  */
  var $hide_for_current_page;
  /**
  *
  * @return void
  * @access private
  */
  function check_nesting_level()
  {
    if ($this->find_parent_by_class('pager_last_tag'))
    {
      error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    if (!$this->find_parent_by_class('pager_navigator_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'enclosing_tag' => 'pager:navigator',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }
  /**
  *
  * @param code $ _writer
  * @return void
  * @access protected
  */
  function pre_generate(&$code)
  {
    $this->hide_for_current_page = array_key_exists('hide_for_current_page', $this->attributes);

    $parent = &$this->find_parent_by_class('pager_navigator_tag');
    $code->write_php('if (!' . $parent->get_component_ref_code() . '->is_last()) {');

    parent::pre_generate($code);

    $code->write_php($this->get_component_ref_code() . '->set("href", ' . $parent->get_component_ref_code() . '->get_last_page_uri());');

    if (!$this->hide_for_current_page)
    {
      $code->write_php('}');
    }
  }

  function generate_contents(&$code)
  {
    $parent = &$this->find_parent_by_class('pager_navigator_tag');

    $code->write_php('if (!' . $parent->get_component_ref_code() . '->is_last()) {');

    parent :: generate_contents($code);

    $code->write_php('}');
  }

  /**
  *
  * @param code $ _writer
  * @return void
  * @access protected
  */
  function post_generate(&$code)
  {
    if (!$this->hide_for_current_page)
    {
      $parent = &$this->find_parent_by_class('pager_navigator_tag');
      $code->write_php('if (!' . $parent->get_component_ref_code() . '->is_last()) {');
    }
    parent::post_generate($code);

    $code->write_php('}');
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