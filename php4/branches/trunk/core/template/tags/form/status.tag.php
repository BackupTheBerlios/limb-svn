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


class form_status_tag_info
{
  var $tag = 'form:STATUS';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'form_status_tag';
}

register_tag(new form_status_tag_info());

/**
* The parent compile time component for lists
*/
class form_status_tag extends compiler_directive_tag
{
  function check_nesting_level()
  {
    if (!$this->find_parent_by_class('form_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'enclosing_tag' => 'form',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function generate_contents(&$code)
  {
    $error_child =& $this->find_child_by_class('error_status_tag');
    $success_child =& $this->find_child_by_class('success_status_tag');

    $code->write_php('if (!' . $this->get_component_ref_code() . '->is_first_time()) {');

    $code->write_php('if (' . $this->get_component_ref_code() . '->is_valid()) {');
      if ($success_child)
        $success_child->generate($code);
    $code->write_php('}else{');
      if ($error_child)
        $error_child->generate($code);
    $code->write_php('}');

    $code->write_php('}');
  }
}

?>