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
class action_tag_info
{
  var $tag = 'actions:ITEM';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'action_tag';
}

register_tag(new action_tag_info());

/**
* Compile time component for items (rows) in the list
*/
class action_tag extends compiler_directive_tag
{
  /**
  *
  * @return void
  * @access protected
  */
  function check_nesting_level()
  {
    if (!is_a($this->parent, 'actions_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('tag' => $this->tag,
          'enclosing_tag' => 'actions',
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
  function generate_contents(&$code)
  {
    $code->write_php('do { ');

    parent::generate_contents($code);

    $code->write_php('} while (' . $this->get_dataspace_ref_code() . '->next());');
  }
}

?>