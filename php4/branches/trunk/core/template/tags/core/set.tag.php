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


class core_set_tag_info
{
  var $tag = 'core:SET';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'core_set_tag';
}

register_tag(new core_set_tag_info());

/**
* Sets a variable in the runtime dataspace, according the attributes of this
* tag at compile time.
*/
class core_set_tag extends silent_compiler_directive_tag
{
  /**
  *
  * @return void
  * @access protected
  */
  function check_nesting_level()
  {
    if ($this->find_parent_by_class('core_set_tag'))
    {
      error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  /**
  *
  * @return int PARSER_FORBID_PARSING
  * @access protected
  */
  function pre_parse()
  {
    $dataspace = &$this->get_dataspace();
    $dataspace->vars += $this->attributes;
    return PARSER_FORBID_PARSING;
  }
}

?>